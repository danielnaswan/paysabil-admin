<?php

namespace App\Http\Controllers\ApiController;

use App\Enums\TransactionStatus;
use App\Models\Student;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**p
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
        $qrCode = $request->all();

        DB::beginTransaction();

        try {
            $matchQRCode = QRCode::where('code', $qrCode)->firstOrFail();

            $matchStudent = Student::where('matrix_no', $qrCode['matrix_no'])->firstOrFail();

            $isTransExist = Transaction::where('student_id', $matchStudent['id'])
                ->whereDate('created_at', today())
                ->exists();

            if (!$isTransExist) {
                $transactionData = Transaction::create([
                    'student_id' => $matchStudent['id'],
                    'vendor_id' => $matchQRCode['vendor_id'],
                    'qr_code_id' => $matchQRCode['id'],
                    'status' => TransactionStatus::COMPLETED->value,
                    'transaction_date' => now(),
                    'amount' => $matchQRCode['service_details']['price'],
                    'meal_details' => $matchQRCode['service_details']['service_name'],
                ]);
            } else {
                return response()->json([
                    'message' => 'Transaction existed today'
                ], 401);
            }

            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json([
                'message' => 'Transaction failed: ' . $th->getMessage(),
                'error' => true
            ], 500);
        }

        return response()->json([
            'transaction' => $transactionData,
            'data' => $matchQRCode  
        ], 200);
    }

    public function storeFeedback(Request $request) {
        //create api endpoint here to fetch data from flutter
        $request->validate([
            'student_id' => ['required'],
            'vendor_id' => ['required'],
            'stars' => ['required'],
            'review_comment' => ['required', 'max:150', 'string'],
        ]);

        $feedback = Rating::create([
            'stars' => $request->stars,
            'review_comment' => $request->review_comment,
            'student_id' => $request->student_id,
            'vendor_id' => $request->vendor_id,
            'review_date' => now(),
        ]);

        return response()->json([
            'message' => 'feedback submitted!',
            'feedback' => $feedback,
            'student' => $feedback->student,
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function showStudentParticipation()
    {
        $studentIds = Transaction::select('student_id')
            ->whereNotIn('student_id', function($query) {
                $query->select('student_id')
                      ->from('transactions')
                      ->where('transaction_date', '>=', now()->subDays(5))
                      ->where('transaction_date', '<', now())
                      ->whereNull('deleted_at');
            })
            ->whereNull('deleted_at')
            ->distinct()
            ->get()
            ->toArray();

        //create a collection of student's data

        $nonActiveStudents = Student::whereIn('id', $studentIds)->get();

        return view('pages.report.report-participation', ['students' => $nonActiveStudents]);
    }
    public function showFinancial(string $vendor, Request $request)//the month picker is not doing well ...
    {
        $month = $request->input('month');

        $vendorModel = Vendor::findOrFail($vendor);

        $query = Transaction::with(['student', 'qrCode', 'vendor'])
                    ->where('vendor_id', $vendor)
                    ->orderBy('transaction_date', 'desc');

        if ($month) {
            $query->whereYear('transaction_date', '=', date('Y', strtotime($month)))
                  ->whereMonth('transaction_date', '=', date('m', strtotime($month)));
        }

        $transactionData = $query->get();

        return view('pages.report.report-vendortransaction', compact('transactionData', 'vendorModel'));
    }
    public function showFeedback(Request $request, string $vendor) {
        //using rating model..
        $vendorModel = Vendor::findOrFail($vendor);


        $feedbacks = Rating::with(['student', 'vendor'])
        ->select('*')
        ->where('vendor_id', $vendor)
        ->get(); //select * from ratings where vendor_id = $vendor

        return view('pages.report.report-feedback', compact('feedbacks', 'vendorModel'));
    }
    public function showAnomaly()
    {
        $anomalies = Transaction::with(['student', 'vendor'])
                ->select('student_id', 'transaction_date', DB::raw('COUNT(*) as transaction_count'))
                ->groupBy('student_id', 'transaction_date')
                ->having('transaction_count', '>', 1)
                ->orderBy('transaction_date', 'desc')
                ->get();

        return view('pages.report.report-anomaly', compact( 'anomalies'));
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
