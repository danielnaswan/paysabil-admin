<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Application extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'submission_date',
        'document_url',
        'document_name',
        'document_size',
        'admin_remarks',
        'student_id',
        'reviewed_by',
        'reviewed_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'id' => 'string',
        'submission_date' => 'datetime',
        'reviewed_at' => 'datetime'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'status_badge_class',
        'days_pending',
        'document_size_human',
        'review_turnaround_time',
        'is_overdue'
    ];

    /**
     * Application status constants.
     */
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';

    /**
     * SLA days for application review.
     */
    public const SLA_DAYS = 5;

    // ================================
    // Relationships
    // ================================

    /**
     * Get the student that owns the application.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the admin who reviewed the application.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get CSS class for status badge.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->status) {
                    'PENDING' => 'badge-warning',
                    'APPROVED' => 'badge-success',
                    'REJECTED' => 'badge-danger',
                    default => 'badge-secondary'
                };
            }
        );
    }

    /**
     * Get number of days application has been pending.
     */
    protected function daysPending(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status !== 'PENDING') {
                    return 0;
                }
                return $this->submission_date->diffInDays(now());
            }
        );
    }

    /**
     * Get human-readable document size.
     */
    protected function documentSizeHuman(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->document_size) {
                    return 'Unknown';
                }

                $bytes = $this->document_size;
                $units = ['B', 'KB', 'MB', 'GB'];
                $index = 0;
                
                while ($bytes >= 1024 && $index < count($units) - 1) {
                    $bytes /= 1024;
                    $index++;
                }
                
                return round($bytes, 2) . ' ' . $units[$index];
            }
        );
    }

    /**
     * Get review turnaround time in hours.
     */
    protected function reviewTurnaroundTime(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->reviewed_at || !$this->submission_date) {
                    return null;
                }
                return $this->submission_date->diffInHours($this->reviewed_at);
            }
        );
    }

    /**
     * Check if application is overdue for review.
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->status !== 'PENDING') {
                    return false;
                }
                return $this->days_pending > self::SLA_DAYS;
            }
        );
    }

    /**
     * Format title with proper casing.
     */
    protected function title(): Attribute
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
     * Scope a query to only include applications with specific status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved applications.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected applications.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include overdue applications.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->where('submission_date', '<', now()->subDays(self::SLA_DAYS));
    }

    /**
     * Scope a query to include applications submitted within date range.
     */
    public function scopeSubmittedBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('submission_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to include applications reviewed by specific admin.
     */
    public function scopeReviewedBy(Builder $query, int $adminId): Builder
    {
        return $query->where('reviewed_by', $adminId);
    }

    /**
     * Scope a query to order by priority (overdue first, then by submission date).
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderByRaw("
            CASE 
                WHEN status = 'PENDING' AND submission_date < DATE_SUB(NOW(), INTERVAL " . self::SLA_DAYS . " DAY) THEN 1
                WHEN status = 'PENDING' THEN 2
                ELSE 3
            END
        ")->orderBy('submission_date', 'asc');
    }

    // ================================
    // Helper Methods
    // ================================

    /**
     * Approve the application.
     */
    public function approve(int $reviewerId, ?string $remarks): bool
    {
        return $this->updateStatus(self::STATUS_APPROVED, $reviewerId, $remarks);
    }

    /**
     * Reject the application.
     */
    public function reject(int $reviewerId, string $remarks): bool
    {
        return $this->updateStatus(self::STATUS_REJECTED, $reviewerId, $remarks);
    }

    /**
     * Update application status.
     */
    private function updateStatus(string $status, int $reviewerId, ?string $remarks): bool
    {
        $this->status = $status;
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        
        if ($remarks) {
            $this->admin_remarks = $remarks;
        }

        return $this->save();
    }

    /**
     * Check if application can be reviewed.
     */
    public function canBeReviewed(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if application can be edited by student.
     */
    public function canBeEditedByStudent(): bool
    {
        return $this->status === self::STATUS_PENDING && 
               $this->submission_date->diffInHours(now()) <= 24;
    }

    /**
     * Get document URL with validation.
     */
    public function getDocumentUrl(): ?string
    {
        if (!$this->document_url) {
            return null;
        }

        // Check if file exists
        $path = str_replace('/storage', 'public', $this->document_url);
        if (!Storage::exists($path)) {
            return null;
        }

        return $this->document_url;
    }

    /**
     * Delete associated document file.
     */
    public function deleteDocumentFile(): bool
    {
        if (!$this->document_url) {
            return true;
        }

        $path = str_replace('/storage', 'public', $this->document_url);
        return Storage::delete($path);
    }

    /**
     * Get application statistics for dashboard.
     */
    public static function getStatistics(): array
    {
        $total = self::count();
        $pending = self::pending()->count();
        $approved = self::approved()->count();
        $rejected = self::rejected()->count();
        $overdue = self::overdue()->count();

        $avgProcessingTime = self::whereNotNull('reviewed_at')
            ->whereNotNull('submission_date')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submission_date, reviewed_at)) as avg_hours')
            ->value('avg_hours') ?? 0;

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
            'overdue' => $overdue,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            'average_processing_time_hours' => round($avgProcessingTime, 2),
            'sla_compliance' => $pending > 0 ? round((($pending - $overdue) / $pending) * 100, 2) : 100,
        ];
    }

    /**
     * Get monthly application trends.
     */
    public static function getMonthlyTrends(int $months = 6): array
    {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $monthData = self::submittedBetween($monthStart, $monthEnd);
            
            $trends[] = [
                'month' => $date->format('M Y'),
                'total' => $monthData->count(),
                'approved' => $monthData->clone()->approved()->count(),
                'rejected' => $monthData->clone()->rejected()->count(),
                'pending' => $monthData->clone()->pending()->count(),
            ];
        }

        return $trends;
    }

    /**
     * Get priority score for sorting.
     */
    public function getPriorityScore(): int
    {
        if ($this->status !== self::STATUS_PENDING) {
            return 999; // Lowest priority
        }

        $score = $this->days_pending;
        
        // Boost priority if overdue
        if ($this->is_overdue) {
            $score += 100;
        }

        return $score;
    }

    /**
     * Send notification to student about status change.
     */
    public function notifyStudent(): void
    {
        // This would integrate with your notification system
        // For example, sending email or push notification
        
        $message = match($this->status) {
            'APPROVED' => 'Your application has been approved! You can now start claiming meals.',
            'REJECTED' => 'Your application has been rejected. Please check the admin remarks and resubmit if needed.',
            default => 'Your application status has been updated.'
        };

        // Implementation would depend on your notification system
        // notify($this->student->user, $message, $this);
    }
}