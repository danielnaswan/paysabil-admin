<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors.
     */
    public function index(): View
    {
        $vendors = Vendor::with(['user', 'services'])
            ->withCount(['services', 'ratings'])
            ->get();

        return view('pages.vendor.vendor', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create(): View
    {
        return view('pages.vendor.create-vendor');
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateVendorData($request);

        DB::beginTransaction();

        try {
            $profilePictureUrl = $this->handleProfilePicture($request);

            $user = $this->createUser($validated, $profilePictureUrl);
            $vendor = $this->createVendor($validated, $user->id);

            DB::commit();

            return redirect()
                ->route('vendor.show', $vendor->id)
                ->with('success', 'Vendor registered successfully! You can now add menu items.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Vendor creation failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create vendor. Please try again.']);
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor): View
    {
        $vendor->load([
            'user',
            'services' => function($query) {
                $query->orderBy('is_available', 'desc')
                      ->orderBy('name');
            },
            'ratings' => function($query) {
                $query->with('student')
                      ->latest()
                      ->take(5);
            }
        ]);

        // Get vendor statistics
        $statistics = $this->getVendorStatistics($vendor);

        return view('pages.vendor.show-vendor', compact('vendor', 'statistics'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor): View
    {
        $vendor->load('user');
        return view('pages.vendor.edit-vendor', compact('vendor'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $this->validateVendorData($request, $vendor);

        DB::beginTransaction();

        try {
            $this->updateUser($vendor->user, $validated, $request);
            $this->updateVendor($vendor, $validated);

            // Update vendor ratings if there are reviews
            $this->updateVendorRatings($vendor);

            DB::commit();

            return redirect()
                ->route('vendor.index')
                ->with('success', 'Vendor updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Vendor update failed: ' . $e->getMessage(), [
                'vendor_id' => $vendor->id,
                'request_data' => $request->except(['password', 'password_confirmation']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update vendor. Please try again.']);
        }
    }

    /**
     * Remove the specified vendor.
     */
    public function destroy(Vendor $vendor): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Check if vendor has active services or recent transactions
            if ($this->hasActiveBusinessActivity($vendor)) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => 'Cannot delete vendor with active services or recent transactions.']);
            }

            // Delete profile picture if exists
            if ($vendor->user->profile_picture_url) {
                $this->deleteProfilePicture($vendor->user->profile_picture_url);
            }

            // Soft delete vendor and user
            $vendor->delete();
            $vendor->user->delete();

            DB::commit();

            return redirect()
                ->route('vendor.index')
                ->with('success', 'Vendor deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Vendor deletion failed: ' . $e->getMessage(), [
                'vendor_id' => $vendor->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete vendor. Please try again.']);
        }
    }

    /**
     * Validate vendor data for store/update operations.
     */
    private function validateVendorData(Request $request, ?Vendor $vendor = null): array
    {
        $rules = [
            'business_name' => [
                'required', 
                'string', 
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\&\.]+$/'
            ],
            'service_category' => [
                'required', 
                'string', 
                'max:50',
                Rule::in(['Food & Beverage', 'Restaurant', 'Cafe', 'Fast Food', 'Catering', 'Other'])
            ],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($vendor?->user_id)
            ],
            'phone_number' => [
                'required', 
                'string', 
                'max:15', 
                'regex:/^[0-9\-\+\s\(\)]+$/'
            ],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ];

        // Add password validation only for store operation
        if (!$vendor) {
            $rules['password'] = ['required', 'string', 'min:5', 'confirmed'];
        }

        return $request->validate($rules, [
            'business_name.regex' => 'Business name can only contain letters, numbers, spaces, hyphens, ampersands, and periods.',
            'service_category.in' => 'Please select a valid service category.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'experience_years.max' => 'Experience years cannot exceed 50 years.',
            'profile_picture.max' => 'Profile picture must not exceed 2MB.',
        ]);
    }

    /**
     * Handle profile picture upload.
     */
    private function handleProfilePicture(Request $request): ?string
    {
        if (!$request->hasFile('profile_picture')) {
            return null;
        }

        $file = $request->file('profile_picture');
        $fileName = 'vendor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('public/profile_pictures', $fileName);

        return Storage::url($filePath);
    }

    /**
     * Create new user record.
     */
    private function createUser(array $validated, ?string $profilePictureUrl): User
    {
        return User::create([
            'name' => $validated['business_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'profile_picture_url' => $profilePictureUrl,
            'role' => UserRole::VENDOR
        ]);
    }

    /**
     * Create new vendor record.
     */
    private function createVendor(array $validated, int $userId): Vendor
    {
        return Vendor::create([
            'business_name' => $validated['business_name'],
            'service_category' => $validated['service_category'],
            'experience_years' => $validated['experience_years'],
            'average_rating' => 0.00,
            'total_reviews' => 0,
            'user_id' => $userId
        ]);
    }

    /**
     * Update user record.
     */
    private function updateUser(User $user, array $validated, Request $request): void
    {
        $updateData = [
            'name' => $validated['business_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ];

        // Handle profile picture update
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($user->profile_picture_url) {
                $this->deleteProfilePicture($user->profile_picture_url);
            }

            $updateData['profile_picture_url'] = $this->handleProfilePicture($request);
        }

        $user->update($updateData);
    }

    /**
     * Update vendor record.
     */
    private function updateVendor(Vendor $vendor, array $validated): void
    {
        $vendor->update([
            'business_name' => $validated['business_name'],
            'service_category' => $validated['service_category'],
            'experience_years' => $validated['experience_years'],
        ]);
    }

    /**
     * Update vendor rating statistics.
     */
    private function updateVendorRatings(Vendor $vendor): void
    {
        $ratings = $vendor->ratings();
        
        $vendor->update([
            'average_rating' => $ratings->avg('stars') ?? 0.00,
            'total_reviews' => $ratings->count()
        ]);
    }

    /**
     * Check if vendor has active business activity.
     */
    private function hasActiveBusinessActivity(Vendor $vendor): bool
    {
        // Check for active services
        $hasActiveServices = $vendor->services()->where('is_available', true)->exists();
        
        // Check for recent transactions (within last 30 days)
        $hasRecentTransactions = $vendor->services()
            ->whereHas('transactions', function($query) {
                $query->where('transaction_date', '>=', now()->subDays(30));
            })
            ->exists();

        return $hasActiveServices || $hasRecentTransactions;
    }

    /**
     * Get vendor statistics for display.
     */
    private function getVendorStatistics(Vendor $vendor): array
    {
        return [
            'total_services' => $vendor->services()->count(),
            'active_services' => $vendor->services()->where('is_available', true)->count(),
            'total_transactions' => $vendor->services()
                ->withCount('transactions')
                ->get()
                ->sum('transactions_count'),
            'monthly_transactions' => $vendor->services()
                ->whereHas('transactions', function($query) {
                    $query->whereMonth('transaction_date', now()->month)
                          ->whereYear('transaction_date', now()->year);
                })
                ->withCount('transactions')
                ->get()
                ->sum('transactions_count'),
            'total_revenue' => $vendor->services()
                ->with('transactions')
                ->get()
                ->flatMap->transactions
                ->where('status', 'COMPLETED')
                ->sum('amount'),
            'monthly_revenue' => $vendor->services()
                ->with(['transactions' => function($query) {
                    $query->where('transactions.status', 'COMPLETED')
                          ->whereMonth('transaction_date', now()->month)
                          ->whereYear('transaction_date', now()->year);
                }])
                ->get()
                ->flatMap->transactions
                ->sum('amount'),
            'average_rating' => round($vendor->average_rating, 2),
            'total_ratings' => $vendor->total_reviews,
            'recent_ratings' => $vendor->ratings()
                ->where('review_date', '>=', now()->subDays(30))
                ->count()
        ];
    }

    /**
     * Delete profile picture file.
     */
    private function deleteProfilePicture(string $profilePictureUrl): bool
    {
        $path = str_replace('/storage', 'public', $profilePictureUrl);
        return Storage::delete($path);
    }

    /**
     * Toggle vendor service availability (AJAX endpoint).
     */
    public function toggleServiceAvailability(Request $request, Vendor $vendor): \Illuminate\Http\JsonResponse
    {
        try {
            $serviceId = $request->input('service_id');
            $service = $vendor->services()->findOrFail($serviceId);
            
            $service->toggleAvailability();
            
            return response()->json([
                'success' => true,
                'is_available' => $service->is_available,
                'message' => 'Service availability updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Service availability toggle failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service availability.'
            ], 500);
        }
    }
}