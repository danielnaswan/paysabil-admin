<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AuditLog extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'action',
        'details',
        'timestamp',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'details' => 'array',
        'timestamp' => 'datetime'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'formatted_timestamp',
        'time_since_action',
        'is_recent',
        'action_category'
    ];

    /**
     * Action type constants.
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_VIEW = 'view';
    public const ACTION_EXPORT = 'export';
    public const ACTION_APPROVE = 'approve';
    public const ACTION_REJECT = 'reject';
    public const ACTION_GENERATE_REPORT = 'generate_report';
    public const ACTION_GENERATE_QR = 'generate_qr';

    /**
     * Action category constants.
     */
    public const CATEGORY_AUTH = 'authentication';
    public const CATEGORY_DATA = 'data_management';
    public const CATEGORY_REPORTING = 'reporting';
    public const CATEGORY_APPROVAL = 'approval_process';
    public const CATEGORY_SYSTEM = 'system_operation';

    // ================================
    // Relationships
    // ================================

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get formatted timestamp.
     */
    protected function formattedTimestamp(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->timestamp?->format('d M Y, H:i:s') ?? 'Unknown',
        );
    }

    /**
     * Get time since action was performed.
     */
    protected function timeSinceAction(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->timestamp?->diffForHumans() ?? 'Unknown',
        );
    }

    /**
     * Check if action is recent (within 1 hour).
     */
    protected function isRecent(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->timestamp && 
                         $this->timestamp->diffInMinutes(now()) <= 60,
        );
    }

    /**
     * Get action category based on action type.
     */
    protected function actionCategory(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->action) {
                    'login', 'logout' => self::CATEGORY_AUTH,
                    'create', 'update', 'delete', 'view' => self::CATEGORY_DATA,
                    'generate_report', 'export' => self::CATEGORY_REPORTING,
                    'approve', 'reject' => self::CATEGORY_APPROVAL,
                    'generate_qr' => self::CATEGORY_SYSTEM,
                    default => self::CATEGORY_SYSTEM
                };
            }
        );
    }

    /**
     * Format action for display.
     */
    protected function action(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(str_replace('_', ' ', $value)),
            set: fn ($value) => strtolower(str_replace(' ', '_', $value)),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to filter by action.
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to include logs within date range.
     */
    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Scope a query to include recent logs.
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('timestamp', '>=', now()->subHours($hours));
    }

    /**
     * Scope a query to include logs from today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('timestamp', today());
    }

    /**
     * Scope a query to include logs from this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('timestamp', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to include logs from this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('timestamp', now()->month)
                    ->whereYear('timestamp', now()->year);
    }

    /**
     * Scope a query to filter by action category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        $actions = match($category) {
            self::CATEGORY_AUTH => ['login', 'logout'],
            self::CATEGORY_DATA => ['create', 'update', 'delete', 'view'],
            self::CATEGORY_REPORTING => ['generate_report', 'export'],
            self::CATEGORY_APPROVAL => ['approve', 'reject'],
            self::CATEGORY_SYSTEM => ['generate_qr'],
            default => []
        };

        return $query->whereIn('action', $actions);
    }

    /**
     * Scope a query to include authentication actions.
     */
    public function scopeAuthActions(Builder $query): Builder
    {
        return $query->whereIn('action', ['login', 'logout']);
    }

    /**
     * Scope a query to include data management actions.
     */
    public function scopeDataActions(Builder $query): Builder
    {
        return $query->whereIn('action', ['create', 'update', 'delete', 'view']);
    }

    // ================================
    // Simple Helper Methods (Model Logic Only)
    // ================================

    /**
     * Check if log belongs to user.
     */
    public function belongsToUser(int $userId): bool
    {
        return $this->user_id === $userId;
    }

    /**
     * Check if action is of specific type.
     */
    public function isAction(string $action): bool
    {
        return $this->action === $action;
    }

    /**
     * Check if action is in specific category.
     */
    public function isCategory(string $category): bool
    {
        return $this->action_category === $category;
    }

    /**
     * Get details as array.
     */
    public function getDetailsArray(): array
    {
        return $this->details ?? [];
    }

    /**
     * Check if log has details.
     */
    public function hasDetails(): bool
    {
        return !empty($this->details) && is_array($this->details);
    }

    /**
     * Get specific detail value.
     */
    public function getDetail(string $key, $default = null)
    {
        return $this->details[$key] ?? $default;
    }

    /**
     * Get user name who performed the action.
     */
    public function getUserName(): string
    {
        return $this->user?->name ?? 'System';
    }

    /**
     * Get user role who performed the action.
     */
    public function getUserRole(): string
    {
        return $this->user?->role?->value ?? 'Unknown';
    }

    /**
     * Get the age of the log in hours.
     */
    public function getAgeInHours(): int
    {
        return $this->timestamp?->diffInHours(now()) ?? 0;
    }

    /**
     * Get the age of the log in days.
     */
    public function getAgeInDays(): int
    {
        return $this->timestamp?->diffInDays(now()) ?? 0;
    }

    /**
     * Check if log is older than specified days.
     */
    public function isOlderThan(int $days): bool
    {
        return $this->getAgeInDays() > $days;
    }

    /**
     * Check if this is a login action.
     */
    public function isLoginAction(): bool
    {
        return $this->action === self::ACTION_LOGIN;
    }

    /**
     * Check if this is a logout action.
     */
    public function isLogoutAction(): bool
    {
        return $this->action === self::ACTION_LOGOUT;
    }

    /**
     * Check if this is a data modification action.
     */
    public function isDataModificationAction(): bool
    {
        return in_array($this->action, [
            self::ACTION_CREATE,
            self::ACTION_UPDATE,
            self::ACTION_DELETE
        ]);
    }

    /**
     * Check if this is a sensitive action that requires extra attention.
     */
    public function isSensitiveAction(): bool
    {
        return in_array($this->action, [
            self::ACTION_DELETE,
            self::ACTION_APPROVE,
            self::ACTION_REJECT
        ]);
    }

    /**
     * Get formatted action description.
     */
    public function getActionDescription(): string
    {
        $userName = $this->getUserName();
        $action = $this->action;
        
        return "{$userName} performed {$action}";
    }

    /**
     * Check if log occurred during business hours (9 AM - 5 PM).
     */
    public function isDuringBusinessHours(): bool
    {
        if (!$this->timestamp) {
            return false;
        }
        
        $hour = $this->timestamp->hour;
        return $hour >= 9 && $hour <= 17;
    }
}