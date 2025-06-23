<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Carbon\Carbon;

class VendorQrCodeController extends Controller
{
    /**
     * Display vendor QR codes.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCodes = QrCode::where('vendor_id', $vendor->id)
            ->with(['service'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get statistics
        $stats = [
            'total_qr_codes' => QrCode::where('vendor_id', $vendor->id)->count(),
            'active_qr_codes' => QrCode::where('vendor_id', $vendor->id)->where('status', 'ACTIVE')->count(),
            'expired_qr_codes' => QrCode::where('vendor_id', $vendor->id)->where('status', 'EXPIRED')->count(),
            'used_qr_codes' => QrCode::where('vendor_id', $vendor->id)->where('status', 'USED')->count(),
        ];

        return view('vendor.qrcodes.index', compact('qrCodes', 'stats'));
    }

    /**
     * Show the form for creating a new QR code.
     */
    public function create()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $services = Service::where('vendor_id', $vendor->id)
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            return redirect()
                ->route('vendor.services.index')
                ->with('error', 'You need to create at least one available service before generating QR codes.');
        }

        return view('vendor.qrcodes.create', compact('services'));
    }

    /**
     * Store a newly created QR code.
     */
    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'expiry_hours' => ['required', 'integer', 'min:1', 'max:168'], // Max 1 week
            'quantity' => ['required', 'integer', 'min:1', 'max:10'], // Max 10 QR codes at once
        ]);

        // Verify service belongs to vendor
        $service = Service::where('id', $validated['service_id'])
            ->where('vendor_id', $vendor->id)
            ->where('is_available', true)
            ->first();

        if (!$service) {
            return redirect()
                ->back()
                ->withErrors(['service_id' => 'Invalid service selected.']);
        }

        try {
            $qrCodes = [];
            $expiryDate = Carbon::now()->addHours($validated['expiry_hours']);

            for ($i = 0; $i < $validated['quantity']; $i++) {
                // Generate unique QR code
                $code = $this->generateUniqueCode();

                // Create service details
                $serviceDetails = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'price' => $service->price,
                    'vendor_id' => $vendor->id,
                    'vendor_name' => $vendor->business_name,
                    'generated_at' => now()->toISOString(),
                ];

                $qrCode = QrCode::create([
                    'code' => $code,
                    'service_details' => json_encode($serviceDetails),
                    'generated_date' => now(),
                    'expiry_date' => $expiryDate,
                    'status' => 'ACTIVE',
                    'vendor_id' => $vendor->id,
                    'service_id' => $service->id,
                ]);

                $qrCodes[] = $qrCode;
            }

            $message = $validated['quantity'] == 1
                ? 'QR code generated successfully!'
                : $validated['quantity'] . ' QR codes generated successfully!';

            return redirect()
                ->route('vendor.qrcodes.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to generate QR code(s). Please try again.']);
        }
    }

    /**
     * Display the specified QR code.
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)
            ->with(['service', 'transactions.student.user'])
            ->findOrFail($id);

        return view('vendor.qrcodes.show', compact('qrCode'));
    }

    /**
     * Show the form for editing the specified QR code.
     */
    public function edit($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)->findOrFail($id);

        // Only allow editing of ACTIVE QR codes
        if ($qrCode->status !== 'ACTIVE') {
            return redirect()
                ->route('vendor.qrcodes.index')
                ->withErrors(['error' => 'Only active QR codes can be edited.']);
        }

        return view('vendor.qrcodes.edit', compact('qrCode'));
    }

    /**
     * Update the specified QR code.
     */
    public function update(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)->findOrFail($id);

        // Only allow updating of ACTIVE QR codes
        if ($qrCode->status !== 'ACTIVE') {
            return redirect()
                ->route('vendor.qrcodes.index')
                ->withErrors(['error' => 'Only active QR codes can be updated.']);
        }

        $validated = $request->validate([
            'expiry_hours' => ['required', 'integer', 'min:1', 'max:168'],
        ]);

        try {
            $newExpiryDate = Carbon::now()->addHours($validated['expiry_hours']);

            $qrCode->update([
                'expiry_date' => $newExpiryDate
            ]);

            return redirect()
                ->route('vendor.qrcodes.index')
                ->with('success', 'QR code updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update QR code. Please try again.']);
        }
    }

    /**
     * Remove the specified QR code (set to expired).
     */
    public function destroy($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)->findOrFail($id);

        try {
            // Don't actually delete, just expire the QR code
            $qrCode->update(['status' => 'EXPIRED']);

            return redirect()
                ->route('vendor.qrcodes.index')
                ->with('success', 'QR code expired successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to expire QR code. Please try again.']);
        }
    }

    /**
     * Download QR code image.
     */
    public function download($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)->findOrFail($id);

        try {
            // Generate QR code image
            $qrImage = QrCodeGenerator::format('png')
                ->size(300)
                ->margin(1)
                ->generate($qrCode->code);

            $filename = 'qr_code_' . $qrCode->id . '_' . now()->format('Y-m-d') . '.png';

            return response($qrImage)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to download QR code. Please try again.']);
        }
    }

    /**
     * Display QR code image.
     */
    public function image($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $qrCode = QrCode::where('vendor_id', $vendor->id)->findOrFail($id);

        try {
            $qrImage = QrCodeGenerator::format('png')
                ->size(300)
                ->margin(1)
                ->generate($qrCode->code);

            return response($qrImage)->header('Content-Type', 'image/png');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to display QR code. Please try again.']);
        }
    }

    /**
     * Bulk expire QR codes.
     */
    public function bulkExpire(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $validated = $request->validate([
            'qr_code_ids' => ['required', 'array'],
            'qr_code_ids.*' => ['exists:qr_codes,id'],
        ]);

        try {
            $count = QrCode::where('vendor_id', $vendor->id)
                ->whereIn('id', $validated['qr_code_ids'])
                ->where('status', 'ACTIVE')
                ->update(['status' => 'EXPIRED']);

            return redirect()
                ->route('vendor.qrcodes.index')
                ->with('success', $count . ' QR code(s) expired successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to expire QR codes. Please try again.']);
        }
    }

    /**
     * Generate a unique QR code.
     */
    private function generateUniqueCode()
    {
        do {
            $code = 'QR' . strtoupper(Str::random(8)) . time();
        } while (QrCode::where('code', $code)->exists());

        return $code;
    }
}
