<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(): View
    {
        $students = Student::with(['user', 'application'])
            ->active()
            ->get();

        return view('pages.student.student', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        return view('pages.student.create-student');
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateStudentData($request);

        DB::beginTransaction();

        try {
            $profilePictureUrl = $this->handleProfilePicture($request);

            $user = $this->createUser($validated, $profilePictureUrl);
            $student = $this->createStudent($validated, $user->id);

            DB::commit();

            return redirect()
                ->route('student.index')
                ->with('success', 'Student registered successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student creation failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['password', 'password_confirmation']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create student. Please try again.']);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): View
    {
        $student->load(['user', 'application', 'transactions' => function($query) {
            $query->with('vendor')->latest()->take(5);
        }]);

        return view('pages.student.show-student', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        $student->load('user');
        return view('pages.student.edit-student', compact('student'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $this->validateStudentData($request, $student);

        DB::beginTransaction();

        try {
            $this->updateUser($student->user, $validated, $request);
            $this->updateStudent($student, $validated);

            DB::commit();

            return redirect()
                ->route('student.index')
                ->with('success', 'Student updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student update failed: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'request_data' => $request->except(['password', 'password_confirmation']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update student. Please try again.']);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Delete profile picture if exists
            if ($student->user->profile_picture_url) {
                $this->deleteProfilePicture($student->user->profile_picture_url);
            }

            // Soft delete student (will cascade to user due to relationship)
            $student->delete();
            $student->user->delete();

            DB::commit();

            return redirect()
                ->route('student.index')
                ->with('success', 'Student deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Student deletion failed: ' . $e->getMessage(), [
                'student_id' => $student->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete student. Please try again.']);
        }
    }

    /**
     * Validate student data for store/update operations.
     */
    private function validateStudentData(Request $request, ?Student $student = null): array
    {
        $rules = [
            'full_name' => ['required', 'string', 'max:100'],
            'matrix_no' => [
                'required', 
                'string', 
                'max:20', 
                'regex:/^[A-Z0-9]+$/',
                Rule::unique('students')->ignore($student?->id)
            ],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
                Rule::unique('users')->ignore($student?->user_id)
            ],
            'phone_number' => ['required', 'string', 'max:15', 'regex:/^[0-9\-\+\s]+$/'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ];

        // Add password validation only for store operation
        if (!$student) {
            $rules['password'] = ['required', 'string', 'min:5', 'confirmed'];
        }

        return $request->validate($rules, [
            'matrix_no.regex' => 'Matrix number must contain only uppercase letters and numbers.',
            'phone_number.regex' => 'Please enter a valid phone number.',
            'profile_picture.max' => 'Profile picture must not exceed 2MB.',
        ]);
    }

    /**
     * Handle profile picture upload.
     */
    private function handleProfilePicture(Request $request): ?string
    {
        if (!$request->hasFile('profile_picture')) {
            return null;
        }

        $file = $request->file('profile_picture');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('public/profile_pictures', $fileName);

        return Storage::url($filePath);
    }

    /**
     * Create new user record.
     */
    private function createUser(array $validated, ?string $profilePictureUrl): User
    {
        return User::create([
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'profile_picture_url' => $profilePictureUrl,
            'role' => UserRole::STUDENT
        ]);
    }

    /**
     * Create new student record.
     */
    private function createStudent(array $validated, int $userId): Student
    {
        return Student::create([
            'full_name' => $validated['full_name'],
            'matrix_no' => strtoupper($validated['matrix_no']),
            'user_id' => $userId
        ]);
    }

    /**
     * Update user record.
     */
    private function updateUser(User $user, array $validated, Request $request): void
    {
        $updateData = [
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ];

        // Handle profile picture update
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($user->profile_picture_url) {
                $this->deleteProfilePicture($user->profile_picture_url);
            }

            $updateData['profile_picture_url'] = $this->handleProfilePicture($request);
        }

        $user->update($updateData);
    }

    /**
     * Update student record.
     */
    private function updateStudent(Student $student, array $validated): void
    {
        $student->update([
            'full_name' => $validated['full_name'],
            'matrix_no' => strtoupper($validated['matrix_no']),
        ]);
    }

    /**
     * Delete profile picture file.
     */
    private function deleteProfilePicture(string $profilePictureUrl): bool
    {
        $path = str_replace('/storage', 'public', $profilePictureUrl);
        return Storage::delete($path);
    }
}