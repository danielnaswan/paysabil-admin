<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class ApplicationController extends Controller
{
    /**
     * Maximum file size in kilobytes (10MB)
     */
    private const MAX_FILE_SIZE = 10240;

    /**
     * Allowed file types for documents
     */
    private const ALLOWED_MIMES = ['pdf'];

    /**
     * Application review SLA in days
     */
    private const REVIEW_SLA_DAYS = 5;

    /**
     * Display a listing of applications with filtering and sorting.
     */
    public function index(Request $request): View
    {
        $query = Application::with(['student.user', 'reviewer'])
            ->when($request->status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->search, function ($q, $search) {
                return $q->whereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('matrix_no', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->overdue === '1', function ($q) {
                return $q->where('status', 'PENDING')
                    ->where('submission_date', '<', now()->subDays(self::REVIEW_SLA_DAYS));
            });

        $applications = $query->orderBy('status')
            ->orderByRaw("CASE WHEN status = 'PENDING' AND submission_date < ? THEN 1 ELSE 2 END", [now()->subDays(self::REVIEW_SLA_DAYS)])
            ->orderBy('submission_date', 'asc')
            ->paginate(20);

        $statistics = $this->getApplicationStatistics();

        return view('pages.application.application', compact('applications', 'statistics'));
    }

    /**
     * Show the form for creating a new application.
     */
    public function create(): View
    {
        $students = Student::with('user')
            ->whereDoesntHave('application', function ($query) {
                $query->whereIn('status', ['PENDING', 'APPROVED']);
            })
            ->orderBy('full_name')
            ->get();

        return view('pages.application.create-application', compact('students'));
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateApplicationData($request);

        // Check if student already has pending or approved application
        if ($this->hasActiveApplication($validated['student_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['student_id' => 'This student already has an active application.']);
        }

        DB::beginTransaction();

        try {
            $documentData = $this->handleDocumentUpload($request);

            $application = Application::create([
                'student_id' => $validated['student_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'submission_date' => now(),
                'document_url' => $documentData['url'],
                'document_name' => $documentData['name'],
                'document_size' => $documentData['size'],
                'status' => Application::STATUS_PENDING
            ]);

            // Log application submission
            $this->logApplicationActivity($application, 'submitted');

            DB::commit();

            return redirect()
                ->route('application.index')
                ->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Application creation failed: ' . $e->getMessage(), [
                'request_data' => $request->except(['document']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit application. Please try again.']);
        }
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application): View
    {
        $application->load(['student.user', 'reviewer']);

        // Check if document exists
        $documentExists = $this->checkDocumentExists($application);

        return view('pages.application.show-application', compact('application', 'documentExists'));
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit(Application $application): View|RedirectResponse
    {
        // Only allow editing of pending applications
        if (!$application->canBeReviewed()) {
            return redirect()
                ->route('application.index')
                ->withErrors(['error' => 'This application cannot be edited.']);
        }

        $application->load(['student.user', 'reviewer']);

        return view('pages.application.edit-application', compact('application'));
    }

    /**
     * Update the specified application.
     */
    public function update(Request $request, Application $application): RedirectResponse
    {
        $validated = $this->validateApplicationUpdate($request, $application);

        DB::beginTransaction();

        try {
            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description']
            ];

            // Handle status update (admin only)
            if ($this->canUpdateStatus($request)) {
                $updateData['status'] = $validated['status'];
                $updateData['admin_remarks'] = $validated['admin_remarks'] ?? null;
                $updateData['reviewed_by'] = Auth::id();
                $updateData['reviewed_at'] = now();

                // Log status change
                $this->logApplicationActivity($application, 'status_changed', [
                    'old_status' => $application->status,
                    'new_status' => $validated['status']
                ]);
            }

            // Handle document replacement
            if ($request->hasFile('document')) {
                $this->deleteOldDocument($application);
                $documentData = $this->handleDocumentUpload($request);

                $updateData['document_url'] = $documentData['url'];
                $updateData['document_name'] = $documentData['name'];
                $updateData['document_size'] = $documentData['size'];
            }

            $application->update($updateData);

            // Send notification to student if status changed
            if (isset($validated['status']) && $validated['status'] !== $application->getOriginal('status')) {
                $this->notifyStudentStatusChange($application);
            }

            DB::commit();

            $message = isset($validated['status']) ?
                'Application reviewed successfully!' :
                'Application updated successfully!';

            return redirect()
                ->route('application.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Application update failed: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'request_data' => $request->except(['document']),
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update application. Please try again.']);
        }
    }

    /**
     * Remove the specified application.
     */
    public function destroy(Application $application): RedirectResponse
    {
        // Prevent deletion of approved applications
        if ($application->status === Application::STATUS_APPROVED) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Cannot delete approved applications.']);
        }

        DB::beginTransaction();

        try {
            // Delete associated document
            $this->deleteOldDocument($application);

            // Log deletion
            $this->logApplicationActivity($application, 'deleted');

            // Force delete (permanent deletion)
            $application->forceDelete();

            DB::commit();

            return redirect()
                ->route('application.index')
                ->with('success', 'Application deleted successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Application deletion failed: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'exception' => $e
            ]);

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete application. Please try again.']);
        }
    }

    /**
     * Bulk approve applications.
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_ids' => 'required|array|min:1',
            'application_ids.*' => 'exists:applications,id',
            'admin_remarks' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $applications = Application::whereIn('id', $validated['application_ids'])
                ->where('status', Application::STATUS_PENDING)
                ->get();

            $updatedCount = 0;
            foreach ($applications as $application) {
                $application->approve(Auth::id(), $validated['admin_remarks'] ?? null);
                $this->notifyStudentStatusChange($application);
                $updatedCount++;
            }

            DB::commit();

            return redirect()
                ->route('application.index')
                ->with('success', "Successfully approved {$updatedCount} applications.");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk approval failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to approve applications. Please try again.']);
        }
    }

    /**
     * Download application document.
     */
    public function downloadDocument(Application $application): \Symfony\Component\HttpFoundation\StreamedResponse|RedirectResponse
    {
        if (!$this->checkDocumentExists($application)) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Document not found.']);
        }

        $path = str_replace('/storage', 'public', $application->document_url);
        $fileName = $application->document_name ?: 'application_document.pdf';

        return Storage::download($path, $fileName);
    }

    /**
     * Validate application data for creation.
     */
    private function validateApplicationData(Request $request): array
    {
        return $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255|min:5',
            'description' => 'nullable|string|max:1000',
            'document' => [
                'required',
                'file',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'max:' . self::MAX_FILE_SIZE
            ]
        ], [
            'title.min' => 'Title must be at least 5 characters long.',
            'document.required' => 'A PDF document is required.',
            'document.mimes' => 'Only PDF files are allowed.',
            'document.max' => 'Document size cannot exceed 10MB.',
        ]);
    }

    /**
     * Validate application data for updates.
     */
    private function validateApplicationUpdate(Request $request, Application $application): array
    {
        $rules = [
            'title' => 'required|string|max:255|min:5',
            'description' => 'nullable|string|max:1000',
            'document' => [
                'nullable',
                'file',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'max:' . self::MAX_FILE_SIZE
            ]
        ];

        // Add status validation for admin users
        if ($this->canUpdateStatus($request)) {
            $rules['status'] = [
                'required',
                Rule::in([Application::STATUS_PENDING, Application::STATUS_APPROVED, Application::STATUS_REJECTED])
            ];
            $rules['admin_remarks'] = 'nullable|string|max:500';
        }

        return $request->validate($rules, [
            'title.min' => 'Title must be at least 5 characters long.',
            'document.mimes' => 'Only PDF files are allowed.',
            'document.max' => 'Document size cannot exceed 10MB.',
        ]);
    }

    /**
     * Handle document upload and return file data.
     */
    private function handleDocumentUpload(Request $request): array
    {
        $file = $request->file('document');
        $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('public/applications', $fileName);

        return [
            'url' => Storage::url($filePath),
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ];
    }

    /**
     * Delete old document file.
     */
    private function deleteOldDocument(Application $application): bool
    {
        if (!$application->document_url) {
            return true;
        }

        $path = str_replace('/storage', 'public', $application->document_url);
        return Storage::delete($path);
    }

    /**
     * Check if document file exists.
     */
    private function checkDocumentExists(Application $application): bool
    {
        if (!$application->document_url) {
            return false;
        }

        $path = str_replace('/storage', 'public', $application->document_url);
        return Storage::exists($path);
    }

    /**
     * Check if student has active application.
     */
    private function hasActiveApplication(int $studentId): bool
    {
        return Application::where('student_id', $studentId)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_APPROVED])
            ->exists();
    }

    /**
     * Check if user can update application status.
     */
    private function canUpdateStatus(Request $request): bool
    {
        return Auth::user()->role->value === 'ADMIN' && $request->has('status');
    }

    /**
     * Get application statistics for dashboard.
     */
    private function getApplicationStatistics(): array
    {
        return [
            'total' => Application::count(),
            'pending' => Application::pending()->count(),
            'approved' => Application::approved()->count(),
            'rejected' => Application::rejected()->count(),
            'overdue' => Application::overdue()->count(),
            'this_month' => Application::whereMonth('submission_date', now()->month)->count(),
            'avg_processing_time' => $this->getAverageProcessingTime(),
        ];
    }

    /**
     * Get average processing time in hours.
     */
    private function getAverageProcessingTime(): float
    {
        return Application::whereNotNull('reviewed_at')
            ->whereNotNull('submission_date')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submission_date, reviewed_at)) as avg_hours')
            ->value('avg_hours') ?? 0;
    }

    /**
     * Log application activity for audit trail.
     */
    private function logApplicationActivity(Application $application, string $action, array $details = []): void
    {
        Log::info("Application {$action}", [
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'user_id' => Auth::id(),
            'action' => $action,
            'details' => $details,
            'timestamp' => now()
        ]);
    }

    /**
     * Send notification to student about status change.
     */
    private function notifyStudentStatusChange(Application $application): void
    {
        // This would integrate with your notification system
        // For now, we'll just log it
        Log::info('Student notification sent', [
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'status' => $application->status,
            'timestamp' => now()
        ]);

        // You could implement email, SMS, or push notifications here
        // Example: Mail::to($application->student->user->email)->send(new ApplicationStatusChanged($application));
    }
}
