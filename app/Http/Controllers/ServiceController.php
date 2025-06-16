<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('vendor')->get();
        return view('pages.service.create-service', compact('services'));
    }

    public function create($vendor)
    {
        $vendorModel = null;
        $vendors = collect();
        
        if ($vendor) {
            $vendorModel = Vendor::findOrFail($vendor);
        } else {
            $vendors = Vendor::all();
        }

        return view('pages.service.create-service', [
        'vendor' => $vendorModel,
        'vendors' => $vendors
    ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'preparation_time' => 'required|integer|min:1',
            'vendor_id' => 'required|exists:vendors,id',
            'is_available' => 'boolean|nullable'
        ]);

        DB::beginTransaction();

        try {
            Service::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'preparation_time' => $request->preparation_time,
                'vendor_id' => $request->vendor_id,
                'is_available' => 1,
            ]);

            DB::commit();

            return redirect()->route('vendor.show', $request->vendor_id)
                           ->with('success', 'Menu item added successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Error adding menu item: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $service = Service::with('vendor')->findOrFail($id);
        return view('pages.service.show', compact('service'));
    }

    public function edit(string $id)
    {
        $service = Service::with('vendor')->findOrFail($id);
        $vendors = Vendor::all();
        return view('pages.service.edit-service', compact('service', 'vendors'));
    }

    public function update(Request $request, string $id)
    {
        $service = Service::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:50',
            'preparation_time' => 'required|integer|min:1',
            'vendor_id' => 'required|exists:vendors,id',
            'is_available' => 'boolean'
        ]);

        try {
            $service->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category' => $request->category,
                'preparation_time' => $request->preparation_time,
                'is_available' => $request->has('is_available'),
                'vendor_id' => $request->vendor_id
            ]);

            return redirect()->route('vendor.show', $service->vendor_id)
                           ->with('success', 'Menu item updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error updating menu item: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $service = Service::findOrFail($id);
            $vendor_id = $service->vendor_id;
            $service->delete();

            return redirect()->route('vendor.show', $vendor_id)
                           ->with('success', 'Menu item deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error deleting menu item: ' . $e->getMessage()]);
        }
    }
}