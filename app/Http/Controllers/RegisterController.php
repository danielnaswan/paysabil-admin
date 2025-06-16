<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Log;
use Throwable;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store(Request $request)
    {
        $rule = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'id' => ['required', 'string', 'max:10', Rule::unique('users', 'id')],
            'password' => ['required', 'string', 'min:5', 'max:15'],
            'phone_number' => ['required', 'string', 'min:5', 'max:15'],
            'role' => ['required', new Enum(UserRole::class)],
            'agreement' => ['required', 'accepted'],
        ];


        $attributes = $request->validate($rule);
        $role = $attributes['role'];
        $attributes['password'] = bcrypt($attributes['password']);

        DB::beginTransaction();

        try {
            $user = User::create($attributes);

            switch($role) {
                case UserRole::STUDENT->value:
                    $user->student()->create([
                        'full_name' => $attributes['name'],
                        'matrix_no' =>$attributes['id'],
                    ]);
                    break;
                case UserRole::VENDOR->value:
                    $user->vendor()->create([
                        'business_name' => $attributes['name'],
                        'service_category' => ''?? null,
                        'experience_years' => 0,
                    ]);
            }

            DB::commit();

            session()->flash('success', 'Your account has been created succcesfully!');
            Auth::login($user);
            return redirect('/dashboard');

        } catch (Throwable $th) {
            DB::rollBack();
            Log::error('Registration error: ' . $th->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}
