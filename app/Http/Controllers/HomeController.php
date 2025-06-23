<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;

class HomeController extends Controller
{
    public function home()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Redirect based on user role
        switch ($user->role) {
            case UserRole::ADMIN:
                return redirect('/dashboard');

            case UserRole::VENDOR:
                // Check if vendor profile exists
                if (!$user->vendor) {
                    return redirect('/logout')->with('error', 'Vendor profile not found. Please contact support.');
                }
                return redirect('/vendor/dashboard');

            case UserRole::STUDENT:
                // For now, redirect to a student dashboard or home page
                // You can create a student dashboard later
                return redirect('/logout')->with('error', 'Invalid user role.');

            default:
                return redirect('/logout')->with('error', 'Invalid user role.');
        }
    }
}
