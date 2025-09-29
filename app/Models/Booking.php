<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_amount',
        'deposit_amount',
        'monthly_rent',
        'status',
        'payment_status',
        'notes',
        'landlord_notes',
        'approved_at',
        'approved_by',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'monthly_rent' => 'decimal:2',
        'approved_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    // Alternative accessor for check_in compatibility
    public function getCheckInAttribute()
    {
        return $this->check_in_date;
    }

    // Alternative accessor for check_out compatibility
    public function getCheckOutAttribute()
    {
        return $this->check_out_date;
    }

    // Booking statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    // Payment statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PARTIAL = 'partial';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_OVERDUE = 'overdue';
    const PAYMENT_REFUNDED = 'refunded';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed'
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Payment Pending',
            self::PAYMENT_PARTIAL => 'Partially Paid',
            self::PAYMENT_PAID => 'Fully Paid',
            self::PAYMENT_OVERDUE => 'Overdue',
            self::PAYMENT_REFUNDED => 'Refunded'
        ];
    }

    // Relationships
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Accessors
    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getPaymentStatusNameAttribute(): string
    {
        return self::getPaymentStatuses()[$this->payment_status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'text-yellow-600 bg-yellow-100',
            self::STATUS_APPROVED => 'text-green-600 bg-green-100',
            self::STATUS_REJECTED => 'text-red-600 bg-red-100',
            self::STATUS_CANCELLED => 'text-gray-600 bg-gray-100',
            self::STATUS_ACTIVE => 'text-blue-600 bg-blue-100',
            self::STATUS_COMPLETED => 'text-purple-600 bg-purple-100'
        ];

        return $colors[$this->status] ?? 'text-gray-600 bg-gray-100';
    }

    public function getPaymentStatusColorAttribute(): string
    {
        $colors = [
            self::PAYMENT_PENDING => 'text-yellow-600 bg-yellow-100',
            self::PAYMENT_PARTIAL => 'text-orange-600 bg-orange-100',
            self::PAYMENT_PAID => 'text-green-600 bg-green-100',
            self::PAYMENT_OVERDUE => 'text-red-600 bg-red-100',
            self::PAYMENT_REFUNDED => 'text-gray-600 bg-gray-100'
        ];

        return $colors[$this->payment_status] ?? 'text-gray-600 bg-gray-100';
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return '₱' . number_format($this->total_amount, 2);
    }

    public function getFormattedDepositAmountAttribute(): string
    {
        return '₱' . number_format($this->deposit_amount, 2);
    }

    public function getFormattedMonthlyRentAttribute(): string
    {
        return '₱' . number_format($this->monthly_rent, 2);
    }

    public function getDurationAttribute(): int
    {
        if (!$this->check_in_date || !$this->check_out_date) {
            return 0;
        }
        return $this->check_in_date->diffInMonths($this->check_out_date);
    }

    // Methods
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeRejected(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_ACTIVE
        ]);
    }

    public function canBeActivated(): bool
    {
        return $this->status === self::STATUS_APPROVED && 
               $this->payment_status === self::PAYMENT_PAID;
    }

    public function approve($approvedBy): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approvedBy
        ]);

        // Reserve the room
        if ($this->room) {
            $this->room->reserve();
        }
    }

    public function reject($reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'landlord_notes' => $reason
        ]);
    }

    public function cancel($cancelledBy, $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy,
            'cancellation_reason' => $reason
        ]);

        // Make room available again
        if ($this->room) {
            $this->room->makeAvailable();
        }
    }

    public function activate(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
        
        // Mark room as occupied
        if ($this->room) {
            $this->room->occupy();
        }
    }

    public function complete(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
        
        // Make room available again
        if ($this->room) {
            $this->room->makeAvailable();
        }
    }

    // Scopes
    public function scopeForTenant($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForLandlord($query, $landlordId)
    {
        return $query->whereHas('property', function($q) use ($landlordId) {
            $q->where('user_id', $landlordId);
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Static method to check if tenant has active bookings (pending or approved)
    public static function tenantHasActiveBooking($tenantId): bool
    {
        return self::where('user_id', $tenantId)
                   ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_ACTIVE])
                   ->exists();
    }

    // Static method to get tenant's active booking
    public static function getTenantActiveBooking($tenantId)
    {
        return self::where('user_id', $tenantId)
                   ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_ACTIVE])
                   ->with(['property', 'room'])
                   ->first();
    }

    // Check if this booking prevents new bookings
    public function preventsNewBookings(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_ACTIVE]);
    }
}