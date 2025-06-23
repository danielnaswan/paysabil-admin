<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorBasedAccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // Check if user has vendor role
        $user = Auth::user();

        // Make sure user has a vendor profile
        if ($user->role === UserRole::VENDOR) {
            // Check if vendor profile exists
            if (!$user->vendor) {
                return redirect('/logout')->with('error', 'Vendor profile not found. Please contact support.');
            }
            return $next($request);
        }

        // If not a vendor, redirect based on role
        if ($user->role === UserRole::ADMIN) {
            return redirect('/dashboard')->with('error', 'Admin accounts cannot access vendor portal.');
        }

        if ($user->role === UserRole::STUDENT) {
            return redirect('/')->with('error', 'Student accounts cannot access vendor portal.');
        }

        return redirect('/logout')->with('error', 'Invalid account type.');
    }
}
