<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Rating;
use App\Models\Service;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VendorDashboardController extends Controller
{
    /**
     * Display vendor dashboard with statistics and recent activities.
     */
    public function index()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats($vendor);

        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($vendor);

        // Get recent reviews
        $recentReviews = $this->getRecentReviews($vendor);

        // Get chart data
        $chartData = $this->getChartData($vendor);

        return view('vendor.dashboard', compact(
            'vendor',
            'stats',
            'recentTransactions',
            'recentReviews',
            'chartData'
        ));
    }

    /**
     * Get dashboard statistics for the vendor.
     */
    private function getDashboardStats($vendor)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Today's statistics
        $todayTransactions = Transaction::where('vendor_id', $vendor->id)
            ->whereDate('transaction_date', $today)
            ->where('status', 'COMPLETED')
            ->count();

        $todayRevenue = Transaction::where('vendor_id', $vendor->id)
            ->whereDate('transaction_date', $today)
            ->where('status', 'COMPLETED')
            ->sum('amount');

        // This month's statistics
        $monthTransactions = Transaction::where('vendor_id', $vendor->id)
            ->where('transaction_date', '>=', $thisMonth)
            ->where('status', 'COMPLETED')
            ->count();

        $monthRevenue = Transaction::where('vendor_id', $vendor->id)
            ->where('transaction_date', '>=', $thisMonth)
            ->where('status', 'COMPLETED')
            ->sum('amount');

        // Last month's statistics for comparison
        $lastMonthTransactions = Transaction::where('vendor_id', $vendor->id)
            ->whereBetween('transaction_date', [$lastMonth, $lastMonthEnd])
            ->where('status', 'COMPLETED')
            ->count();

        $lastMonthRevenue = Transaction::where('vendor_id', $vendor->id)
            ->whereBetween('transaction_date', [$lastMonth, $lastMonthEnd])
            ->where('status', 'COMPLETED')
            ->sum('amount');

        // Calculate percentage changes
        $transactionChange = $lastMonthTransactions > 0
            ? (($monthTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100
            : ($monthTransactions > 0 ? 100 : 0);

        $revenueChange = $lastMonthRevenue > 0
            ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : ($monthRevenue > 0 ? 100 : 0);

        // Other statistics
        $totalServices = Service::where('vendor_id', $vendor->id)->count();
        $activeServices = Service::where('vendor_id', $vendor->id)->where('is_available', true)->count();
        $totalReviews = Rating::where('vendor_id', $vendor->id)->count();
        $avgRating = Rating::where('vendor_id', $vendor->id)->avg('stars') ?? 0;
        $activeQRCodes = QrCode::where('vendor_id', $vendor->id)->where('status', 'ACTIVE')->count();

        return [
            'today' => [
                'transactions' => $todayTransactions,
                'revenue' => $todayRevenue
            ],
            'month' => [
                'transactions' => $monthTransactions,
                'revenue' => $monthRevenue,
                'transaction_change' => round($transactionChange, 1),
                'revenue_change' => round($revenueChange, 1)
            ],
            'overall' => [
                'total_services' => $totalServices,
                'active_services' => $activeServices,
                'total_reviews' => $totalReviews,
                'avg_rating' => round($avgRating, 2),
                'active_qr_codes' => $activeQRCodes
            ]
        ];
    }

    /**
     * Get recent transactions for the vendor.
     */
    private function getRecentTransactions($vendor)
    {
        return Transaction::where('vendor_id', $vendor->id)
            ->with(['student.user', 'qrCode.service'])
            ->orderBy('transaction_date', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get recent reviews for the vendor.
     */
    private function getRecentReviews($vendor)
    {
        return Rating::where('vendor_id', $vendor->id)
            ->with('student.user')
            ->orderBy('review_date', 'desc')
            ->take(5)
            ->get();
    }

    /**
     * Get chart data for dashboard visualizations.
     */
    private function getChartData($vendor)
    {
        // Last 7 days revenue data
        $dailyRevenue = [];
        $dailyTransactions = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            $revenue = Transaction::where('vendor_id', $vendor->id)
                ->whereDate('transaction_date', $date)
                ->where('status', 'COMPLETED')
                ->sum('amount');

            $transactions = Transaction::where('vendor_id', $vendor->id)
                ->whereDate('transaction_date', $date)
                ->where('status', 'COMPLETED')
                ->count();

            $dailyRevenue[] = [
                'date' => $date->format('M j'),
                'revenue' => (float) $revenue
            ];

            $dailyTransactions[] = [
                'date' => $date->format('M j'),
                'transactions' => $transactions
            ];
        }

        // FIXED: Service popularity data with proper table specification
        $servicePopularity = Service::where('vendor_id', $vendor->id)
            ->withCount(['transactions as completed_orders' => function ($query) {
                // FIXED: Specify table name for status column
                $query->where('transactions.status', 'COMPLETED');
            }])
            ->orderBy('completed_orders', 'desc')
            ->take(5)
            ->get()
            ->map(function ($service) {
                return [
                    'name' => $service->name,
                    'orders' => $service->completed_orders ?? 0
                ];
            });

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('vendor_id', $vendor->id)->where('stars', $i)->count();
            $ratingDistribution[] = [
                'rating' => $i,
                'count' => $count
            ];
        }

        return [
            'daily_revenue' => $dailyRevenue,
            'daily_transactions' => $dailyTransactions,
            'service_popularity' => $servicePopularity,
            'rating_distribution' => $ratingDistribution
        ];
    }
}
