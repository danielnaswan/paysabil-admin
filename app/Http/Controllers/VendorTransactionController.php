<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VendorTransactionController extends Controller
{
    /**
     * Display vendor transactions.
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $query = Transaction::where('vendor_id', $vendor->id)
            ->with(['student.user', 'qrCode.service']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('matrix_no', 'like', "%{$search}%");
            })->orWhereHas('qrCode.service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sort by latest first
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get summary statistics
        $stats = $this->getTransactionStats($vendor, $request);

        return view('vendor.transactions.index', compact('transactions', 'stats'));
    }

    /**
     * Display transaction details.
     */
    public function show($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $transaction = Transaction::where('vendor_id', $vendor->id)
            ->with(['student.user', 'qrCode.service'])
            ->findOrFail($id);

        return view('vendor.transactions.show', compact('transaction'));
    }

    /**
     * Get transaction statistics based on filters.
     */
    private function getTransactionStats($vendor, $request)
    {
        $query = Transaction::where('vendor_id', $vendor->id);

        // Apply same filters as main query
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Get statistics
        $totalTransactions = $query->count();
        $completedTransactions = $query->clone()->where('status', 'COMPLETED')->count();
        $pendingTransactions = $query->clone()->where('status', 'PENDING')->count();
        $failedTransactions = $query->clone()->where('status', 'FAILED')->count();
        $cancelledTransactions = $query->clone()->where('status', 'CANCELLED')->count();

        $totalRevenue = $query->clone()->where('status', 'COMPLETED')->sum('amount');
        $averageOrderValue = $completedTransactions > 0 ? $totalRevenue / $completedTransactions : 0;

        return [
            'total_transactions' => $totalTransactions,
            'completed_transactions' => $completedTransactions,
            'pending_transactions' => $pendingTransactions,
            'failed_transactions' => $failedTransactions,
            'cancelled_transactions' => $cancelledTransactions,
            'total_revenue' => $totalRevenue,
            'average_order_value' => round($averageOrderValue, 2),
            'completion_rate' => $totalTransactions > 0 ? round(($completedTransactions / $totalTransactions) * 100, 1) : 0
        ];
    }

    /**
     * Export transactions to CSV.
     */
    public function export(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $query = Transaction::where('vendor_id', $vendor->id)
            ->with(['student.user', 'qrCode.service']);

        // Apply same filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        $filename = 'transactions_' . $vendor->business_name . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Transaction ID',
                'Date',
                'Student Name',
                'Matrix No',
                'Service',
                'Amount',
                'Status',
                'Meal Details'
            ]);

            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->transaction_date->format('Y-m-d H:i:s'),
                    $transaction->student->user->name,
                    $transaction->student->matrix_no,
                    $transaction->qrCode->service->name,
                    $transaction->amount,
                    $transaction->status,
                    $transaction->meal_details
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
