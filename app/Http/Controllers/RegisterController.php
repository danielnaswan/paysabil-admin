<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store(Request $request)
{
    $request->validate([
        'business_name' => ['required', 'string', 'max:100'],
        'service_category' => ['required', 'string', 'max:50'],
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
        'phone_number' => ['required', 'string', 'max:15'],
        'password' => ['required', 'string', 'min:5', 'confirmed'],
        'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    ]);

    $encryptedPassword = Hash::make($request->password);
    $profilePictureUrl = null;

    DB::beginTransaction();

    try {
        if($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $fileName = time(). '_'. $file->getClientOriginalName();
            $filePath = $file->storeAs('public/profile_pictures', $fileName);
            $profilePictureUrl = Storage::url($filePath);
        }

        $user = User::create([
            'name' => $request->business_name,
            'email' => $request->email,
            'password' => $encryptedPassword,
            'phone_number' => $request->phone_number,
            'profile_picture_url' => $profilePictureUrl,
            'role' => UserRole::VENDOR->value,
        ]);

        $vendor = Vendor::create([
            'user_id' => $user->id,
            'business_name' => $user->name,
            'service_category' => $request->service_category,
            'experience_years' => 0,
        ]);

        DB::commit();

        session()->flash('success', 'Your account has been created successfully!');
        return redirect('/login');

    } catch (Throwable $th) {
        DB::rollBack();
        Log::error('Registration error: ' . $th->getMessage());

        return back()
            ->withInput()
            ->withErrors(['error' => 'Registration failed. Please try again.']);
    }
}
}
