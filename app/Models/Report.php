<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Report extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'generated_date',
        'format',
        'parameters',
        'admin_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'generated_date' => 'datetime',
        'parameters' => 'array'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'formatted_generated_date',
        'time_since_generated',
        'is_recent'
    ];

    /**
     * Report type constants.
     */
    public const TYPE_FINANCIAL = 'financial';
    public const TYPE_PARTICIPATION = 'participation';
    public const TYPE_VENDOR_PERFORMANCE = 'vendor_performance';
    public const TYPE_STUDENT_ACTIVITY = 'student_activity';
    public const TYPE_ANOMALY = 'anomaly';
    public const TYPE_FEEDBACK = 'feedback';

    /**
     * Report format constants.
     */
    public const FORMAT_PDF = 'pdf';
    public const FORMAT_EXCEL = 'excel';
    public const FORMAT_CSV = 'csv';

    // ================================
    // Relationships
    // ================================

    /**
     * Get the admin who generated the report.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get formatted generation date.
     */
    protected function formattedGeneratedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->generated_date?->format('d M Y, H:i') ?? 'Unknown',
        );
    }

    /**
     * Get time since report was generated.
     */
    protected function timeSinceGenerated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->generated_date?->diffForHumans() ?? 'Unknown',
        );
    }

    /**
     * Check if report is recent (within 24 hours).
     */
    protected function isRecent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->generated_date && 
                         $this->generated_date->diffInHours(now()) <= 24,
        );
    }

    /**
     * Format report type for display.
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(str_replace('_', ' ', $value)),
            set: fn ($value) => strtolower(str_replace(' ', '_', $value)),
        );
    }

    /**
     * Format report format for display.
     */
    protected function format(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtolower($value),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to filter by report type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by format.
     */
    public function scopeByFormat(Builder $query, string $format): Builder
    {
        return $query->where('format', $format);
    }

    /**
     * Scope a query to filter by admin.
     */
    public function scopeByAdmin(Builder $query, int $adminId): Builder
    {
        return $query->where('admin_id', $adminId);
    }

    /**
     * Scope a query to include reports generated within date range.
     */
    public function scopeGeneratedBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('generated_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to include recent reports.
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('generated_date', '>=', now()->subHours($hours));
    }

    /**
     * Scope a query to include reports generated today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('generated_date', today());
    }

    /**
     * Scope a query to include reports generated this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('generated_date', now()->month)
                    ->whereYear('generated_date', now()->year);
    }

    // ================================
    // Simple Helper Methods (Model Logic Only)
    // ================================

    /**
     * Check if report belongs to admin.
     */
    public function belongsToAdmin(int $adminId): bool
    {
        return $this->admin_id === $adminId;
    }

    /**
     * Check if report is of specific type.
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }

    /**
     * Check if report is in specific format.
     */
    public function isFormat(string $format): bool
    {
        return $this->format === $format;
    }

    /**
     * Get report parameters as array.
     */
    public function getParametersArray(): array
    {
        return $this->parameters ?? [];
    }

    /**
     * Check if report has parameters.
     */
    public function hasParameters(): bool
    {
        return !empty($this->parameters) && is_array($this->parameters);
    }

    /**
     * Get specific parameter value.
     */
    public function getParameter(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * Get the age of the report in hours.
     */
    public function getAgeInHours(): int
    {
        return $this->generated_date?->diffInHours(now()) ?? 0;
    }

    /**
     * Get the age of the report in days.
     */
    public function getAgeInDays(): int
    {
        return $this->generated_date?->diffInDays(now()) ?? 0;
    }

    /**
     * Check if report is older than specified days.
     */
    public function isOlderThan(int $days): bool
    {
        return $this->getAgeInDays() > $days;
    }

    /**
     * Get admin name who generated the report.
     */
    public function getAdminName(): string
    {
        return $this->admin?->full_name ?? 'Unknown Admin';
    }

    /**
     * Get formatted file name for download.
     */
    public function getFileName(): string
    {
        $date = $this->generated_date?->format('Y-m-d_H-i') ?? 'unknown';
        $type = str_replace(' ', '_', strtolower($this->type));
        $format = strtolower($this->format);
        
        return "{$type}_report_{$date}.{$format}";
    }

    /**
     * Check if report can be regenerated.
     */
    public function canBeRegenerated(): bool
    {
        return $this->hasParameters() && $this->admin;
    }
}