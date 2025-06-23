<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorFeedbackController extends Controller
{
    /**
     * Display vendor feedback and reviews.
     */
    public function index(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $query = Rating::where('vendor_id', $vendor->id)
            ->with(['student.user']);

        // Apply filters
        if ($request->filled('rating')) {
            $query->where('stars', $request->rating);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('review_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('review_date', '<=', $request->date_to);
        }

        if ($request->filled('has_response')) {
            if ($request->has_response === 'yes') {
                $query->whereNotNull('vendor_response');
            } else {
                $query->whereNull('vendor_response');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('review_comment', 'like', "%{$search}%")
                    ->orWhereHas('student.user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort by latest first
        $reviews = $query->orderBy('review_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get rating statistics
        $stats = $this->getRatingStats($vendor, $request);

        return view('vendor.feedback.index', compact('reviews', 'stats'));
    }

    /**
     * Show form to respond to a review.
     */
    public function respond($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $review = Rating::where('vendor_id', $vendor->id)
            ->with('student.user')
            ->findOrFail($id);

        return view('vendor.feedback.respond', compact('review'));
    }

    /**
     * Store vendor response to a review.
     */
    public function storeResponse(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $review = Rating::where('vendor_id', $vendor->id)->findOrFail($id);

        $validated = $request->validate([
            'vendor_response' => ['required', 'string', 'max:1000']
        ]);

        try {
            $review->update([
                'vendor_response' => $validated['vendor_response'],
                'response_date' => now()
            ]);

            return redirect()
                ->route('vendor.feedback.index')
                ->with('success', 'Response submitted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to submit response. Please try again.']);
        }
    }

    /**
     * Update vendor response to a review.
     */
    public function updateResponse(Request $request, $id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $review = Rating::where('vendor_id', $vendor->id)->findOrFail($id);

        $validated = $request->validate([
            'vendor_response' => ['required', 'string', 'max:1000']
        ]);

        try {
            $review->update([
                'vendor_response' => $validated['vendor_response'],
                'response_date' => now()
            ]);

            return redirect()
                ->route('vendor.feedback.index')
                ->with('success', 'Response updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update response. Please try again.']);
        }
    }

    /**
     * Delete vendor response to a review.
     */
    public function deleteResponse($id)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $review = Rating::where('vendor_id', $vendor->id)->findOrFail($id);

        try {
            $review->update([
                'vendor_response' => null,
                'response_date' => null
            ]);

            return redirect()
                ->route('vendor.feedback.index')
                ->with('success', 'Response deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to delete response. Please try again.']);
        }
    }

    /**
     * Get rating statistics based on filters.
     */
    private function getRatingStats($vendor, $request)
    {
        $query = Rating::where('vendor_id', $vendor->id);

        // Apply same filters as main query (except rating filter)
        if ($request->filled('date_from')) {
            $query->whereDate('review_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('review_date', '<=', $request->date_to);
        }

        $totalReviews = $query->count();
        $averageRating = $query->avg('stars') ?? 0;
        $respondedReviews = $query->clone()->whereNotNull('vendor_response')->count();
        $responseRate = $totalReviews > 0 ? round(($respondedReviews / $totalReviews) * 100, 1) : 0;

        // Rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $query->clone()->where('stars', $i)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        // Recent rating trend (last 30 days vs previous 30 days)
        $last30Days = $query->clone()
            ->where('review_date', '>=', now()->subDays(30))
            ->avg('stars') ?? 0;

        $previous30Days = $query->clone()
            ->whereBetween('review_date', [now()->subDays(60), now()->subDays(30)])
            ->avg('stars') ?? 0;

        $ratingTrend = $previous30Days > 0
            ? round((($last30Days - $previous30Days) / $previous30Days) * 100, 1)
            : ($last30Days > 0 ? 100 : 0);

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'responded_reviews' => $respondedReviews,
            'response_rate' => $responseRate,
            'rating_distribution' => $ratingDistribution,
            'rating_trend' => $ratingTrend,
            'last_30_days_avg' => round($last30Days, 2),
            'previous_30_days_avg' => round($previous30Days, 2)
        ];
    }

    /**
     * Export reviews to CSV.
     */
    public function export(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('login')->with('error', 'Vendor profile not found.');
        }

        $query = Rating::where('vendor_id', $vendor->id)
            ->with(['student.user']);

        // Apply same filters
        if ($request->filled('rating')) {
            $query->where('stars', $request->rating);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('review_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('review_date', '<=', $request->date_to);
        }

        $reviews = $query->orderBy('review_date', 'desc')->get();

        $filename = 'reviews_' . $vendor->business_name . '_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reviews) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Review ID',
                'Date',
                'Student Name',
                'Rating',
                'Comment',
                'Vendor Response',
                'Response Date'
            ]);

            // CSV data
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->review_date->format('Y-m-d H:i:s'),
                    $review->student->user->name,
                    $review->stars,
                    $review->review_comment,
                    $review->vendor_response,
                    $review->response_date ? $review->response_date->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
