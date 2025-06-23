<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Vendor;
use App\Models\Transaction;
use App\Models\Application;
use App\Models\QrCode;
use App\Models\Rating;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with system statistics
     */
    public function index()
    {
        // Get key statistics
        $statistics = $this->getSystemStatistics();

        // Get chart data
        $chartData = $this->getChartData();

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get top performing vendors
        $topVendors = $this->getTopVendors();

        // Get system health metrics
        $systemHealth = $this->getSystemHealth();

        return view('dashboard', compact(
            'statistics',
            'chartData',
            'recentActivities',
            'topVendors',
            'systemHealth'
        ));
    }

    /**
     * Get main system statistics
     */
    private function getSystemStatistics(): array
    {
        $today = now();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        // Today's transactions and revenue
        $todayTransactions = Transaction::whereDate('transaction_date', $today)
            ->where('status', 'COMPLETED')
            ->get();

        $todayRevenue = $todayTransactions->sum('amount');
        $todayCount = $todayTransactions->count();

        // This month's data
        $thisMonthTransactions = Transaction::where('transaction_date', '>=', $thisMonth)
            ->where('status', 'COMPLETED')
            ->get();

        $thisMonthRevenue = $thisMonthTransactions->sum('amount');
        $thisMonthCount = $thisMonthTransactions->count();

        // Last month's data for comparison
        $lastMonthRevenue = Transaction::whereBetween('transaction_date', [
            $lastMonth,
            $lastMonth->copy()->endOfMonth()
        ])
            ->where('status', 'COMPLETED')
            ->sum('amount');

        $lastMonthCount = Transaction::whereBetween('transaction_date', [
            $lastMonth,
            $lastMonth->copy()->endOfMonth()
        ])
            ->where('status', 'COMPLETED')
            ->count();

        // Calculate percentage changes
        $revenueChange = $lastMonthRevenue > 0 ?
            (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        $transactionChange = $lastMonthCount > 0 ?
            (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100 : 0;

        // Active students (students who have transactions in last 7 days)
        $activeStudents = Student::whereHas('transactions', function ($query) {
            $query->where('transaction_date', '>=', now()->subDays(7))
                ->where('status', 'COMPLETED');
        })->count();

        // Total eligible students
        $totalEligibleStudents = Student::whereHas('application', function ($query) {
            $query->where('status', 'APPROVED');
        })->count();

        // Student participation rate
        $participationRate = $totalEligibleStudents > 0 ?
            ($activeStudents / $totalEligibleStudents) * 100 : 0;

        // Pending applications
        $pendingApplications = Application::where('status', 'PENDING')->count();
        $totalApplications = Application::count();

        return [
            'today_revenue' => $todayRevenue,
            'today_transactions' => $todayCount,
            'month_revenue' => $thisMonthRevenue,
            'month_transactions' => $thisMonthCount,
            'revenue_change' => round($revenueChange, 1),
            'transaction_change' => round($transactionChange, 1),
            'active_students' => $activeStudents,
            'total_eligible_students' => $totalEligibleStudents,
            'participation_rate' => round($participationRate, 1),
            'pending_applications' => $pendingApplications,
            'total_applications' => $totalApplications,
            'total_vendors' => Vendor::count(),
            'active_qr_codes' => QrCode::where('status', 'ACTIVE')->count(),
        ];
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData(): array
    {
        // Last 7 days transaction data
        $dailyData = [];
        $revenueData = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayTransactions = Transaction::whereDate('transaction_date', $date)
                ->where('status', 'COMPLETED')
                ->get();

            $labels[] = $date->format('M d');
            $dailyData[] = $dayTransactions->count();
            $revenueData[] = $dayTransactions->sum('amount');
        }

        // Monthly data for the year
        $monthlyLabels = [];
        $monthlyTransactions = [];
        $monthlyRevenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            $transactions = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
                ->where('status', 'COMPLETED')
                ->get();

            $monthlyLabels[] = $month->format('M Y');
            $monthlyTransactions[] = $transactions->count();
            $monthlyRevenue[] = $transactions->sum('amount');
        }

        // Vendor performance data - FIXED: Use direct transaction queries instead of hasManyThrough
        $vendorPerformance = Vendor::with('user')
            ->get()
            ->map(function ($vendor) {
                // Get transactions for this vendor directly
                $transactions = Transaction::where('vendor_id', $vendor->id)
                    ->where('status', 'COMPLETED')
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->get();

                return [
                    'name' => $vendor->business_name,
                    'transactions' => $transactions->count(),
                    'revenue' => $transactions->sum('amount'),
                    'rating' => $vendor->average_rating
                ];
            })
            ->filter(function ($vendor) {
                return $vendor['transactions'] > 0;
            })
            ->sortByDesc('transactions')
            ->take(6)
            ->values();

        return [
            'daily' => [
                'labels' => $labels,
                'transactions' => $dailyData,
                'revenue' => $revenueData
            ],
            'monthly' => [
                'labels' => $monthlyLabels,
                'transactions' => $monthlyTransactions,
                'revenue' => $monthlyRevenue
            ],
            'vendor_performance' => $vendorPerformance
        ];
    }

    /**
     * Get recent system activities
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent transactions
        $recentTransactions = Transaction::with(['student', 'vendor'])
            ->where('status', 'COMPLETED')
            ->latest('transaction_date')
            ->take(5)
            ->get();

        foreach ($recentTransactions as $transaction) {
            $activities[] = [
                'type' => 'transaction',
                'icon' => 'ni-cart',
                'color' => 'success',
                'title' => 'Meal claimed by ' . $transaction->student->full_name,
                'description' => 'RM ' . number_format($transaction->amount, 2) . ' at ' . $transaction->vendor->business_name,
                'time' => $transaction->transaction_date->diffForHumans(),
                'timestamp' => $transaction->transaction_date->timestamp
            ];
        }

        // Recent applications
        $recentApplications = Application::with('student')
            ->latest('submission_date')
            ->take(3)
            ->get();

        foreach ($recentApplications as $application) {
            $activities[] = [
                'type' => 'application',
                'icon' => 'ni-paper-diploma',
                'color' => $application->status === 'APPROVED' ? 'success' : ($application->status === 'REJECTED' ? 'danger' : 'warning'),
                'title' => 'Application ' . strtolower($application->status),
                'description' => $application->student->full_name . ' - ' . $application->title,
                'time' => $application->submission_date->diffForHumans(),
                'timestamp' => $application->submission_date->timestamp
            ];
        }

        // Recent QR codes generated
        $recentQrCodes = QrCode::with(['vendor', 'service'])
            ->latest('generated_date')
            ->take(2)
            ->get();

        foreach ($recentQrCodes as $qrCode) {
            $activities[] = [
                'type' => 'qr_code',
                'icon' => 'ni-mobile-button',
                'color' => 'info',
                'title' => 'QR Code generated',
                'description' => $qrCode->service->name . ' by ' . $qrCode->vendor->business_name,
                'time' => $qrCode->generated_date->diffForHumans(),
                'timestamp' => $qrCode->generated_date->timestamp
            ];
        }

        // Sort by timestamp and take top 8
        usort($activities, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return array_slice($activities, 0, 8);
    }

    /**
     * Get top performing vendors - FIXED: Resolve ambiguous status column
     */
    private function getTopVendors(): array
    {
        return Vendor::with('user')
            ->get()
            ->map(function ($vendor) {
                // Use direct Transaction query to avoid ambiguous column issues
                $thisMonthTransactions = Transaction::where('vendor_id', $vendor->id)
                    ->where('transactions.status', 'COMPLETED') // Specify table name for status
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->get();

                $totalRevenue = $thisMonthTransactions->sum('amount');
                $totalTransactions = $thisMonthTransactions->count();

                return [
                    'id' => $vendor->id,
                    'name' => $vendor->business_name,
                    'category' => $vendor->service_category,
                    'revenue' => $totalRevenue,
                    'transactions' => $totalTransactions,
                    'rating' => $vendor->average_rating,
                    'total_reviews' => $vendor->total_reviews
                ];
            })
            ->filter(function ($vendor) {
                return $vendor['transactions'] > 0;
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values()
            ->toArray();
    }

    /**
     * Get system health metrics
     */
    private function getSystemHealth(): array
    {
        $totalStudents = Student::count();
        $eligibleStudents = Student::whereHas('application', function ($query) {
            $query->where('status', 'APPROVED');
        })->count();

        $activeQrCodes = QrCode::where('status', 'ACTIVE')->count();
        $expiredQrCodes = QrCode::where('status', 'EXPIRED')->count();
        $totalQrCodes = QrCode::count();

        $failedTransactions = Transaction::where('status', 'FAILED')
            ->whereDate('transaction_date', today())
            ->count();

        $totalTodayTransactions = Transaction::whereDate('transaction_date', today())->count();

        $successRate = $totalTodayTransactions > 0 ?
            (($totalTodayTransactions - $failedTransactions) / $totalTodayTransactions) * 100 : 100;

        return [
            'student_eligibility_rate' => $totalStudents > 0 ?
                round(($eligibleStudents / $totalStudents) * 100, 1) : 0,
            'qr_code_active_rate' => $totalQrCodes > 0 ?
                round(($activeQrCodes / $totalQrCodes) * 100, 1) : 0,
            'transaction_success_rate' => round($successRate, 1),
            'pending_applications' => Application::where('status', 'PENDING')->count(),
            'overdue_applications' => Application::where('status', 'PENDING')
                ->where('submission_date', '<', now()->subDays(5))
                ->count()
        ];
    }
}
