<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiAuthController extends Controller
{
    public function register(Request $request) 
    {
        $attributes = $request->validate([
            'name'          => ['required', 'max:50'],
            'matrix_no'     => ['required', 'max:10'],
            'email'         => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'phone_number'  => ['required','string', 'max:15'],
            'role'          => ['required', new Enum(UserRole::class)],
            'password'      => ['required', 'min:5', 'max:20']
        ]);
        
        $attributes['password'] = bcrypt($attributes['password']);

        DB::beginTransaction();
        try {
            $user = User::create($attributes);
            
            if ($attributes['role'] === UserRole::STUDENT->value) {
                $student = $user->student()->create([
                    'full_name' => $attributes['name'],
                    'matrix_no' => $attributes['matrix_no'],
                ]);
            }
            
            DB::commit();
            
            $user->load('student');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed: ' . $th->getMessage(),
                'error' => true
            ], 500);
        }

        $token = $user->createToken($attributes['name']);

        return response()->json([
            'user' => $user,
            'student' => $user->student,
            'token' => [
                'access_token' => $token->plainTextToken,
                'token_type' => 'Bearer'
            ],
            'message' => 'Registration successful'
        ], 201);
    }

    public function login(Request $request) 
    {
        $request->validate([
            'email'         => ['required', 'email', 'max:50'],
            'password'      => ['required']
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect!',
                'error' => true
            ], 401);
        }

        $user->load('student');
        
        $token = $user->createToken($user->name);

        return response()->json([
            'user' => $user,
            'student' => $user->student,
            'token' => $token->plainTextToken,
            'application' => $user->student->application, //added 10-06-2025
            'message' => 'Login successful'
        ], 200);
    }

    public function logout(Request $request) 
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'You are logged out'
        ], 200);
    }
}