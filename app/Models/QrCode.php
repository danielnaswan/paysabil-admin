<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class QrCode extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'service_details',
        'generated_date',
        'expiry_date',
        'status',
        'vendor_id',
        'service_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'service_details' => 'array',
        'generated_date' => 'datetime',
        'expiry_date' => 'datetime'
    ];

    /**
     * The attributes that should be appended to arrays.
     */
    protected $appends = [
        'is_active',
        'is_expired',
        'is_used',
        'time_remaining',
        'status_badge_class'
    ];

    /**
     * QR Code status constants.
     */
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_USED = 'USED';
    public const STATUS_INVALID = 'INVALID';

    // ================================
    // Relationships
    // ================================

    /**
     * Get the vendor that owns the QR code.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the service associated with the QR code.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the transactions that used this QR code.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'qr_code_id');
    }

    /**
     * Get the completed transactions for this QR code.
     */
    public function completedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('status', 'COMPLETED');
    }

    // ================================
    // Auto Status Update Methods
    // ================================

    /**
     * Update status to EXPIRED if expiry date has passed
     */
    public function updateStatusIfExpired(): void
    {
        if ($this->status === self::STATUS_ACTIVE && $this->expiry_date && $this->expiry_date <= now()) {
            $this->status = self::STATUS_EXPIRED;
            $this->saveQuietly(); // Save without triggering events to avoid recursion
        }
    }

    /**
     * Force check and update expired QR codes (for batch operations)
     */
    public static function updateExpiredQrCodes(): int
    {
        return self::where('status', self::STATUS_ACTIVE)
            ->where('expiry_date', '<=', now())
            ->update(['status' => self::STATUS_EXPIRED]);
    }

    // ================================
    // Accessors & Mutators
    // ================================

    /**
     * Check if QR code is active.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_ACTIVE &&
                $this->expiry_date > now(),
        );
    }

    /**
     * Check if QR code is expired.
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->expiry_date <= now() ||
                $this->status === self::STATUS_EXPIRED,
        );
    }

    /**
     * Check if QR code has been used.
     */
    protected function isUsed(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_USED ||
                $this->transactions()->where('status', 'COMPLETED')->exists(),
        );
    }

    /**
     * Get time remaining until expiry.
     */
    protected function timeRemaining(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->is_expired) {
                    return 'Expired';
                }

                $diff = $this->expiry_date->diff(now());

                if ($diff->days > 0) {
                    return $diff->days . ' days, ' . $diff->h . ' hours';
                } elseif ($diff->h > 0) {
                    return $diff->h . ' hours, ' . $diff->i . ' minutes';
                } else {
                    return $diff->i . ' minutes';
                }
            }
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
                    'ACTIVE' => $this->is_expired ? 'badge-warning' : 'badge-success',
                    'EXPIRED' => 'badge-secondary',
                    'USED' => 'badge-info',
                    'INVALID' => 'badge-danger',
                    default => 'badge-secondary'
                };
            }
        );
    }

    /**
     * Ensure code is always uppercase.
     */
    protected function code(): Attribute
    {
        return Attribute::make(
            get: fn($value) => strtoupper($value),
            set: fn($value) => strtoupper($value),
        );
    }

    // ================================
    // Scopes
    // ================================

    /**
     * Scope a query to only include active QR codes.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expiry_date', '>', now());
    }

    /**
     * Scope a query to only include expired QR codes.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('expiry_date', '<=', now())
                ->orWhere('status', self::STATUS_EXPIRED);
        });
    }

    /**
     * Scope a query to only include used QR codes.
     */
    public function scopeUsed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_USED)
            ->orWhereHas('transactions', function ($q) {
                $q->where('status', 'COMPLETED');
            });
    }

    /**
     * Scope a query to filter by vendor.
     */
    public function scopeByVendor(Builder $query, int $vendorId): Builder
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope a query to filter by service.
     */
    public function scopeByService(Builder $query, int $serviceId): Builder
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope a query to include QR codes expiring soon.
     */
    public function scopeExpiringSoon(Builder $query, int $hours = 24): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('expiry_date', [now(), now()->addHours($hours)]);
    }

    /**
     * Scope a query to include QR codes generated within date range.
     */
    public function scopeGeneratedBetween(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('generated_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to include unused QR codes.
     */
    public function scopeUnused(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_USED)
            ->whereDoesntHave('transactions', function ($q) {
                $q->where('status', 'COMPLETED');
            });
    }

    /**
     * Scope a query to include QR codes generated today.
     */
    public function scopeGeneratedToday(Builder $query): Builder
    {
        return $query->whereDate('generated_date', today());
    }

    // ================================
    // Simple Helper Methods (Model Logic Only)
    // ================================

    /**
     * Check if QR code is valid for scanning.
     */
    public function isValidForScanning(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
            $this->expiry_date > now() &&
            $this->service &&
            $this->service->is_available &&
            $this->vendor;
    }

    /**
     * Check if QR code can be extended.
     */
    public function canBeExtended(): bool
    {
        return $this->status === self::STATUS_ACTIVE && !$this->is_used;
    }

    /**
     * Check if QR code can be reactivated.
     */
    public function canBeReactivated(): bool
    {
        return $this->status === self::STATUS_EXPIRED && !$this->is_used;
    }

    /**
     * Check if QR code belongs to vendor.
     */
    public function belongsToVendor(int $vendorId): bool
    {
        return $this->vendor_id === $vendorId;
    }

    /**
     * Check if QR code is for specific service.
     */
    public function isForService(int $serviceId): bool
    {
        return $this->service_id === $serviceId;
    }

    /**
     * Get the QR code's age in hours.
     */
    public function getAgeInHours(): int
    {
        return $this->generated_date->diffInHours(now());
    }

    /**
     * Get the QR code's lifetime in hours.
     */
    public function getLifetimeInHours(): int
    {
        return $this->generated_date->diffInHours($this->expiry_date);
    }

    /**
     * Get usage count.
     */
    public function getUsageCount(): int
    {
        return $this->completedTransactions()->count();
    }

    /**
     * Get formatted generation date.
     */
    public function getFormattedGeneratedDate(): string
    {
        return $this->generated_date?->format('d M Y, H:i') ?? 'Unknown';
    }

    /**
     * Get formatted expiry date.
     */
    public function getFormattedExpiryDate(): string
    {
        return $this->expiry_date?->format('d M Y, H:i') ?? 'Unknown';
    }

    /**
     * Check if QR code has service details.
     */
    public function hasServiceDetails(): bool
    {
        return !empty($this->service_details) && is_array($this->service_details);
    }

    /**
     * Get service name from details.
     */
    public function getServiceName(): string
    {
        return $this->service_details['service_name'] ?? 'Unknown Service';
    }

    /**
     * Get service price from details.
     */
    public function getServicePrice(): float
    {
        return (float) ($this->service_details['price'] ?? 0);
    }

    /**
     * Get vendor name from details.
     */
    public function getVendorName(): string
    {
        return $this->service_details['vendor_name'] ?? $this->vendor?->business_name ?? 'Unknown Vendor';
    }

    /**
     * Check if QR code has expired based on time only.
     */
    public function hasExpiredByTime(): bool
    {
        return $this->expiry_date <= now();
    }

    /**
     * Check if QR code is marked as invalid.
     */
    public function isInvalid(): bool
    {
        return $this->status === self::STATUS_INVALID;
    }

    /**
     * Get hours until expiry (negative if expired).
     */
    public function getHoursUntilExpiry(): int
    {
        return now()->diffInHours($this->expiry_date, false);
    }

    /**
     * Check if QR code is expiring within specified hours.
     */
    public function isExpiringSoon(int $hours = 24): bool
    {
        return $this->is_active &&
            $this->getHoursUntilExpiry() <= $hours &&
            $this->getHoursUntilExpiry() > 0;
    }
}
