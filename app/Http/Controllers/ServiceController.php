<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Service category constants
     */
    private const VALID_CATEGORIES = [
        'Main Course',
        'Appetizer',
        'Dessert',
        'Beverage',
        'Snack',
        'Combo Meal',
        'Special',
        'Other'
    ];

    /**
     * Maximum preparation time in minutes
     */
    private const MAX_PREPARATION_TIME = 180; // 3 hours

    /**
     * Display a listing of services.
     */
    public function index(): View
    {
        $services = Service::with(['vendor.user'])
            ->orderBy('is_available', 'desc')
            ->orderBy('vendor_id')
            ->orderBy('name')
            ->paginate(20);

        $statistics = $this->getServiceStatistics();

        return view('pages.service.service', compact('services', 'statistics'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(?int $vendorId = null): View
    {
        $vendor = null;
        $vendors = collect();

        if ($vendorId) {
            $vendor = Vendor::with('user')->findOrFail($vendorId);
        } else {
            $vendors = Vendor::with('user')
                ->whereHas('user') // Ensure vendor has a user account
                ->orderBy('business_name')
                ->get();
        }

        $categories = self::VALID_CATEGORIES;

        return view('pages.service.create-service', compact('vendor', 'vendors', 'categories'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateServiceData($request);

        // Verify vendor exists and is active
        $vendor = Vendor::with('user')->findOrFail($validated['vendor_id']);

        if (!$vendor->user) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['vendor_id' => 'Selected vendor does not have a valid user account.']);
        }

        DB::beginTransaction();

        try {
            $service = Service::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category' => $validated['category'],
                'preparation_time' => $validated['preparation_time'],
                'vendor_id' => $validated['vendor_id'],
                'is_available' => $validated['is_available'] ?? true,
            ]);

            // Log service creation
            // $this->logServiceActivity($service, 'created');

            DB::commit();

            return redirect()
                ->route('vendor.show', $service->vendor_id)
                ->with('success', 'Menu item added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Service creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create menu item. Please try again.']);
        }
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        $service->load(['vendor.user', 'qrCodes', 'ratings.student']);

        $statistics = $service->getStatistics();
        $feedbackSummary = $service->getFeedbackSummary();
        $recommendations = $service->getRecommendations();

        return view('pages.service.show-service', compact(
            'service',
            'statistics',
            'feedbackSummary',
            'recommendations'
        ));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $service->load('vendor.user');
        $categories = self::VALID_CATEGORIES;

        // Get all vendors for potential reassignment (admin only)
        $vendors = Vendor::with('user')
            ->whereHas('user')
            ->orderBy('business_name')
            ->get();

        return view('pages.service.edit-service', compact('service', 'categories', 'vendors'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $this->validateServiceData($request, $service);

        // Check if vendor change is allowed and valid
        if ($validated['vendor_id'] !== $service->vendor_id) {
            if (!$this->canChangeVendor($service)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['vendor_id' => 'Cannot change vendor for service with existing transactions.']);
            }

            $newVendor = Vendor::with('user')->findOrFail($validated['vendor_id']);
            if (!$newVendor->user) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['vendor_id' => 'Selected vendor does not have a valid user account.']);
            }
        }

        DB::beginTransaction();

        try {
            $oldData = $service->toArray();

            $service->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category' => $validated['category'],
                'preparation_time' => $validated['preparation_time'],
                'vendor_id' => $validated['vendor_id'],
                'is_available' => $validated['is_available'] ?? false,
            ]);

            // Log service update
            // $this->logServiceActivity($service, 'updated', [
            //     'old_data' => $oldData,
            //     'changes' => $service->getChanges()
            // ]);

            DB::commit();

            return redirect()
                ->route('vendor.show', $service->vendor_id)
                ->with('success', 'Menu item updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Service update failed: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'request_data' => $request->all(),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update menu item. Please try again.']);
        }
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        // Prevent deletion of services with transactions or active QR codes
        if (!$this->canDeleteService($service)) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Cannot delete service with existing transactions or active QR codes.']);
        }

        DB::beginTransaction();

        try {
            $vendorId = $service->vendor_id;

            // Log service deletion
            // $this->logServiceActivity($service, 'deleted');

            // Soft delete the service
            $service->delete();

            DB::commit();

            return redirect()
                ->route('vendor.show', $vendorId)
                ->with('success', 'Menu item deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Service deletion failed: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete menu item. Please try again.']);
        }
    }

    /**
     * Toggle service availability.
     */
    public function toggleAvailability(Service $service): RedirectResponse
    {
        try {
            $oldStatus = $service->is_available;
            $service->toggleAvailability();

            // Log availability change
            // $this->logServiceActivity($service, 'availability_toggled', [
            //     'old_availability' => $oldStatus,
            //     'new_availability' => $service->is_available
            // ]);

            $status = $service->is_available ? 'available' : 'unavailable';

            return redirect()
                ->back()
                ->with('success', "Menu item marked as {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Service availability toggle failed: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update availability. Please try again.']);
        }
    }

    /**
     * Bulk update service availability.
     */
    public function bulkUpdateAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'availability' => 'required|boolean'
        ]);

        DB::beginTransaction();

        try {
            $services = Service::whereIn('id', $validated['service_ids'])->get();
            $updatedCount = 0;

            foreach ($services as $service) {
                if ($service->is_available !== $validated['availability']) {
                    $service->update(['is_available' => $validated['availability']]);
                    // $this->logServiceActivity($service, 'bulk_availability_update', [
                    //     'new_availability' => $validated['availability']
                    // ]);
                    $updatedCount++;
                }
            }

            DB::commit();

            $status = $validated['availability'] ? 'available' : 'unavailable';

            return redirect()
                ->back()
                ->with('success', "Successfully marked {$updatedCount} services as {$status}.");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk availability update failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update service availability. Please try again.']);
        }
    }

    /**
     * Get services by vendor (AJAX endpoint).
     */
    public function getByVendor(Vendor $vendor): \Illuminate\Http\JsonResponse
    {
        try {
            $services = $vendor->services()
                ->select('id', 'name', 'price', 'category', 'is_available', 'preparation_time')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'services' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get vendor services: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load services.'
            ], 500);
        }
    }

    /**
     * Validate service data for create/update operations.
     */
    private function validateServiceData(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'min:2',
                Rule::unique('services')->where(function ($query) use ($request) {
                    return $query->where('vendor_id', $request->vendor_id);
                })->ignore($service?->id)
            ],
            'description' => ['required', 'string', 'max:1000', 'min:10'],
            'price' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'category' => [
                'required',
                'string',
                Rule::in(self::VALID_CATEGORIES)
            ],
            'preparation_time' => [
                'required',
                'integer',
                'min:1',
                'max:' . self::MAX_PREPARATION_TIME
            ],
            'vendor_id' => ['required', 'exists:vendors,id'],
            'is_available' => ['boolean']
        ], [
            'name.unique' => 'This vendor already has a service with this name.',
            'name.min' => 'Service name must be at least 2 characters long.',
            'description.min' => 'Description must be at least 10 characters long.',
            'price.regex' => 'Price must be a valid amount with up to 2 decimal places.',
            'price.max' => 'Price cannot exceed RM 999.99.',
            'preparation_time.max' => 'Preparation time cannot exceed 3 hours (180 minutes).',
            'category.in' => 'Please select a valid category.',
        ]);
    }

    /**
     * Check if service can be deleted.
     */
    private function canDeleteService(Service $service): bool
    {
        // Check for completed transactions
        $hasTransactions = $service->transactions()
            ->where('transactions.status', 'COMPLETED')
            ->exists();

        // Check for active QR codes
        $hasActiveQrCodes = $service->activeQrCodes()->exists();

        return !$hasTransactions && !$hasActiveQrCodes;
    }

    /**
     * Check if vendor can be changed for service.
     */
    private function canChangeVendor(Service $service): bool
    {
        // Prevent vendor change if service has any transactions
        return !$service->transactions()->exists();
    }

    /**
     * Get service statistics for dashboard.
     */
    private function getServiceStatistics(): array
    {
        return [
            'total' => Service::count(),
            'available' => Service::available()->count(),
            'unavailable' => Service::where('is_available', false)->count(),
            'by_category' => Service::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->pluck('count', 'category')
                ->toArray(),
            'avg_price' => Service::avg('price') ?? 0,
            'avg_prep_time' => Service::avg('preparation_time') ?? 0,
            'most_popular' => Service::withCount('completedTransactions')
                ->orderBy('completed_transactions_count', 'desc')
                ->first()?->name ?? 'N/A',
        ];
    }

    /**
     * Log service activity for audit trail.
     */
    // private function logServiceActivity(Service $service, string $action, array $details = []): void
    // {
    //     Log::info("Service {$action}", [
    //         'service_id' => $service->id,
    //         'service_name' => $service->name,
    //         'vendor_id' => $service->vendor_id,
    //         'user_id' => auth()->id(),
    //         'action' => $action,
    //         'details' => $details,
    //         'timestamp' => now()
    //     ]);
    // }
}
