<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendorSettingsController extends Controller
{
    /**
     * Display vendor settings.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $vendor->load('user');

        return view('vendor.settings.index', compact('vendor'));
    }

    /**
     * Update vendor settings.
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $user = Auth::user();

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $validated = $request->validate([
            'current_password' => ['required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:5', 'confirmed'],
            'email_notifications' => ['nullable', 'boolean'],
            'sms_notifications' => ['nullable', 'boolean'],
            'marketing_emails' => ['nullable', 'boolean'],
        ]);

        try {
            // Update password if provided
            if (!empty($validated['new_password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return redirect()
                        ->back()
                        ->withErrors(['current_password' => 'Current password is incorrect.']);
                }

                // Use save() method instead of update()
                $user->password = Hash::make($validated['new_password']);
                $user->save();
            }

            // Here you can add logic to save notification preferences
            // For now, we'll just show success message

            return redirect()
                ->route('vendor.settings')
                ->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to update settings. Please try again.']);
        }
    }
}
