<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorServiceController extends Controller
{
    /**
     * Display vendor services.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $services = Service::where('vendor_id', $vendor->id)
            ->withCount(['transactions as total_orders' => function ($query) {
                // FIXED: Specify table name for status column
                $query->where('transactions.status', 'COMPLETED');
            }])
            ->withSum(['transactions as total_revenue' => function ($query) {
                // FIXED: Specify table name for status column
                $query->where('transactions.status', 'COMPLETED');
            }], 'amount')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        return view('vendor.services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $validated = $this->validateServiceData($request);

        try {
            $service = Service::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category' => $validated['category'],
                'preparation_time' => $validated['preparation_time'],
                'is_available' => $validated['is_available'] ?? true,
                'vendor_id' => $vendor->id
            ]);

            return redirect()
                ->route('vendor.services.index')
                ->with('success', 'Service created successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create service. Please try again.']);
        }
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $service = Service::where('vendor_id', $vendor->id)
            ->with(['qrCodes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->withCount(['transactions as total_orders' => function ($query) {
                // FIXED: Specify table name for status column
                $query->where('transactions.status', 'COMPLETED');
            }])
            ->withSum(['transactions as total_revenue' => function ($query) {
                // FIXED: Specify table name for status column
                $query->where('transactions.status', 'COMPLETED');
            }], 'amount')
            ->findOrFail($id);

        // Get recent transactions for this service
        $recentTransactions = $service->transactions()
            ->with(['student.user'])
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();

        return view('vendor.services.show', compact('service', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $service = Service::where('vendor_id', $vendor->id)->findOrFail($id);

        return view('vendor.services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $service = Service::where('vendor_id', $vendor->id)->findOrFail($id);
        $validated = $this->validateServiceData($request);

        try {
            $service->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category' => $validated['category'],
                'preparation_time' => $validated['preparation_time'],
                'is_available' => $validated['is_available'] ?? $service->is_available,
            ]);

            return redirect()
                ->route('vendor.services.index')
                ->with('success', 'Service updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update service. Please try again.']);
        }
    }

    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $service = Service::where('vendor_id', $vendor->id)->findOrFail($id);

        DB::beginTransaction();

        try {
            // Check if service has active QR codes or recent transactions
            $hasActiveQRCodes = $service->qrCodes()->where('status', 'ACTIVE')->exists();
            $hasRecentTransactions = $service->transactions()
                ->where('transaction_date', '>=', now()->subDays(30))
                ->exists();

            if ($hasActiveQRCodes) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => 'Cannot delete service with active QR codes. Please expire QR codes first.']);
            }

            if ($hasRecentTransactions) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => 'Cannot delete service with recent transactions (last 30 days).']);
            }

            // Soft delete the service
            $service->delete();

            DB::commit();

            return redirect()
                ->route('vendor.services.index')
                ->with('success', 'Service deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete service. Please try again.']);
        }
    }

    /**
     * Toggle service availability.
     */
    public function toggleAvailability($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $service = Service::where('vendor_id', $vendor->id)->findOrFail($id);

        try {
            $service->update([
                'is_available' => !$service->is_available
            ]);

            $status = $service->is_available ? 'available' : 'unavailable';

            return redirect()
                ->back()
                ->with('success', "Service marked as {$status}!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update service availability.']);
        }
    }

    /**
     * Validate service data.
     */
    private function validateServiceData(Request $request)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'category' => ['required', 'string', 'max:50'],
            'preparation_time' => ['required', 'integer', 'min:1', 'max:180'],
            'is_available' => ['nullable', 'boolean']
        ], [
            'price.min' => 'Price must be at least RM 0.01',
            'price.max' => 'Price cannot exceed RM 999.99',
            'preparation_time.min' => 'Preparation time must be at least 1 minute',
            'preparation_time.max' => 'Preparation time cannot exceed 180 minutes'
        ]);
    }
}
