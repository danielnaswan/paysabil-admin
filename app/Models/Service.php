<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Service extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'category',
        'preparation_time',
        'is_available',
        'vendor_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'preparation_time' => 'integer',
        'is_available' => 'boolean'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'formatted_price',
        'preparation_time_text',
        'average_rating',
        'total_orders',
        'popularity_score',
        'availability_status'
    ];

    // ================================
    // Relationships
    // ================================

    /**
     * Get the vendor that owns the service.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the QR codes for this service.
     */
    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class);
    }

    /**
     * Get the active QR codes for this service.
     */
    public function activeQrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class)->where('status', 'ACTIVE');
    }

    /**
     * Get transactions through QR codes.
     */
    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, QrCode::class);
    }

    /**
     * Get completed transactions through QR codes.
     */
    public function completedTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(Transaction::class, QrCode::class)
                    ->where('transactions.status', 'COMPLETED');
    }

    /**
     * Get ratings through transactions.
     */
    public function ratings(): HasManyThrough
    {
        return $this->hasManyThrough(
            Rating::class, 
            Transaction::class,
            'qr_code_id', // Foreign key on transactions table
            'vendor_id',  // Foreign key on ratings table
            'id',         // Local key on services table
            'vendor_id'   // Local key on transactions table
        )->whereColumn('ratings.vendor_id', 'transactions.vendor_id');
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get formatted price with currency.
     */
    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => 'RM ' . number_format($this->price, 2),
        );
    }

    /**
     * Get preparation time as human readable text.
     */
    protected function preparationTimeText(): Attribute
    {
        return Attribute::make(
            get: function () {
                $minutes = $this->preparation_time;
                
                if ($minutes < 60) {
                    return $minutes . ' minutes';
                } else {
                    $hours = floor($minutes / 60);
                    $remainingMinutes = $minutes % 60;
                    
                    if ($remainingMinutes > 0) {
                        return $hours . 'h ' . $remainingMinutes . 'm';
                    } else {
                        return $hours . ' hour' . ($hours > 1 ? 's' : '');
                    }
                }
            }
        );
    }

    /**
     * Get average rating for this service.
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ratings()->avg('stars') ?? 0,
        );
    }

    /**
     * Get total orders count.
     */
    protected function totalOrders(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->completedTransactions()->count(),
        );
    }

    /**
     * Get popularity score based on orders and ratings.
     */
    protected function popularityScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                $orders = $this->total_orders;
                $rating = $this->average_rating;
                
                // Weighted score: 70% orders, 30% rating
                return ($orders * 0.7) + ($rating * 0.3);
            }
        );
    }

    /**
     * Get availability status text.
     */
    protected function availabilityStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_available ? 'Available' : 'Unavailable',
        );
    }

    /**
     * Format service name with proper casing.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(strtolower($value)),
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    /**
     * Format category with proper casing.
     */
    protected function category(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(strtolower($value)),
            set: fn ($value) => ucwords(strtolower($value)),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to only include available services.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', 'LIKE', "%{$category}%");
    }

    /**
     * Scope a query to filter by price range.
     */
    public function scopePriceRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope a query to filter by preparation time.
     */
    public function scopeMaxPrepTime(Builder $query, int $minutes): Builder
    {
        return $query->where('preparation_time', '<=', $minutes);
    }

    /**
     * Scope a query to order by popularity.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('completedTransactions')
                    ->orderBy('completed_transactions_count', 'desc');
    }

    /**
     * Scope a query to include services with active QR codes.
     */
    public function scopeWithActiveQrCodes(Builder $query): Builder
    {
        return $query->whereHas('activeQrCodes');
    }

    // ================================
    // Helper Methods
    // ================================

    /**
     * Check if service can generate QR codes.
     */
    public function canGenerateQrCode(): bool
    {
        return $this->is_available && 
               $this->vendor && 
               $this->vendor->user;
    }

    /**
     * Get service statistics.
     */
    public function getStatistics(): array
    {
        $transactions = $this->completedTransactions();
        $totalRevenue = $transactions->sum('amount');
        $totalOrders = $transactions->count();
        $uniqueCustomers = $transactions->distinct('student_id')->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'unique_customers' => $uniqueCustomers,
            'average_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0,
            'average_rating' => $this->average_rating,
            'total_ratings' => $this->ratings()->count(),
            'active_qr_codes' => $this->activeQrCodes()->count(),
        ];
    }

    /**
     * Get monthly performance data.
     */
    public function getMonthlyPerformance(?int $year, ?int $month): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $monthlyTransactions = $this->completedTransactions()
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->get();

        $revenue = $monthlyTransactions->sum('amount');
        $orders = $monthlyTransactions->count();
        $uniqueCustomers = $monthlyTransactions->unique('student_id')->count();

        $monthlyRatings = $this->ratings()
            ->whereYear('review_date', $year)
            ->whereMonth('review_date', $month)
            ->get();

        return [
            'period' => now()->setYear($year)->setMonth($month)->format('F Y'),
            'revenue' => $revenue,
            'orders' => $orders,
            'unique_customers' => $uniqueCustomers,
            'average_rating' => $monthlyRatings->avg('stars') ?? 0,
            'total_ratings' => $monthlyRatings->count(),
        ];
    }

    /**
     * Get service recommendations based on similar services.
     */
    public function getRecommendations(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('id', '!=', $this->id)
            ->where('category', $this->category)
            ->available()
            ->popular()
            ->limit($limit)
            ->get();
    }

    /**
     * Toggle availability status.
     */
    public function toggleAvailability(): bool
    {
        $this->is_available = !$this->is_available;
        return $this->save();
    }

    /**
     * Check if service is popular (above average orders).
     */
    public function isPopular(): bool
    {
        $averageOrders = self::withCount('completedTransactions')
            ->avg('completed_transactions_count') ?? 0;
            
        return $this->total_orders > $averageOrders;
    }

    /**
     * Get peak ordering times for this service.
     */
    public function getPeakOrderingTimes(): array
    {
        $transactions = $this->completedTransactions()
            ->selectRaw('HOUR(transaction_date) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'hour' => $transaction->hour,
                'formatted_time' => sprintf('%02d:00', $transaction->hour),
                'order_count' => $transaction->count,
            ];
        })->toArray();
    }

    /**
     * Get customer feedback summary.
     */
    public function getFeedbackSummary(): array
    {
        $ratings = $this->ratings();
        $totalRatings = $ratings->count();

        if ($totalRatings === 0) {
            return [
                'average_rating' => 0,
                'total_ratings' => 0,
                'rating_distribution' => [],
                'recent_comments' => [],
            ];
        }

        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $ratings->clone()->where('stars', $i)->count();
            $ratingDistribution[$i] = [
                'stars' => $i,
                'count' => $count,
                'percentage' => round(($count / $totalRatings) * 100, 1),
            ];
        }

        $recentComments = $ratings->clone()
            ->whereNotNull('review_comment')
            ->with('student')
            ->orderBy('review_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'student_name' => $rating->student->full_name,
                    'stars' => $rating->stars,
                    'comment' => $rating->review_comment,
                    'date' => $rating->review_date,
                ];
            });

        return [
            'average_rating' => round($this->average_rating, 2),
            'total_ratings' => $totalRatings,
            'rating_distribution' => $ratingDistribution,
            'recent_comments' => $recentComments,
        ];
    }
}