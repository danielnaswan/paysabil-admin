<?php

namespace App\Http\Controllers\ApiController;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\Vendor;
use App\Models\QrCode;
use App\Models\Rating;
use App\Models\Transaction;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TransactionController extends Controller
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
     * Dailty max limit for as student
     */
    private const DAILY_TRANSACTION_LIMIT = 1;
    /**
     * Dailty max limit for as student
     */
    private const MAX_RATING = 5;

    /**
     * Student Registration
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'matrix_no' => 'required|string|unique:students,matrix_no',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
                'phone_number' => 'required|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error' => true
                ], 422);
            }

            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role' => 'STUDENT'
            ]);

            // Create student profile
            $student = Student::create([
                'full_name' => $request->full_name,
                'matrix_no' => $request->matrix_no,
                'user_id' => $user->id
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Student registered successfully',
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'matrix_no' => $student->matrix_no,
                    'email' => $user->email
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Student registration failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Student Login
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'matrix_no' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error' => true
                ], 422);
            }

            // Find student by matrix number
            $student = Student::with('user')->where('matrix_no', $request->matrix_no)->first();

            if (!$student || !Hash::check($request->password, $student->user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                    'error' => true
                ], 401);
            }

            // Check if user is a student
            if ($student->user->role !== UserRole::STUDENT) {
                return response()->json([
                    'message' => 'Access denied. Only students can use this app.',
                    'error' => true
                ], 403);
            }

            // Create token
            $token = $student->user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $student->user->id,
                    'student_id' => $student->id,
                    'full_name' => $student->full_name,
                    'matrix_no' => $student->matrix_no,
                    'email' => $student->user->email,
                    'application_status' => $student->application_status,
                    'is_eligible' => $student->is_eligible
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Login failed: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Submit Application (One-time only)
     */
    public function submitApplication(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255|min:5',
                'description' => 'nullable|string|max:1000',
                'document' => [
                    'nullable',
                    'file',
                    'mimes:' . implode(',', self::ALLOWED_MIMES),
                    'max:' . self::MAX_FILE_SIZE
                ],
                'document_name' => 'nullable|string|max:255',
                'document_size' => 'nullable|integer|min:0'  // Add validation for document_size
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error' => true
                ], 422);
            }

            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            // Check if application already exists
            if ($student->application) {
                return response()->json([
                    'message' => 'Application already submitted. You can only apply once.',
                    'application_status' => $student->application->status,
                    'error' => true
                ], 409);
            }

            DB::beginTransaction();

            // Handle file upload
            $documentUrl = null;
            $documentName = null;
            $documentSize = 0;

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $documentUrl = $file->store('applications', 'public');
                $documentName = $request->input('document_name') ?: $file->getClientOriginalName();
                $documentSize = $request->input('document_size') ?: $file->getSize();
            }

            $application = Application::create([
                'title' => $request->title,
                'description' => $request->description ?: null,
                'student_id' => $student->id,
                'status' => 'PENDING',
                'submission_date' => now(),
                'document_url' => $documentUrl,
                'document_name' => $documentName,
                'document_size' => $documentSize  // Add document_size field
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Application submitted successfully',
                'application' => [
                    'id' => $application->id,
                    'title' => $application->title,
                    'status' => $application->status,
                    'submission_date' => $application->submission_date->format('Y-m-d H:i:s'),
                    'document_name' => $application->document_name,
                    'document_size' => $application->document_size
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Application submission failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Application submission failed: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Get Application Status
     */
    public function getApplicationStatus(Request $request)
    {
        try {
            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            $application = $student->application;

            if (!$application) {
                return response()->json([
                    'message' => 'No application found',
                    'has_application' => false,
                    'can_apply' => true
                ]);
            }

            return response()->json([
                'message' => 'Application status retrieved',
                'has_application' => true,
                'can_apply' => false,
                'application' => [
                    'id' => $application->id,
                    'status' => $application->status,
                    'submission_date' => $application->submission_date->format('Y-m-d H:i:s'),
                    'feedback' => $application->feedback ?? null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get application status: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get application status',
                'error' => true
            ], 500);
        }
    }

    /**
     * Process QR Code Transaction
     */
    public function processTransaction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'qr_code' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'QR code is required',
                    'error' => true
                ], 422);
            }

            DB::beginTransaction();

            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            // Check if student has approved application
            if (!$student->is_eligible) {
                return response()->json([
                    'message' => 'You are not eligible for meal claims. Please submit your application and wait for approval.',
                    'error' => true
                ], 403);
            }

            // Find and validate QR code
            $qrCode = QrCode::with(['vendor', 'service'])
                ->where('code', $request->qr_code)
                ->first();

            if (!$qrCode) {
                return response()->json([
                    'message' => 'Invalid QR code',
                    'error' => true
                ], 404);
            }

            // Check QR code status
            if ($qrCode->status !== 'ACTIVE') {
                return response()->json([
                    'message' => 'QR code is not active or has been used',
                    'error' => true
                ], 400);
            }

            // Check QR code expiry
            if ($qrCode->expiry_date && $qrCode->expiry_date < now()) {
                $qrCode->update(['status' => 'EXPIRED']);
                return response()->json([
                    'message' => 'QR code has expired',
                    'error' => true
                ], 400);
            }

            // Check daily transaction limit
            $todayTransactions = Transaction::where('student_id', $student->id)
                ->whereDate('transaction_date', today())
                ->where('status', 'COMPLETED')
                ->count();

            if ($todayTransactions >= self::DAILY_TRANSACTION_LIMIT) {
                return response()->json([
                    'message' => 'Daily transaction limit reached. You can only claim one meal per day.',
                    'error' => true
                ], 409);
            }

            // Create transaction
            $transaction = Transaction::create([
                'student_id' => $student->id,
                'vendor_id' => $qrCode->vendor_id,
                'qr_code_id' => $qrCode->id,
                'status' => 'COMPLETED',
                'transaction_date' => now(),
                'amount' => $qrCode->service_details['price'] ?? 0,
                'meal_details' => $qrCode->service_details['service_name'] ?? 'Meal Claim'
            ]);

            // Update QR code status
            $qrCode->update(['status' => 'USED']);

            DB::commit();

            return response()->json([
                'message' => 'Transaction completed successfully',
                'transaction' => [
                    'id' => $transaction->id,
                    'vendor_name' => $qrCode->vendor->business_name,
                    'service_name' => $qrCode->service_details['service_name'] ?? 'Meal Claim',
                    'amount' => $transaction->amount,
                    'date' => $transaction->transaction_date->format('Y-m-d H:i:s'),
                    'status' => $transaction->status
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Transaction failed: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Get Transaction History
     */
    public function getTransactionHistory(Request $request)
    {
        try {
            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            $transactions = Transaction::with(['vendor', 'qrCode.service'])
                ->where('student_id', $student->id)
                ->orderBy('transaction_date', 'desc')
                ->paginate(20);

            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'vendor_name' => $transaction->vendor->business_name,
                    'meal_details' => $transaction->meal_details,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'date' => $transaction->transaction_date->format('Y-m-d H:i:s'),
                    'formatted_date' => $transaction->transaction_date->format('M d, Y g:i A')
                ];
            });

            return response()->json([
                'message' => 'Transaction history retrieved successfully',
                'transactions' => $formattedTransactions,
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'total' => $transactions->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get transaction history: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get transaction history',
                'error' => true
            ], 500);
        }
    }

    /**
     * Submit Feedback (One-time per vendor)
     */
    public function submitFeedback(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vendor_id' => 'required|exists:vendors,id',
                'stars' => 'required|integer|min:1|max:5',
                'review_comment' => 'required|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'error' => true
                ], 422);
            }

            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            // Check if feedback already exists
            $existingRating = Rating::where('student_id', $student->id)
                ->where('vendor_id', $request->vendor_id)
                ->first();

            if ($existingRating) {
                return response()->json([
                    'message' => 'You have already submitted feedback for this vendor',
                    'error' => true
                ], 409);
            }

            DB::beginTransaction();

            // Create rating
            $rating = Rating::create([
                'student_id' => $student->id,
                'vendor_id' => $request->vendor_id,
                'stars' => $request->stars,
                'review_comment' => $request->review_comment,
                'review_date' => now()
            ]);

            // Update vendor's average rating
            $this->updateVendorRating($request->vendor_id);

            DB::commit();

            return response()->json([
                'message' => 'Feedback submitted successfully',
                'rating' => [
                    'id' => $rating->id,
                    'stars' => $rating->stars,
                    'review_comment' => $rating->review_comment,
                    'review_date' => $rating->review_date->format('Y-m-d H:i:s')
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Feedback submission failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Feedback submission failed: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Get Available Vendors for Feedback
     */
    public function getVendorsForFeedback(Request $request)
    {
        try {
            $student = $request->user()->student;
            if (!$student) {
                return response()->json([
                    'message' => 'Student profile not found',
                    'error' => true
                ], 404);
            }

            // Get vendors from student's completed transactions
            $vendorIds = Transaction::where('student_id', $student->id)
                ->where('status', 'COMPLETED')
                ->distinct()
                ->pluck('vendor_id');

            // Get vendors not yet rated by this student
            $ratedVendorIds = Rating::where('student_id', $student->id)
                ->pluck('vendor_id');

            $availableVendors = Vendor::whereIn('id', $vendorIds)
                ->whereNotIn('id', $ratedVendorIds)
                ->get(['id', 'business_name', 'service_category', 'average_rating']);

            return response()->json([
                'message' => 'Available vendors for feedback retrieved',
                'vendors' => $availableVendors
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get vendors for feedback: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get vendors for feedback',
                'error' => true
            ], 500);
        }
    }

    /**
     * Update vendor's average rating
     */
    private function updateVendorRating(int $vendorId): void
    {
        $vendor = Vendor::find($vendorId);
        if ($vendor) {
            $ratings = Rating::where('vendor_id', $vendorId);
            $vendor->update([
                'average_rating' => $ratings->avg('stars') ?? 0,
                'total_reviews' => $ratings->count()
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Logout failed',
                'error' => true
            ], 500);
        }
    }
}
