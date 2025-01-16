<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::with('user')->get();
        return view('pages/vendor/vendor', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages/vendor/create-vendor');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'service_category' => 'required|string|max:50',
            'experience_years' => 'required|integer|min:0',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->business_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role' => 'VENDOR'
            ]);

            $profilePictureUrl = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/profile_pictures', $fileName);
                $profilePictureUrl = Storage::url($filePath);
            }

            $vendor = new Vendor();
            $vendor->id = Str::uuid();
            $vendor->business_name = $request->business_name;
            $vendor->service_category = $request->service_category;
            $vendor->experience_years = $request->experience_years;
            $vendor->profile_picture_url = $profilePictureUrl;
            $vendor->user_id = $user->id;
            $vendor->save();

            DB::commit();

            return redirect()->route('vendor.show', $vendor->id)
                           ->with('success', 'Vendor registered successfully! You can now add menu items.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Error registering vendor: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Vendor::with(['user', 'services'])->findOrFail($id);
        return view('pages/vendor/show-vendor', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $vendor = Vendor::with('user')->findOrFail($id);
        return view('pages/vendor/edit-vendor', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);
        
        $request->validate([
            'business_name' => 'required|string|max:100',
            'service_category' => 'required|string|max:50',
            'experience_years' => 'required|integer|min:0',
            'email' => 'required|string|email|max:255|unique:users,email,'.$vendor->user_id,
            'phone_number' => 'required|string|max:15',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $vendor->user->update([
                'name' => $request->business_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            if ($request->hasFile('profile_picture')) {
                if ($vendor->profile_picture_url) {
                    Storage::delete(str_replace('/storage', 'public', $vendor->profile_picture_url));
                }

                $file = $request->file('profile_picture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/profile_pictures', $fileName);
                $vendor->profile_picture_url = Storage::url($filePath);
            }

            $vendor->business_name = $request->business_name;
            $vendor->service_category = $request->service_category;
            $vendor->experience_years = $request->experience_years;
            $vendor->save();

            DB::commit();

            return redirect()->route('vendor.index')->with('success', 'Vendor updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Error updating vendor: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            
            if ($vendor->profile_picture_url) {
                Storage::delete(str_replace('/storage', 'public', $vendor->profile_picture_url));
            }

            $vendor->user()->delete();
            $vendor->delete();

            return redirect()->route('vendor.index')->with('success', 'Vendor deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Error deleting vendor: ' . $e->getMessage()]);
        }
    }
}