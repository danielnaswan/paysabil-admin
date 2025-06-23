<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'vendor_id',
        'qr_code_id',
        'transaction_date',
        'status',
        'amount',
        'meal_details'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'formatted_amount',
        'status_badge_class',
        'time_since_transaction',
        'is_recent'
    ];

    /**
     * Transaction status constants.
     */
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a transaction is being deleted, log it
        static::deleting(function ($transaction) {
            Log::info('Transaction being deleted', [
                'transaction_id' => $transaction->id,
                'qr_code_id' => $transaction->qr_code_id,
                'student_id' => $transaction->student_id,
                'vendor_id' => $transaction->vendor_id,
                'status' => $transaction->status,
                'amount' => $transaction->amount,
                'deleted_at' => now()
            ]);
        });
    }

    // ================================
    // Relationships
    // ================================

    /**
     * Get the student who made the transaction.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the vendor for the transaction.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the QR code used for the transaction.
     * Updated to handle cascade delete properly
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id')
            ->withTrashed(); // Allow access to soft-deleted QR codes
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Get formatted amount with currency.
     */
    protected function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => 'RM ' . number_format($this->amount, 2),
        );
    }

    /**
     * Get CSS class for status badge.
     */
    protected function statusBadgeClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'PENDING' => 'badge-warning',
                    'COMPLETED' => 'badge-success',
                    'FAILED' => 'badge-danger',
                    'CANCELLED' => 'badge-secondary',
                    default => 'badge-light'
                };
            }
        );
    }

    /**
     * Get time since transaction.
     */
    protected function timeSinceTransaction(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->transaction_date?->diffForHumans() ?? 'Unknown',
        );
    }

    /**
     * Check if transaction is recent (within 24 hours).
     */
    protected function isRecent(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->transaction_date &&
                $this->transaction_date->diffInHours(now()) <= 24,
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope a query to only include cancelled transactions.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by today's transactions.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('transaction_date', today());
    }

    /**
     * Scope a query to filter by this week's transactions.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('transaction_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope a query to filter by this month's transactions.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year);
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
    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to include recent transactions.
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('transaction_date', '>=', now()->subHours($hours));
    }

    /**
     * Scope to include transactions with valid QR codes (not deleted)
     */
    public function scopeWithValidQrCodes(Builder $query): Builder
    {
        return $query->whereHas('qrCode');
    }

    /**
     * Scope to include transactions with deleted QR codes
     */
    public function scopeWithDeletedQrCodes(Builder $query): Builder
    {
        return $query->whereHas('qrCode', function ($q) {
            $q->onlyTrashed();
        });
    }

    // ================================
    // Simple Helper Methods (Model Logic Only)
    // ================================

    /**
     * Check if transaction can be rated.
     */
    public function canBeRated(): bool
    {
        return $this->status === self::STATUS_COMPLETED &&
            !Rating::where('student_id', $this->student_id)
                ->where('vendor_id', $this->vendor_id)
                ->whereDate('review_date', $this->transaction_date->toDateString())
                ->exists();
    }

    /**
     * Check if transaction is successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if transaction can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_FAILED]);
    }

    /**
     * Check if transaction involves the given student.
     */
    public function belongsToStudent(int $studentId): bool
    {
        return $this->student_id === $studentId;
    }

    /**
     * Check if transaction involves the given vendor.
     */
    public function belongsToVendor(int $vendorId): bool
    {
        return $this->vendor_id === $vendorId;
    }

    /**
     * Get the transaction's formatted date.
     */
    public function getFormattedDate(): string
    {
        return $this->transaction_date?->format('d M Y, H:i') ?? 'Unknown';
    }

    /**
     * Get the transaction's day of week.
     */
    public function getDayOfWeek(): string
    {
        return $this->transaction_date?->format('l') ?? 'Unknown';
    }

    /**
     * Get the transaction's hour.
     */
    public function getHour(): int
    {
        return $this->transaction_date?->hour ?? 0;
    }

    /**
     * Check if the QR code for this transaction still exists
     */
    public function hasValidQrCode(): bool
    {
        return $this->qrCode()->exists();
    }

    /**
     * Get QR code information even if deleted
     */
    public function getQrCodeInfo(): ?array
    {
        $qrCode = $this->qrCode()->withTrashed()->first();

        if (!$qrCode) {
            return null;
        }

        return [
            'code' => $qrCode->code,
            'status' => $qrCode->status,
            'service_name' => $qrCode->service?->name ?? 'Service Deleted',
            'vendor_name' => $qrCode->vendor?->business_name ?? 'Vendor Deleted',
            'is_deleted' => $qrCode->trashed(),
            'deleted_at' => $qrCode->deleted_at
        ];
    }

    /**
     * Get meal details with fallback from QR code
     */
    public function getMealDetailsWithFallback(): string
    {
        if (!empty($this->meal_details)) {
            return $this->meal_details;
        }

        $qrCodeInfo = $this->getQrCodeInfo();
        return $qrCodeInfo['service_name'] ?? 'Unknown Service';
    }
}
