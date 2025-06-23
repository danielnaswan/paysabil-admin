<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Vendor;
use App\Models\Transaction;
use App\Models\Rating;
use App\Models\Report;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Report type constants
     */
    private const REPORT_STUDENT_PARTICIPATION = 'student_participation';
    private const REPORT_FINANCIAL = 'financial';
    private const REPORT_ANOMALY = 'anomaly';
    private const REPORT_FEEDBACK = 'feedback';

    /**
     * Inactivity threshold in days
     */
    private const INACTIVITY_THRESHOLD_DAYS = 7;

    /**
     * Show report options/menu page
     */
    public function showReportOptions(): View
    {
        $reportTypes = $this->getAvailableReportTypes();
        $recentReports = Report::with('admin.user')
            ->latest('generated_date')
            ->take(5)
            ->get();

        return view('pages.report.report-option', compact('reportTypes', 'recentReports'));
    }

    /**
     * Display the main reports dashboard
     */
    public function index(): View
    {
        $statistics = $this->getReportStatistics();
        return view('pages.report.report-dashboard', compact('statistics'));
    }

    /**
     * Student Participation Report - Shows inactive students
     */
    public function showStudentParticipation(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'days_threshold' => 'nullable|integer|min:1|max:30',
            'export' => 'nullable|in:pdf,excel,csv'
        ]);

        $daysThreshold = (int) ($validated['days_threshold'] ?? self::INACTIVITY_THRESHOLD_DAYS);

        try {
            // Get inactive students with their last transactions
            $inactiveStudents = $this->getInactiveStudents($daysThreshold);

            // Get participation statistics
            $statistics = $this->getParticipationStatistics($daysThreshold);

            // Log report generation
            $this->logReportGeneration(self::REPORT_STUDENT_PARTICIPATION, [
                'days_threshold' => $daysThreshold,
                'inactive_count' => $inactiveStudents->count()
            ]);

            // Handle export requests
            if (isset($validated['export'])) {
                return $this->exportStudentParticipation($inactiveStudents, $validated['export'], $daysThreshold);
            }

            // Return view with data
            return view('pages.report.report-participation', compact(
                'inactiveStudents',
                'daysThreshold',
                'statistics'
            ));
        } catch (\Exception $e) {
            Log::error('Student participation report failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'threshold' => $daysThreshold
            ]);

            return redirect()
                ->route('report.index')
                ->withErrors(['error' => 'Failed to generate student participation report. Please try again.']);
        }
    }

    /**
     * Financial Report - Shows vendor revenue and financial data
     */
    public function showFinancial(Request $request, ?int $vendor = null)
    {
        // If no vendor specified, show vendor selection
        if (!$vendor) {
            $vendors = Vendor::with('user')
                ->whereHas('services.qrCodes.transactions', function ($query) {
                    $query->where('status', 'COMPLETED');
                })
                ->withCount(['services as completed_transactions_count' => function ($query) {
                    $query->join('qr_codes', 'services.id', '=', 'qr_codes.service_id')
                        ->join('transactions', 'qr_codes.id', '=', 'transactions.qr_code_id')
                        ->where('transactions.status', 'COMPLETED');
                }])
                ->orderBy('business_name')
                ->get();

            return view('pages.report.report-vendorlist', compact('vendors'));
        }

        // Validate input for specific vendor report
        $validated = $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'export' => 'nullable|in:pdf,excel,csv'
        ]);

        try {
            $vendorModel = Vendor::with('user')->findOrFail($vendor);

            // Build transactions query
            $transactionData = $this->getVendorTransactions($vendor, $validated);
            $financialSummary = $this->calculateFinancialSummary($transactionData);
            $periodInfo = $this->getPeriodInfo($validated);

            // Log report generation
            $this->logReportGeneration(self::REPORT_FINANCIAL, [
                'vendor_id' => $vendor,
                'period' => $periodInfo,
                'transaction_count' => $transactionData->count(),
                'total_revenue' => $financialSummary['total_revenue']
            ]);

            // Handle export requests
            if (isset($validated['export'])) {
                return $this->exportFinancialReport($transactionData, $vendorModel, $validated['export'], $periodInfo);
            }

            return view('pages.report.report-vendortransaction', compact(
                'transactionData',
                'vendorModel',
                'financialSummary',
                'periodInfo'
            ));
        } catch (\Exception $e) {
            Log::error('Financial report failed: ' . $e->getMessage(), [
                'vendor_id' => $vendor,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('report.financial')
                ->withErrors(['error' => 'Failed to generate financial report. Please try again.']);
        }
    }

    /**
     * Anomaly Report - Detects unusual transaction patterns
     */
    public function showAnomaly(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'severity' => 'nullable|in:all,high,medium',
            'export' => 'nullable|in:pdf,excel,csv'
        ]);

        try {
            // Get anomalies (students with multiple transactions per day)
            $anomalies = $this->getTransactionAnomalies($validated);

            // Enhance anomaly data with additional details
            $enhancedAnomalies = $this->enhanceAnomalyData($anomalies);
            $anomalyStatistics = $this->getAnomalyStatistics($anomalies);

            // Log report generation
            $this->logReportGeneration(self::REPORT_ANOMALY, [
                'anomaly_count' => $anomalies->count(),
                'date_range' => [
                    'start' => $validated['start_date'] ?? now()->subDays(30)->toDateString(),
                    'end' => $validated['end_date'] ?? now()->toDateString()
                ]
            ]);

            // Handle export requests
            if (isset($validated['export'])) {
                return $this->exportAnomalyReport($enhancedAnomalies, $validated['export']);
            }

            return view('pages.report.report-anomaly', compact(
                'anomalies',
                'enhancedAnomalies',
                'anomalyStatistics',
                'validated'
            ));
        } catch (\Exception $e) {
            Log::error('Anomaly report failed: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('report.index')
                ->withErrors(['error' => 'Failed to generate anomaly report. Please try again.']);
        }
    }

    /**
     * Feedback Report - Shows customer feedback and ratings
     */
    public function showFeedback(Request $request, ?int $vendor = null)
    {
        // If no vendor specified, show vendor selection
        if (!$vendor) {
            $vendors = Vendor::with('user')
                ->whereHas('ratings')
                ->withCount('ratings')
                ->withAvg('ratings', 'stars')
                ->orderBy('business_name')
                ->get();

            return view('pages.report.report-vendorfeedback', compact('vendors'));
        }

        $validated = $request->validate([
            'rating_filter' => 'nullable|in:all,positive,neutral,negative',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'export' => 'nullable|in:pdf,excel,csv'
        ]);

        try {
            $vendorModel = Vendor::with('user')->findOrFail($vendor);

            // Get filtered feedbacks
            $feedbacks = $this->getVendorFeedbacks($vendor, $validated);
            $feedbackSummary = $this->calculateFeedbackSummary($feedbacks);
            $trendAnalysis = $this->analyzeFeedbackTrends($vendor);

            // Log report generation
            $this->logReportGeneration(self::REPORT_FEEDBACK, [
                'vendor_id' => $vendor,
                'feedback_count' => $feedbacks->count(),
                'average_rating' => $feedbackSummary['average_rating']
            ]);

            // Handle export requests
            if (isset($validated['export'])) {
                return $this->exportFeedbackReport($feedbacks, $vendorModel, $validated['export']);
            }

            return view('pages.report.report-feedback', compact(
                'feedbacks',
                'vendorModel',
                'feedbackSummary',
                'trendAnalysis',
                'validated'
            ));
        } catch (\Exception $e) {
            Log::error('Feedback report failed: ' . $e->getMessage(), [
                'vendor_id' => $vendor,
                'user_id' => Auth::id()
            ]);

            return redirect()
                ->route('report.feedback')
                ->withErrors(['error' => 'Failed to generate feedback report. Please try again.']);
        }
    }

    /**
     * Get inactive students based on threshold
     */
    private function getInactiveStudents(int $daysThreshold)
    {
        return Student::with(['user', 'application'])
            ->whereHas('application', function ($query) {
                $query->where('status', 'APPROVED');
            })
            ->whereDoesntHave('transactions', function ($query) use ($daysThreshold) {
                $query->where('transaction_date', '>=', now()->subDays($daysThreshold))
                    ->where('status', 'COMPLETED');
            })
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Get participation statistics
     */
    private function getParticipationStatistics(int $daysThreshold): array
    {
        $totalEligibleStudents = Student::whereHas('application', function ($query) {
            $query->where('status', 'APPROVED');
        })->count();

        $activeStudents = Student::whereHas('application', function ($query) {
            $query->where('status', 'APPROVED');
        })->whereHas('transactions', function ($query) use ($daysThreshold) {
            $query->where('transaction_date', '>=', now()->subDays($daysThreshold))
                ->where('status', 'COMPLETED');
        })->count();

        $inactiveStudents = $totalEligibleStudents - $activeStudents;
        $participationRate = $totalEligibleStudents > 0 ?
            ($activeStudents / $totalEligibleStudents) * 100 : 0;

        return [
            'total_eligible' => $totalEligibleStudents,
            'active_students' => $activeStudents,
            'inactive_students' => $inactiveStudents,
            'participation_rate' => round($participationRate, 2),
            'threshold_days' => $daysThreshold
        ];
    }

    /**
     * Get vendor transactions based on filters
     */
    private function getVendorTransactions(int $vendorId, array $filters)
    {
        $query = Transaction::with(['student.user', 'qrCode.service', 'vendor'])
            ->where('vendor_id', $vendorId)
            ->where('status', 'COMPLETED')
            ->orderBy('transaction_date', 'desc');

        // Apply date filters
        if (isset($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month);
        } elseif (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('transaction_date', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        } else {
            // Default to current month
            $query->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year);
        }

        return $query->get();
    }

    /**
     * Get transaction anomalies
     */
    private function getTransactionAnomalies(array $filters)
    {
        $query = Transaction::with(['student.user', 'vendor'])
            ->select('student_id', DB::raw('DATE(transaction_date) as transaction_date'), DB::raw('COUNT(*) as transaction_count'))
            ->where('status', 'COMPLETED')
            ->groupBy('student_id', DB::raw('DATE(transaction_date)'))
            ->having('transaction_count', '>', 1);

        // Apply date filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('transaction_date', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        } else {
            $query->where('transaction_date', '>=', now()->subDays(30));
        }

        // Apply severity filter
        if (isset($filters['severity'])) {
            switch ($filters['severity']) {
                case 'high':
                    $query->having('transaction_count', '>', 3);
                    break;
                case 'medium':
                    $query->having('transaction_count', '=', 2);
                    break;
            }
        }

        return $query->orderBy('transaction_date', 'desc')
            ->orderBy('transaction_count', 'desc')
            ->get();
    }

    /**
     * Get vendor feedbacks based on filters
     */
    private function getVendorFeedbacks(int $vendorId, array $filters)
    {
        $query = Rating::with(['student.user', 'vendor'])
            ->where('vendor_id', $vendorId);

        // Apply date filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('review_date', [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }

        // Apply rating filters
        if (isset($filters['rating_filter'])) {
            switch ($filters['rating_filter']) {
                case 'positive':
                    $query->where('stars', '>=', 4);
                    break;
                case 'neutral':
                    $query->where('stars', 3);
                    break;
                case 'negative':
                    $query->where('stars', '<=', 2);
                    break;
            }
        }

        return $query->orderBy('review_date', 'desc')->get();
    }

    /**
     * Calculate financial summary
     */
    private function calculateFinancialSummary($transactions): array
    {
        $totalRevenue = $transactions->sum('amount');
        $totalTransactions = $transactions->count();
        $averageTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        $uniqueStudents = $transactions->pluck('student_id')->unique()->count();

        $dailyRevenue = $transactions->groupBy(function ($transaction) {
            return $transaction->transaction_date->format('Y-m-d');
        })->map(function ($dayTransactions) {
            return [
                'revenue' => $dayTransactions->sum('amount'),
                'count' => $dayTransactions->count()
            ];
        });

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction_value' => round($averageTransactionValue, 2),
            'unique_students' => $uniqueStudents,
            'daily_breakdown' => $dailyRevenue
        ];
    }

    /**
     * Enhance anomaly data with additional details
     */
    private function enhanceAnomalyData($anomalies)
    {
        return $anomalies->map(function ($anomaly) {
            $student = Student::with('user')->find($anomaly->student_id);
            $dayTransactions = Transaction::with(['vendor', 'qrCode.service'])
                ->where('student_id', $anomaly->student_id)
                ->whereDate('transaction_date', $anomaly->transaction_date)
                ->where('status', 'COMPLETED')
                ->get();

            return [
                'student' => $student,
                'transaction_date' => $anomaly->transaction_date,
                'transaction_count' => $anomaly->transaction_count,
                'transactions' => $dayTransactions,
                'total_amount' => $dayTransactions->sum('amount'),
                'vendors_involved' => $dayTransactions->pluck('vendor.business_name')->unique(),
                'severity' => $this->calculateAnomalySeverity($anomaly->transaction_count)
            ];
        });
    }

    /**
     * Calculate anomaly severity
     */
    private function calculateAnomalySeverity(int $transactionCount): string
    {
        if ($transactionCount >= 4) return 'high';
        if ($transactionCount == 3) return 'medium';
        return 'low';
    }

    /**
     * Get anomaly statistics
     */
    private function getAnomalyStatistics($anomalies): array
    {
        $totalAnomalies = $anomalies->count();
        $uniqueStudents = $anomalies->pluck('student_id')->unique()->count();

        $severityBreakdown = $anomalies->groupBy(function ($anomaly) {
            return $this->calculateAnomalySeverity($anomaly->transaction_count);
        })->map->count();

        return [
            'total_anomalies' => $totalAnomalies,
            'unique_students_affected' => $uniqueStudents,
            'severity_breakdown' => $severityBreakdown->toArray(),
            'average_violations_per_student' => $uniqueStudents > 0 ?
                round($totalAnomalies / $uniqueStudents, 2) : 0
        ];
    }

    /**
     * Calculate feedback summary
     */
    private function calculateFeedbackSummary($feedbacks): array
    {
        $totalFeedbacks = $feedbacks->count();
        $averageRating = $feedbacks->avg('stars') ?? 0;

        $ratingDistribution = $feedbacks->groupBy('stars')->map->count();
        $sentimentBreakdown = [
            'positive' => $feedbacks->where('stars', '>=', 4)->count(),
            'neutral' => $feedbacks->where('stars', 3)->count(),
            'negative' => $feedbacks->where('stars', '<=', 2)->count()
        ];

        $responseRate = $feedbacks->whereNotNull('vendor_response')->count();
        $responsePercentage = $totalFeedbacks > 0 ? ($responseRate / $totalFeedbacks) * 100 : 0;

        return [
            'total_feedbacks' => $totalFeedbacks,
            'average_rating' => round($averageRating, 2),
            'rating_distribution' => $ratingDistribution->toArray(),
            'sentiment_breakdown' => $sentimentBreakdown,
            'response_rate' => round($responsePercentage, 2),
            'latest_feedback_date' => $feedbacks->max('review_date')
        ];
    }

    /**
     * Analyze feedback trends
     */
    private function analyzeFeedbackTrends(int $vendorId): array
    {
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthRatings = Rating::where('vendor_id', $vendorId)
                ->whereBetween('review_date', [$monthStart, $monthEnd])
                ->get();

            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'average_rating' => round($monthRatings->avg('stars') ?? 0, 2),
                'total_ratings' => $monthRatings->count(),
                'positive_percentage' => $monthRatings->count() > 0 ?
                    round(($monthRatings->where('stars', '>=', 4)->count() / $monthRatings->count()) * 100, 1) : 0
            ];
        }

        return [
            'monthly_trends' => $monthlyTrends,
            'trend_direction' => $this->calculateTrendDirection($monthlyTrends)
        ];
    }

    /**
     * Calculate trend direction
     */
    private function calculateTrendDirection(array $monthlyTrends): string
    {
        if (count($monthlyTrends) < 2) return 'stable';

        $recent = end($monthlyTrends)['average_rating'];
        $previous = prev($monthlyTrends)['average_rating'];

        if ($recent > $previous + 0.2) return 'improving';
        if ($recent < $previous - 0.2) return 'declining';
        return 'stable';
    }

    /**
     * Get period information
     */
    private function getPeriodInfo(array $validated): array
    {
        if (isset($validated['month'])) {
            $date = Carbon::createFromFormat('Y-m', $validated['month']);
            return [
                'type' => 'month',
                'description' => $date->format('F Y'),
                'start_date' => $date->startOfMonth()->toDateString(),
                'end_date' => $date->endOfMonth()->toDateString()
            ];
        } elseif (isset($validated['start_date']) && isset($validated['end_date'])) {
            return [
                'type' => 'custom',
                'description' => "From {$validated['start_date']} to {$validated['end_date']}",
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date']
            ];
        } else {
            return [
                'type' => 'current_month',
                'description' => now()->format('F Y'),
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString()
            ];
        }
    }

    /**
     * Get available report types
     */
    private function getAvailableReportTypes(): array
    {
        return [
            self::REPORT_STUDENT_PARTICIPATION => [
                'title' => 'Student Participation Report',
                'description' => 'Identify inactive students and monitor program participation',
                'icon' => 'fas fa-users',
                'route' => 'report.participation'
            ],
            self::REPORT_FINANCIAL => [
                'title' => 'Financial & Budget Report',
                'description' => 'Track vendor revenue and financial performance',
                'icon' => 'fas fa-chart-line',
                'route' => 'report.financial'
            ],
            self::REPORT_ANOMALY => [
                'title' => 'Anomaly Detection Report',
                'description' => 'Detect unusual transaction patterns and potential fraud',
                'icon' => 'fas fa-exclamation-triangle',
                'route' => 'report.anomaly'
            ],
            self::REPORT_FEEDBACK => [
                'title' => 'Feedback & Complaints Report',
                'description' => 'Monitor customer satisfaction and service quality',
                'icon' => 'fas fa-comments',
                'route' => 'report.feedback'
            ]
        ];
    }

    /**
     * Get general report statistics
     */
    private function getReportStatistics(): array
    {
        return [
            'total_reports_generated' => Report::count(),
            'reports_this_month' => Report::whereMonth('generated_date', now()->month)->count(),
            'most_requested_report' => $this->getMostRequestedReportType(),
            'active_students' => Student::whereHas('transactions', function ($query) {
                $query->where('transaction_date', '>=', now()->subDays(7))
                    ->where('status', 'COMPLETED');
            })->count(),
            'total_vendors' => Vendor::count(),
            'total_transactions_this_month' => Transaction::whereMonth('transaction_date', now()->month)
                ->where('status', 'COMPLETED')->count()
        ];
    }

    /**
     * Get most requested report type
     */
    private function getMostRequestedReportType(): string
    {
        $mostRequested = Report::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->first();

        return $mostRequested ? ucwords(str_replace('_', ' ', $mostRequested->type)) : 'N/A';
    }

    /**
     * Log report generation for audit
     */
    private function logReportGeneration(string $reportType, array $parameters = []): void
    {
        try {
            $adminId = Auth::user()->admin?->id ?? 1; // Fallback for testing

            Report::create([
                'type' => $reportType,
                'generated_date' => now(),
                'format' => 'web_view',
                'parameters' => $parameters,
                'admin_id' => $adminId
            ]);

            Log::info("Report generated: {$reportType}", [
                'user_id' => Auth::id(),
                'admin_id' => $adminId,
                'report_type' => $reportType,
                'parameters' => $parameters,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log report generation: ' . $e->getMessage(), [
                'report_type' => $reportType,
                'user_id' => Auth::id()
            ]);
        }
    }

    /**
     * Export student participation report
     */
    private function exportStudentParticipation($students, string $format, int $threshold)
    {
        $filename = "student_participation_report_" . now()->format('Y_m_d_H_i_s');

        switch ($format) {
            case 'csv':
                return $this->exportStudentParticipationCSV($students, $filename, $threshold);
            case 'excel':
                return $this->exportStudentParticipationExcel($students, $filename, $threshold);
            case 'pdf':
                return $this->exportStudentParticipationPDF($students, $filename, $threshold);
            default:
                return response()->json(['error' => 'Invalid export format'], 400);
        }
    }

    /**
     * Export financial report
     */
    private function exportFinancialReport($transactions, $vendor, string $format, array $periodInfo)
    {
        $filename = "financial_report_" . str_replace(' ', '_', $vendor->business_name) . "_" . now()->format('Y_m_d_H_i_s');

        switch ($format) {
            case 'csv':
                return $this->exportFinancialCSV($transactions, $filename, $vendor, $periodInfo);
            case 'excel':
                return $this->exportFinancialExcel($transactions, $filename, $vendor, $periodInfo);
            case 'pdf':
                return $this->exportFinancialPDF($transactions, $filename, $vendor, $periodInfo);
            default:
                return response()->json(['error' => 'Invalid export format'], 400);
        }
    }

    /**
     * Export anomaly report
     */
    private function exportAnomalyReport($anomalies, string $format)
    {
        $filename = "anomaly_report_" . now()->format('Y_m_d_H_i_s');

        switch ($format) {
            case 'csv':
                return $this->exportAnomalyCSV($anomalies, $filename);
            case 'excel':
                return $this->exportAnomalyExcel($anomalies, $filename);
            case 'pdf':
                return $this->exportAnomalyPDF($anomalies, $filename);
            default:
                return response()->json(['error' => 'Invalid export format'], 400);
        }
    }

    /**
     * Export feedback report
     */
    private function exportFeedbackReport($feedbacks, $vendor, string $format)
    {
        $filename = "feedback_report_" . str_replace(' ', '_', $vendor->business_name) . "_" . now()->format('Y_m_d_H_i_s');

        switch ($format) {
            case 'csv':
                return $this->exportFeedbackCSV($feedbacks, $filename, $vendor);
            case 'excel':
                return $this->exportFeedbackExcel($feedbacks, $filename, $vendor);
            case 'pdf':
                return $this->exportFeedbackPDF($feedbacks, $filename, $vendor);
            default:
                return response()->json(['error' => 'Invalid export format'], 400);
        }
    }

    /**
     * Export student participation to CSV
     */
    private function exportStudentParticipationCSV($students, string $filename, int $threshold)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($students, $threshold) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add header
            fputcsv($file, [
                'No',
                'Full Name',
                'Matrix Number',
                'Email',
                'Phone',
                'Application Status',
                'Last Transaction Date',
                'Days Inactive'
            ]);

            foreach ($students as $index => $student) {
                $lastTransaction = $student->transactions()
                    ->where('status', 'COMPLETED')
                    ->latest('transaction_date')
                    ->first();

                $daysInactive = $lastTransaction ?
                    $lastTransaction->transaction_date->diffInDays(now()) :
                    'Never active';

                fputcsv($file, [
                    $index + 1,
                    $student->full_name,
                    $student->matrix_no,
                    $student->user?->email ?? 'N/A',
                    $student->user?->phone_number ?? 'N/A',
                    $student->application?->status ?? 'No Application',
                    $lastTransaction ? $lastTransaction->transaction_date->format('Y-m-d H:i:s') : 'Never',
                    $daysInactive
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export student participation to Excel
     */
    private function exportStudentParticipationExcel($students, string $filename, int $threshold)
    {
        // For simplicity, return CSV with Excel MIME type
        $csvResponse = $this->exportStudentParticipationCSV($students, $filename, $threshold);

        return response($csvResponse->getContent(), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Export student participation to PDF
     */
    private function exportStudentParticipationPDF($students, string $filename, int $threshold)
    {
        $html = $this->generateStudentParticipationHTML($students, $threshold);

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate HTML for student participation report
     */
    private function generateStudentParticipationHTML($students, int $threshold): string
    {
        $generatedDate = now()->format('F d, Y \a\t H:i');
        $count = $students->count();

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Student Participation Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .meta { color: #666; font-size: 12px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #007bff; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Pay Sabil Al-Hikmah</h1>
                <h2>Student Participation Report</h2>
                <div class='meta'>Generated on {$generatedDate}</div>
            </div>
            
            <div class='summary'>
                <h3>Report Summary</h3>
                <p><strong>Inactivity Threshold:</strong> {$threshold} days</p>
                <p><strong>Inactive Students Found:</strong> {$count}</p>
                <p><strong>Report Period:</strong> Students with no transactions in the last {$threshold} days</p>
            </div>
        ";

        if ($count > 0) {
            $html .= "<table>";
            $html .= "<tr><th>No</th><th>Name</th><th>Matrix No</th><th>Email</th><th>Phone</th><th>Status</th><th>Last Transaction</th></tr>";

            foreach ($students as $index => $student) {
                $lastTransaction = $student->transactions()
                    ->where('status', 'COMPLETED')
                    ->latest('transaction_date')
                    ->first();

                $html .= "<tr>";
                $html .= "<td>" . ($index + 1) . "</td>";
                $html .= "<td>{$student->full_name}</td>";
                $html .= "<td>{$student->matrix_no}</td>";
                $html .= "<td>" . ($student->user?->email ?? 'N/A') . "</td>";
                $html .= "<td>" . ($student->user?->phone_number ?? 'N/A') . "</td>";
                $html .= "<td>" . ($student->application?->status ?? 'No Application') . "</td>";
                $html .= "<td>" . ($lastTransaction ? $lastTransaction->transaction_date->format('M d, Y') : 'Never') . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html .= "<div style='text-align: center; padding: 40px;'>";
            $html .= "<h3 style='color: #28a745;'>Excellent Participation!</h3>";
            $html .= "<p>All eligible students have been active in the last {$threshold} days.</p>";
            $html .= "</div>";
        }

        $html .= "</body></html>";
        return $html;
    }

    /**
     * Export financial report to CSV
     */
    private function exportFinancialCSV($transactions, string $filename, $vendor, array $periodInfo)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($transactions, $vendor, $periodInfo) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add header info
            fputcsv($file, ['Financial Report for: ' . $vendor->business_name]);
            fputcsv($file, ['Period: ' . $periodInfo['description']]);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row

            // Add column headers
            fputcsv($file, [
                'Transaction Date',
                'Student Name',
                'Matrix Number',
                'Service/Meal',
                'Amount (RM)',
                'Status',
                'QR Code Used'
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d H:i:s'),
                    $transaction->student->full_name ?? 'N/A',
                    $transaction->student->matrix_no ?? 'N/A',
                    $transaction->qrCode?->service?->name ?? $transaction->meal_details,
                    number_format($transaction->amount, 2),
                    $transaction->status,
                    $transaction->qrCode?->code ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export financial report to Excel
     */
    private function exportFinancialExcel($transactions, string $filename, $vendor, array $periodInfo)
    {
        $csvResponse = $this->exportFinancialCSV($transactions, $filename, $vendor, $periodInfo);

        return response($csvResponse->getContent(), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Export financial report to PDF
     */
    private function exportFinancialPDF($transactions, string $filename, $vendor, array $periodInfo)
    {
        $html = $this->generateFinancialHTML($transactions, $vendor, $periodInfo);

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate HTML for financial report
     */
    private function generateFinancialHTML($transactions, $vendor, array $periodInfo): string
    {
        $generatedDate = now()->format('F d, Y \a\t H:i');
        $summary = $this->calculateFinancialSummary($transactions);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Financial Report - {$vendor->business_name}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .meta { color: #666; font-size: 12px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #28a745; }
                .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Pay Sabil Al-Hikmah</h1>
                <h2>Financial Report</h2>
                <h3>{$vendor->business_name}</h3>
                <div class='meta'>Generated on {$generatedDate}</div>
            </div>
            
            <div class='summary'>
                <h3>Financial Summary</h3>
                <div class='summary-grid'>
                    <div>
                        <p><strong>Period:</strong> {$periodInfo['description']}</p>
                        <p><strong>Total Revenue:</strong> RM " . number_format($summary['total_revenue'], 2) . "</p>
                        <p><strong>Total Transactions:</strong> {$summary['total_transactions']}</p>
                    </div>
                    <div>
                        <p><strong>Average Transaction:</strong> RM " . number_format($summary['average_transaction_value'], 2) . "</p>
                        <p><strong>Unique Students:</strong> {$summary['unique_students']}</p>
                        <p><strong>Vendor:</strong> {$vendor->business_name}</p>
                    </div>
                </div>
            </div>
        ";

        if ($transactions->count() > 0) {
            $html .= "<table>";
            $html .= "<tr><th>Date</th><th>Student</th><th>Matrix No</th><th>Service</th><th>Amount</th><th>Status</th></tr>";

            foreach ($transactions as $transaction) {
                $html .= "<tr>";
                $html .= "<td>" . $transaction->transaction_date->format('M d, Y H:i') . "</td>";
                $html .= "<td>" . ($transaction->student->full_name ?? 'N/A') . "</td>";
                $html .= "<td>" . ($transaction->student->matrix_no ?? 'N/A') . "</td>";
                $html .= "<td>" . ($transaction->qrCode?->service?->name ?? $transaction->meal_details) . "</td>";
                $html .= "<td>RM " . number_format($transaction->amount, 2) . "</td>";
                $html .= "<td>" . $transaction->status . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html .= "<div style='text-align: center; padding: 40px;'>";
            $html .= "<h3>No Transactions Found</h3>";
            $html .= "<p>No transactions found for the selected period.</p>";
            $html .= "</div>";
        }

        $html .= "</body></html>";
        return $html;
    }

    /**
     * Export anomaly report to CSV
     */
    private function exportAnomalyCSV($anomalies, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($anomalies) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add headers
            fputcsv($file, [
                'Date',
                'Student Name',
                'Matrix Number',
                'Transaction Count',
                'Total Amount (RM)',
                'Severity',
                'Vendors Involved'
            ]);

            foreach ($anomalies as $anomaly) {
                fputcsv($file, [
                    $anomaly['transaction_date'],
                    $anomaly['student']->full_name,
                    $anomaly['student']->matrix_no,
                    $anomaly['transaction_count'],
                    number_format($anomaly['total_amount'], 2),
                    ucfirst($anomaly['severity']),
                    implode(', ', $anomaly['vendors_involved']->toArray())
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export anomaly report to Excel
     */
    private function exportAnomalyExcel($anomalies, string $filename)
    {
        $csvResponse = $this->exportAnomalyCSV($anomalies, $filename);

        return response($csvResponse->getContent(), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Export anomaly report to PDF
     */
    private function exportAnomalyPDF($anomalies, string $filename)
    {
        $html = $this->generateAnomalyHTML($anomalies);

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate HTML for anomaly report
     */
    private function generateAnomalyHTML($anomalies): string
    {
        $generatedDate = now()->format('F d, Y \a\t H:i');
        $count = $anomalies->count();

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Anomaly Detection Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .meta { color: #666; font-size: 12px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #dc3545; }
                .severity-high { background-color: #f8d7da; }
                .severity-medium { background-color: #fff3cd; }
                .severity-low { background-color: #d1ecf1; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Pay Sabil Al-Hikmah</h1>
                <h2>Anomaly Detection Report</h2>
                <div class='meta'>Generated on {$generatedDate}</div>
            </div>
            
            <div class='summary'>
                <h3>Report Summary</h3>
                <p><strong>Anomalies Detected:</strong> {$count}</p>
                <p><strong>Detection Criteria:</strong> Students with multiple transactions on the same day</p>
                <p><strong>Note:</strong> This report identifies potential policy violations or system abuse</p>
            </div>
        ";

        if ($count > 0) {
            $html .= "<table>";
            $html .= "<tr><th>Date</th><th>Student</th><th>Matrix No</th><th>Transaction Count</th><th>Total Amount</th><th>Severity</th><th>Vendors</th></tr>";

            foreach ($anomalies as $anomaly) {
                $severityClass = "severity-" . $anomaly['severity'];
                $html .= "<tr class='{$severityClass}'>";
                $html .= "<td>{$anomaly['transaction_date']}</td>";
                $html .= "<td>{$anomaly['student']->full_name}</td>";
                $html .= "<td>{$anomaly['student']->matrix_no}</td>";
                $html .= "<td>{$anomaly['transaction_count']}</td>";
                $html .= "<td>RM " . number_format($anomaly['total_amount'], 2) . "</td>";
                $html .= "<td>" . ucfirst($anomaly['severity']) . "</td>";
                $html .= "<td>" . implode(', ', $anomaly['vendors_involved']->toArray()) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html .= "<div style='text-align: center; padding: 40px;'>";
            $html .= "<h3 style='color: #28a745;'>No Anomalies Detected</h3>";
            $html .= "<p>No unusual transaction patterns found in the selected period.</p>";
            $html .= "</div>";
        }

        $html .= "</body></html>";
        return $html;
    }

    /**
     * Export feedback report to CSV
     */
    private function exportFeedbackCSV($feedbacks, string $filename, $vendor)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        $callback = function () use ($feedbacks, $vendor) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add header info
            fputcsv($file, ['Feedback Report for: ' . $vendor->business_name]);
            fputcsv($file, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []); // Empty row

            // Add column headers
            fputcsv($file, [
                'Review Date',
                'Student Name',
                'Matrix Number',
                'Rating (Stars)',
                'Review Comment',
                'Vendor Response',
                'Response Date'
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->review_date->format('Y-m-d H:i:s'),
                    $feedback->student->full_name ?? 'N/A',
                    $feedback->student->matrix_no ?? 'N/A',
                    $feedback->stars . ' stars',
                    $feedback->review_comment,
                    $feedback->vendor_response ?? 'No response',
                    $feedback->response_date ? $feedback->response_date->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export feedback report to Excel
     */
    private function exportFeedbackExcel($feedbacks, string $filename, $vendor)
    {
        $csvResponse = $this->exportFeedbackCSV($feedbacks, $filename, $vendor);

        return response($csvResponse->getContent(), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Export feedback report to PDF
     */
    private function exportFeedbackPDF($feedbacks, string $filename, $vendor)
    {
        $html = $this->generateFeedbackHTML($feedbacks, $vendor);

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"{$filename}.html\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Generate HTML for feedback report
     */
    private function generateFeedbackHTML($feedbacks, $vendor): string
    {
        $generatedDate = now()->format('F d, Y \a\t H:i');
        $summary = $this->calculateFeedbackSummary($feedbacks);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Feedback Report - {$vendor->business_name}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .meta { color: #666; font-size: 12px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; border-left: 4px solid #ffc107; }
                .rating-5 { color: #28a745; font-weight: bold; }
                .rating-4 { color: #6f42c1; font-weight: bold; }
                .rating-3 { color: #fd7e14; font-weight: bold; }
                .rating-2 { color: #dc3545; font-weight: bold; }
                .rating-1 { color: #dc3545; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Pay Sabil Al-Hikmah</h1>
                <h2>Feedback & Ratings Report</h2>
                <h3>{$vendor->business_name}</h3>
                <div class='meta'>Generated on {$generatedDate}</div>
            </div>
            
            <div class='summary'>
                <h3>Feedback Summary</h3>
                <p><strong>Total Feedbacks:</strong> {$summary['total_feedbacks']}</p>
                <p><strong>Average Rating:</strong> {$summary['average_rating']} / 5.0</p>
                <p><strong>Response Rate:</strong> {$summary['response_rate']}%</p>
                <p><strong>Positive Reviews:</strong> {$summary['sentiment_breakdown']['positive']} | 
                   <strong>Neutral:</strong> {$summary['sentiment_breakdown']['neutral']} | 
                   <strong>Negative:</strong> {$summary['sentiment_breakdown']['negative']}</p>
            </div>
        ";

        if ($feedbacks->count() > 0) {
            $html .= "<table>";
            $html .= "<tr><th>Date</th><th>Student</th><th>Rating</th><th>Comment</th><th>Response</th></tr>";

            foreach ($feedbacks as $feedback) {
                $ratingClass = "rating-" . $feedback->stars;
                $html .= "<tr>";
                $html .= "<td>" . $feedback->review_date->format('M d, Y') . "</td>";
                $html .= "<td>" . ($feedback->student->full_name ?? 'N/A') . "</td>";
                $html .= "<td class='{$ratingClass}'>" . str_repeat('★', $feedback->stars) . str_repeat('☆', 5 - $feedback->stars) . "</td>";
                $html .= "<td>" . ($feedback->review_comment ?: 'No comment') . "</td>";
                $html .= "<td>" . ($feedback->vendor_response ?: 'No response') . "</td>";
                $html .= "</tr>";
            }
            $html .= "</table>";
        } else {
            $html .= "<div style='text-align: center; padding: 40px;'>";
            $html .= "<h3>No Feedback Found</h3>";
            $html .= "<p>No feedback found for the selected period.</p>";
            $html .= "</div>";
        }

        $html .= "</body></html>";
        return $html;
    }
}
