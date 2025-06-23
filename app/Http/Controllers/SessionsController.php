<?php

namespace App\Http\Controllers;

use BcMath\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($attributes)) {
            session()->regenerate();

            // Role-based redirect
            $user = Auth::user();

            switch ($user->role) {
                case \App\Enums\UserRole::ADMIN:
                    return redirect('/dashboard')->with(['success' => 'You are logged in.']);

                case \App\Enums\UserRole::VENDOR:
                    return redirect('/vendorside/dashboard')->with(['success' => 'Welcome to Vendor Portal.']);

                case \App\Enums\UserRole::STUDENT:
                    return redirect('/')->with(['success' => 'You are logged in.']);

                default:
                    return redirect('/')->with(['success' => 'You are logged in.']);
            }
        } else {
            return back()->withErrors(['email' => 'Email or password invalid.']);
        }
    }

    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success' => 'You\'ve been logged out.']);
    }
}
