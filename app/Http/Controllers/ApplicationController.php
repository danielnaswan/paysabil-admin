<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = Application::with(['student', 'reviewer'])->get();
        return view('pages.application.application', compact('applications'));
    }

    public function create()
    {
        $students = Student::all();  // Get all students for the dropdown
        return view('pages.application.create-application', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'            => 'required|exists:students,id',
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'document'              => 'required|mimes:pdf|max:10240' // Max 10MB
        ]);

        try {
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/applications', $fileName);

            Application::create([
                'id'                => Str::uuid(),
                'title'             => $request->title,
                'description'       => $request->description,
                'submission_date'   => now(),
                'document_url'      => Storage::url($filePath),
                'document_name'     => $file->getClientOriginalName(),
                'document_size'     => $file->getSize(),
                'student_id'        => $request->student_id,
                'status'            => 'PENDING'
            ]);

            return redirect()->route('application.index')
                           ->with('success', 'Application created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error creating application: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $application = Application::with(['student', 'reviewer'])->findOrFail($id);
        return view('pages.application.show-application', compact('application'));
    }

    public function edit(string $id)
    {
        $application = Application::with(['student', 'reviewer'])->findOrFail($id);
        return view('pages.application.edit-application', compact('application'));
    }

    public function update(Request $request, string $id)
    {
        $application = Application::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:PENDING,APPROVED,REJECTED',
            'admin_remarks' => 'required|string',
            'document' => 'nullable|mimes:pdf|max:10240'
        ]);

        try {
            if ($request->hasFile('document')) {
                if ($application->document_url) {
                    Storage::delete(str_replace('/storage', 'public', $application->document_url));
                }

                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/applications', $fileName);
                
                $application->document_url = Storage::url($filePath);
                $application->document_name = $file->getClientOriginalName();
                $application->document_size = $file->getSize();
            }

            $application->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
                'admin_remarks' => $request->admin_remarks,
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now()
            ]);

            return redirect()->route('application.index')
                           ->with('success', 'Application updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error updating application: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $application = Application::findOrFail($id);
            
            if ($application->document_url) {
                Storage::delete(str_replace('/storage', 'public', $application->document_url));
            }
            
            $application->delete();

            return redirect()->route('application.index')
                           ->with('success', 'Application deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withErrors(['msg' => 'Error deleting application: ' . $e->getMessage()]);
        }
    }
}