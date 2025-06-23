<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Rating extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'vendor_id',
        'student_id',
        'stars',
        'review_comment',
        'vendor_response',
        'review_date',
        'response_date',
        'is_modified'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'review_date' => 'datetime',
        'response_date' => 'datetime',
        'is_modified' => 'boolean',
        'stars' => 'integer'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'stars_display',
        'has_response',
        'time_since_review',
        'sentiment',
        'is_recent'
    ];

    /**
     * Rating constants.
     */
    public const MIN_STARS = 1;
    public const MAX_STARS = 5;
    public const RECENT_DAYS = 7;

    // ================================
    // Relationships
    // ================================

    /**
     * Get the vendor that was rated.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the student who gave the rating.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get stars as display string (e.g., "★★★★☆").
     */
    protected function starsDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                $filled = str_repeat('★', $this->stars);
                $empty = str_repeat('☆', self::MAX_STARS - $this->stars);
                return $filled . $empty;
            }
        );
    }

    /**
     * Check if vendor has responded.
     */
    protected function hasResponse(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->vendor_response),
        );
    }

    /**
     * Get time since review was posted.
     */
    protected function timeSinceReview(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->review_date) {
                    return 'Unknown';
                }

                return $this->review_date->diffForHumans();
            }
        );
    }

    /**
     * Get sentiment based on rating and comment.
     */
    protected function sentiment(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->stars >= 4) {
                    return 'positive';
                } elseif ($this->stars >= 3) {
                    return 'neutral';
                } else {
                    return 'negative';
                }
            }
        );
    }

    /**
     * Check if rating is recent.
     */
    protected function isRecent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->review_date && 
                         $this->review_date->diffInDays(now()) <= self::RECENT_DAYS,
        );
    }

    /**
     * Validate stars value.
     */
    protected function stars(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => max(self::MIN_STARS, min(self::MAX_STARS, (int)$value)),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to filter by star rating.
     */
    public function scopeByStars(Builder $query, int $stars): Builder
    {
        return $query->where('stars', $stars);
    }

    /**
     * Scope a query to filter by minimum stars.
     */
    public function scopeMinStars(Builder $query, int $minStars): Builder
    {
        return $query->where('stars', '>=', $minStars);
    }

    /**
     * Scope a query to only include positive ratings (4-5 stars).
     */
    public function scopePositive(Builder $query): Builder
    {
        return $query->where('stars', '>=', 4);
    }

    /**
     * Scope a query to only include negative ratings (1-2 stars).
     */
    public function scopeNegative(Builder $query): Builder
    {
        return $query->where('stars', '<=', 2);
    }

    /**
     * Scope a query to only include neutral ratings (3 stars).
     */
    public function scopeNeutral(Builder $query): Builder
    {
        return $query->where('stars', 3);
    }

    /**
     * Scope a query to only include ratings with comments.
     */
    public function scopeWithComments(Builder $query): Builder
    {
        return $query->whereNotNull('review_comment')
                    ->where('review_comment', '!=', '');
    }

    /**
     * Scope a query to only include ratings with vendor responses.
     */
    public function scopeWithResponses(Builder $query): Builder
    {
        return $query->whereNotNull('vendor_response')
                    ->where('vendor_response', '!=', '');
    }

    /**
     * Scope a query to only include recent ratings.
     */
    public function scopeRecent(Builder $query, ?int $days): Builder
    {
        $days = $days ?? self::RECENT_DAYS;
        return $query->where('review_date', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('review_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by vendor.
     */
    public function scopeForVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeByStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    // ================================
    // Helper Methods
    // ================================

    /**
     * Add vendor response to the rating.
     */
    public function addVendorResponse(string $response): bool
    {
        $this->vendor_response = $response;
        $this->response_date = now();
        
        return $this->save();
    }

    /**
     * Mark rating as modified.
     */
    public function markAsModified(): bool
    {
        $this->is_modified = true;
        return $this->save();
    }

    /**
     * Check if rating can be edited by student.
     */
    public function canBeEditedByStudent(): bool
    {
        return $this->review_date->diffInHours(now()) <= 24 && 
               !$this->has_response;
    }

    /**
     * Check if vendor can respond.
     */
    public function canVendorRespond(): bool
    {
        return empty($this->vendor_response);
    }

    /**
     * Get rating statistics for a vendor.
     */
    public static function getVendorStatistics(int $vendorId): array
    {
        $ratings = self::forVendor($vendorId);
        $totalRatings = $ratings->count();

        if ($totalRatings === 0) {
            return [
                'average_rating' => 0,
                'total_ratings' => 0,
                'rating_distribution' => [],
                'response_rate' => 0,
                'recent_trend' => 'stable'
            ];
        }

        $averageRating = $ratings->avg('stars');
        
        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $ratings->clone()->where('stars', $i)->count();
            $distribution[$i] = [
                'stars' => $i,
                'count' => $count,
                'percentage' => round(($count / $totalRatings) * 100, 1)
            ];
        }

        // Response rate
        $responsesCount = $ratings->clone()->whereNotNull('vendor_response')
                                          ->where('vendor_response', '!=', '')
                                          ->count();
        $responseRate = ($responsesCount / $totalRatings) * 100;

        // Recent trend (last 30 days vs previous 30 days)
        $recentAvg = $ratings->clone()->recent(30)->avg('stars') ?? 0;
        $previousAvg = $ratings->clone()
                              ->whereBetween('review_date', [now()->subDays(60), now()->subDays(30)])
                              ->avg('stars') ?? 0;

        $trend = 'stable';
        if ($recentAvg > $previousAvg + 0.2) {
            $trend = 'improving';
        } elseif ($recentAvg < $previousAvg - 0.2) {
            $trend = 'declining';
        }

        return [
            'average_rating' => round($averageRating, 2),
            'total_ratings' => $totalRatings,
            'rating_distribution' => $distribution,
            'response_rate' => round($responseRate, 1),
            'recent_trend' => $trend,
            'positive_percentage' => round(($ratings->clone()->positive()->count() / $totalRatings) * 100, 1),
            'negative_percentage' => round(($ratings->clone()->negative()->count() / $totalRatings) * 100, 1),
        ];
    }

    /**
     * Get monthly rating trends for a vendor.
     */
    public static function getMonthlyTrends(int $vendorId, int $months = 6): array
    {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthRatings = self::forVendor($vendorId)
                               ->betweenDates($monthStart, $monthEnd);

            $count = $monthRatings->count();
            $average = $count > 0 ? $monthRatings->avg('stars') : 0;

            $trends[] = [
                'month' => $date->format('M Y'),
                'average_rating' => round($average, 2),
                'total_ratings' => $count,
                'positive_count' => $monthRatings->clone()->positive()->count(),
                'negative_count' => $monthRatings->clone()->negative()->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get top positive and negative feedback.
     */
    public static function getTopFeedback(int $vendorId, int $limit = 5): array
    {
        $positiveReviews = self::forVendor($vendorId)
                              ->positive()
                              ->withComments()
                              ->with('student')
                              ->orderBy('stars', 'desc')
                              ->orderBy('review_date', 'desc')
                              ->limit($limit)
                              ->get();

        $negativeReviews = self::forVendor($vendorId)
                              ->negative()
                              ->withComments()
                              ->with('student')
                              ->orderBy('stars', 'asc')
                              ->orderBy('review_date', 'desc')
                              ->limit($limit)
                              ->get();

        return [
            'positive' => $positiveReviews->map(function ($rating) {
                return [
                    'student_name' => $rating->student->full_name,
                    'stars' => $rating->stars,
                    'comment' => $rating->review_comment,
                    'date' => $rating->review_date,
                    'has_response' => $rating->has_response
                ];
            }),
            'negative' => $negativeReviews->map(function ($rating) {
                return [
                    'student_name' => $rating->student->full_name,
                    'stars' => $rating->stars,
                    'comment' => $rating->review_comment,
                    'date' => $rating->review_date,
                    'has_response' => $rating->has_response
                ];
            })
        ];
    }

    /**
     * Get unanswered negative reviews.
     */
    public static function getUnansweredNegativeReviews(int $vendorId): \Illuminate\Database\Eloquent\Collection
    {
        return self::forVendor($vendorId)
                   ->negative()
                   ->whereNull('vendor_response')
                   ->with('student')
                   ->orderBy('review_date', 'desc')
                   ->get();
    }

    /**
     * Calculate vendor response time statistics.
     */
    public static function getResponseTimeStats(int $vendorId): array
    {
        $ratingsWithResponse = self::forVendor($vendorId)
                                  ->withResponses()
                                  ->whereNotNull('response_date')
                                  ->get();

        if ($ratingsWithResponse->isEmpty()) {
            return [
                'average_response_time_hours' => 0,
                'fastest_response_hours' => 0,
                'slowest_response_hours' => 0,
                'total_responses' => 0
            ];
        }

        $responseTimes = $ratingsWithResponse->map(function ($rating) {
            return $rating->review_date->diffInHours($rating->response_date);
        });

        return [
            'average_response_time_hours' => round($responseTimes->avg(), 2),
            'fastest_response_hours' => $responseTimes->min(),
            'slowest_response_hours' => $responseTimes->max(),
            'total_responses' => $ratingsWithResponse->count()
        ];
    }
}