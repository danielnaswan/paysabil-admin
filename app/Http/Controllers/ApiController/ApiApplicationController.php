<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Support\Facades\Storage;

class ApiApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'title' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'document' => ['required', 'mimes:pdf', 'max:10240', 'file'],
        ]);
        
        $file = $request->file('document');
        $fileName = time(). '_'. $file->getClientOriginalName();
        $filePath = $file->storeAs('public/applications', $fileName);

        $application = Application::create([
            'title' => $attributes['title'], 
            'description' => $attributes['description'], 
            'document_name' => $file->getClientOriginalName(), 
            'document_url' => Storage::url($filePath),
            'document_size' => $file->getSize(),
            'submission_date' => now(),
            'student_id' => $request->student_id,
            'status' => 'PENDING',
        ]);

        return response()->json([
            'message' => 'PDF uploaded successfully',
            'data' => $application,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
