<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VendorProfileController extends Controller
{
    /**
     * Display vendor profile.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $vendor->load(['user', 'services', 'ratings.student.user']);

        // Calculate business statistics in the controller to avoid SQL issues
        $businessStats = $this->getBusinessStats($vendor);

        return view('vendor.profile.index', compact('vendor', 'businessStats'));
    }

    /**
     * Show the form for editing vendor profile.
     */
    public function edit()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $vendor->load('user');

        return view('vendor.profile.edit', compact('vendor'));
    }

    /**
     * Update vendor profile.
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $user = Auth::user();

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $validated = $this->validateProfileData($request, $user, $vendor);

        DB::beginTransaction();

        try {
            // Handle profile picture upload
            $profilePictureUrl = $this->handleProfilePicture($request, $user);

            // Update user data using save() method
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone_number = $validated['phone_number'];
            $user->location = $validated['location'] ?? $user->location;
            $user->about_me = $validated['about_me'] ?? $user->about_me;

            if ($profilePictureUrl) {
                $user->profile_picture_url = $profilePictureUrl;
            }

            $user->save();

            // Update password if provided
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
                $user->save();
            }

            // Update vendor data using save() method
            $vendor->business_name = $validated['business_name'];
            $vendor->service_category = $validated['service_category'];
            $vendor->experience_years = $validated['experience_years'];
            $vendor->save();

            DB::commit();

            return redirect()
                ->route('vendor.profile')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Get business statistics for the vendor.
     */
    private function getBusinessStats($vendor)
    {
        // Get total orders using joins to avoid ambiguous column issues
        $totalOrders = DB::table('transactions')
            ->join('qr_codes', 'transactions.qr_code_id', '=', 'qr_codes.id')
            ->join('services', 'qr_codes.service_id', '=', 'services.id')
            ->where('services.vendor_id', $vendor->id)
            ->where('transactions.status', 'COMPLETED')
            ->whereNull('transactions.deleted_at')
            ->whereNull('qr_codes.deleted_at')
            ->whereNull('services.deleted_at')
            ->count();

        // Get total revenue
        $totalRevenue = DB::table('transactions')
            ->join('qr_codes', 'transactions.qr_code_id', '=', 'qr_codes.id')
            ->join('services', 'qr_codes.service_id', '=', 'services.id')
            ->where('services.vendor_id', $vendor->id)
            ->where('transactions.status', 'COMPLETED')
            ->whereNull('transactions.deleted_at')
            ->whereNull('qr_codes.deleted_at')
            ->whereNull('services.deleted_at')
            ->sum('transactions.amount');

        return [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue ?? 0,
        ];
    }

    /**
     * Validate profile data.
     */
    private function validateProfileData(Request $request, $user, $vendor)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone_number' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9\-\+\s\(\)]+$/'
            ],
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
            'location' => ['nullable', 'string', 'max:255'],
            'about_me' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:5', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ], [
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
    private function handleProfilePicture(Request $request, $user)
    {
        if (!$request->hasFile('profile_picture')) {
            return null;
        }

        // Delete old profile picture if exists
        if ($user->profile_picture_url) {
            $oldPath = str_replace('/storage/', 'public/', $user->profile_picture_url);
            Storage::delete($oldPath);
        }

        $file = $request->file('profile_picture');
        $fileName = 'vendor_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('public/profile_pictures', $fileName);

        return Storage::url($filePath);
    }
}
