<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'matrix_no',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'string'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'application_status',
        'is_eligible',
        'transaction_count_today',
        'profile_completion_percentage'
    ];

    // ================================
    // Relationships
    // ================================

    /**
     * Get the user that owns the student profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student's application.
     */
    public function application(): HasOne
    {
        return $this->hasOne(Application::class);
    }

    /**
     * Get the student's latest application.
     */
    public function latestApplication(): HasOne
    {
        return $this->hasOne(Application::class)->latestOfMany();
    }

    /**
     * Get the student's transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the student's completed transactions.
     */
    public function completedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('status', 'COMPLETED');
    }

    /**
     * Get the student's ratings.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get the student's application status.
     */
    protected function applicationStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->application?->status ?? 'NO_APPLICATION',
        );
    }

    /**
     * Check if student is eligible for meal claims.
     */
    protected function isEligible(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->application?->status === 'APPROVED',
        );
    }

    /**
     * Get today's transaction count for the student.
     */
    protected function transactionCountToday(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->transactions()
                ->whereDate('transaction_date', today())
                ->count(),
        );
    }

    /**
     * Get profile completion percentage.
     */
    protected function profileCompletionPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fields = [
                    'full_name' => !empty($this->full_name),
                    'matrix_no' => !empty($this->matrix_no),
                    'user_email' => !empty($this->user?->email),
                    'user_phone' => !empty($this->user?->phone_number),
                    'profile_picture' => !empty($this->user?->profile_picture_url),
                    'has_application' => $this->application !== null,
                    'application_approved' => $this->application?->status === 'APPROVED',
                ];

                $completed = array_sum($fields);
                $total = count($fields);

                return round(($completed / $total) * 100);
            }
        );
    }

    /**
     * Format matrix number with proper casing.
     */
    protected function matrixNo(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtoupper($value),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include eligible students.
     */
    public function scopeEligible(Builder $query): Builder
    {
        return $query->whereHas('application', function ($q) {
            $q->where('status', 'APPROVED');
        });
    }

    /**
     * Scope a query to only include students with applications.
     */
    public function scopeWithApplication(Builder $query): Builder
    {
        return $query->whereHas('application');
    }

    /**
     * Scope a query to filter by application status.
     */
    public function scopeByApplicationStatus(Builder $query, string $status): Builder
    {
        return $query->whereHas('application', function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    /**
     * Scope a query to find students by matrix number pattern.
     */
    public function scopeByMatrixPattern(Builder $query, string $pattern): Builder
    {
        return $query->where('matrix_no', 'LIKE', "%{$pattern}%");
    }

    // ================================
    // Helper Methods
    // ================================

    /**
     * Check if student can claim meal today.
     */
    public function canClaimMealToday(): bool
    {
        if (!$this->is_eligible) {
            return false;
        }

        return $this->transaction_count_today === 0;
    }

    /**
     * Get student's transaction history for a date range.
     */
    public function getTransactionHistory(?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->completedTransactions()->with(['vendor', 'qrCode']);

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    /**
     * Get student's monthly meal claim summary.
     */
    public function getMonthlyClaimSummary(?int $year, ?int $month): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $transactions = $this->completedTransactions()
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->get();

        return [
            'total_claims' => $transactions->count(),
            'total_value' => $transactions->sum('amount'),
            'unique_vendors' => $transactions->unique('vendor_id')->count(),
            'days_claimed' => $transactions->groupBy(function ($transaction) {
                return $transaction->transaction_date->format('Y-m-d');
            })->count(),
        ];
    }

    /**
     * Get student's favorite vendors based on transaction frequency.
     */
    public function getFavoriteVendors(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->completedTransactions()
            ->with('vendor')
            ->get()
            ->groupBy('vendor_id')
            ->map(function ($transactions) {
                return [
                    'vendor' => $transactions->first()->vendor,
                    'transaction_count' => $transactions->count(),
                    'total_spent' => $transactions->sum('amount'),
                ];
            })
            ->sortByDesc('transaction_count')
            ->take($limit)
            ->values();
    }

    /**
     * Submit a new application.
     */
    public function submitApplication(array $applicationData): Application
    {
        // Remove existing application if any
        $this->application()?->delete();

        return $this->application()->create(array_merge($applicationData, [
            'submission_date' => now(),
            'status' => 'PENDING'
        ]));
    }

    /**
     * Get student's rating activity.
     */
    public function getRatingsSummary(): array
    {
        $ratings = $this->ratings();

        return [
            'total_ratings' => $ratings->count(),
            'average_rating_given' => $ratings->avg('stars') ?? 0,
            'latest_rating_date' => $ratings->max('review_date'),
            'vendors_rated' => $ratings->distinct('vendor_id')->count(),
        ];
    }
}