<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Vendor;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode as QRGenerator;
use chillerlan\QRCode\QROptions;
class QrCodeController extends Controller
{
    public function index()
    {
        $qrCodes = QrCode::with(['vendor', 'service'])->get();
        return view('pages.qrcode.qrcode', compact('qrCodes'));
    }

    public function create()
    {
        $vendors = Vendor::all();
        $services = Service::all();
        return view('pages.qrcode.create-qrcode', compact('vendors', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'service_id' => 'required|exists:services,id',
            'expiry_hours' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            $uniqueCode = Str::uuid()->toString();
            $generatedDate = Carbon::now();
            $expiryHours = (int) $request->expiry_hours;
            $expiryDate = $generatedDate->copy()->addHours($expiryHours);

            $service = Service::findOrFail($request->service_id);
            
            $serviceDetails = [
                'service_name' => $service->name,
                'price' => $service->price,
                'vendor_name' => $service->vendor->business_name
            ];

            $qrCode = QrCode::create([
                'code' => $uniqueCode,
                'service_details' => $serviceDetails,
                'generated_date' => $generatedDate,
                'expiry_date' => $expiryDate,
                'status' => 'ACTIVE',
                'vendor_id' => $request->vendor_id,
                'service_id' => $request->service_id
            ]);

            DB::commit();
            
            return redirect()->route('qrcode.show', $qrCode->id)
                           ->with('success', 'QR Code generated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                           ->withErrors(['msg' => 'Error generating QR code: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $qrCode = QrCode::with(['vendor', 'service'])->findOrFail($id);
        
        // Generate QR code image
        $options = new QROptions([
            'outputType' => 'svg',         // Changed from constant to string
            'eccLevel' => 0,               // Changed from constant to integer
            'version' => 5,
            'imageBase64' => false,
            'addQuietzone' => true,
            'svgWidth' => 300,
            'svgHeight' => 300
        ]);

        $qrGenerator = new QRGenerator($options);
        $qrImage = $qrGenerator->render($qrCode->code);

        return view('pages.qrcode.show-qrcode', compact('qrCode', 'qrImage'));
    }

    public function edit(string $id)
    {
        $qrCode = QrCode::with(['vendor', 'service'])->findOrFail($id);
        return view('pages.qrcode.edit-qrcode', compact('qrCode'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,EXPIRED,USED,INVALID',
            'expiry_hours' => 'required|integer|min:1'
        ]);

        $qrCode = QrCode::findOrFail($id);

        try {
            $expiryHours = (int) $request->expiry_hours;
            $expiryDate = Carbon::parse($qrCode->generated_date)->addHours($expiryHours);
            
            $qrCode->update([
                'status' => $request->status,
                'expiry_date' => $expiryDate
            ]);

            return redirect()->route('qrcode.index')
                           ->with('success', 'QR Code updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error updating QR code: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $qrCode = QrCode::findOrFail($id);
            $qrCode->delete();

            return redirect()->route('qrcode.index')
                           ->with('success', 'QR Code deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error deleting QR code: ' . $e->getMessage()]);
        }
    }
}