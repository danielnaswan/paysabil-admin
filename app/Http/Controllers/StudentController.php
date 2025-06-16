<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('user')->get();
        return view('pages/student/student', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages/student/create-student');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:100',
            'matrix_no' => 'required|string|max:20|unique:students',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|string|min:5|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $profilePictureUrl = null;
            if ($request->hasFile('profile_picture')) {
                $file = $request->file('profile_picture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/profile_pictures', $fileName);
                $profilePictureUrl = Storage::url($filePath);
            }

            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'profile_picture_url' => $profilePictureUrl,
                'role' => 'STUDENT'
            ]);

            $student = Student::create([
                'full_name' => $request->full_name,
                'matrix_no' => $request->matrix_no,
                'user_id' => $user->id
            ]);

            DB::commit();

            return redirect()->route('student.index')->with('success', 'Student registered successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Error registering student: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('pages/student/show-student', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('pages/student/edit-student', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        
        $request->validate([
            'full_name'         => 'required|string|max:100',
            'matrix_no'         => 'required|string|max:20|unique:students,matrix_no,'.$student->id,
            'email'             => 'required|string|email|max:255|unique:users,email,'.$student->user_id,
            'phone_number'      => 'required|string|max:15',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $student->user->update([
                'name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if it exists
                if ($student->user->profile_picture_url) {
                    Storage::delete(str_replace('/storage', 'public', $student->user->profile_picture_url));
                }

                $file = $request->file('profile_picture');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/profile_pictures', $fileName);
                $student->user->profile_picture_url = Storage::url($filePath);
            }

            $student->full_name = $request->full_name;
            $student->matrix_no = $request->matrix_no;
            $student->save();
            $student->user->save();

            DB::commit();

            return redirect()->route('student.index')->with('success', 'Student updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['msg' => 'Error updating student: ' . $e->getMessage()]);
        }
    }

    /*
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $student = Student::findOrFail($id);
            
            if ($student->profile_picture_url) {
                Storage::delete(str_replace('/storage', 'public', $student->profile_picture_url));
            }

            $student->user()->delete();
            
            $student->delete();

            return redirect()->route('student.index')->with('success', 'Student deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['msg' => 'Error deleting student: ' . $e->getMessage()]);
        }
    }
}