<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'preferred_date',
        'preferred_time',
        'status',
        'notes',
        'landlord_response',
        'confirmed_date',
        'confirmed_time',
        'cancelled_by',
        'cancellation_reason',
        'visited_at'
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'confirmed_date' => 'date',
        'visited_at' => 'datetime'
    ];

    // Visit statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NO_SHOW = 'no_show';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Confirmation',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_NO_SHOW => 'No Show'
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

    public function landlord()
    {
        return $this->hasOneThrough(
            User::class,
            Property::class,
            'id',
            'id',
            'property_id',
            'user_id'
        );
    }

    // Accessors
    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'text-yellow-600 bg-yellow-100',
            self::STATUS_CONFIRMED => 'text-green-600 bg-green-100',
            self::STATUS_CANCELLED => 'text-red-600 bg-red-100',
            self::STATUS_COMPLETED => 'text-blue-600 bg-blue-100',
            self::STATUS_NO_SHOW => 'text-gray-600 bg-gray-100'
        ];

        return $colors[$this->status] ?? 'text-gray-600 bg-gray-100';
    }

    public function getFormattedPreferredTimeAttribute(): string
    {
        return date('g:i A', strtotime($this->preferred_time));
    }

    public function getFormattedConfirmedTimeAttribute(): string
    {
        return $this->confirmed_time ? date('g:i A', strtotime($this->confirmed_time)) : null;
    }

    public function getDisplayDateTimeAttribute(): string
    {
        if ($this->status === self::STATUS_CONFIRMED && $this->confirmed_date && $this->confirmed_time) {
            return $this->confirmed_date->format('M j, Y') . ' at ' . $this->formatted_confirmed_time;
        }
        return $this->preferred_date->format('M j, Y') . ' at ' . $this->formatted_preferred_time;
    }

    // Compatibility accessors for the history view
    public function getVisitDateAttribute()
    {
        return $this->status === self::STATUS_CONFIRMED && $this->confirmed_date
            ? $this->confirmed_date
            : $this->preferred_date;
    }

    public function getVisitTimeAttribute()
    {
        return $this->status === self::STATUS_CONFIRMED && $this->confirmed_time
            ? $this->formatted_confirmed_time
            : $this->formatted_preferred_time;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isUrgent(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED || !$this->confirmed_date) {
            return false;
        }

        $visitDate = $this->confirmed_date->toDateString();
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        return $visitDate === $today || $visitDate === $tomorrow;
    }

    public function isToday(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED || !$this->confirmed_date) {
            return false;
        }

        return $this->confirmed_date->toDateString() === now()->toDateString();
    }

    public function getUrgencyLevelAttribute(): string
    {
        if (!$this->confirmed_date || $this->status !== self::STATUS_CONFIRMED) {
            return 'normal';
        }

        $visitDate = $this->confirmed_date;
        $today = now()->startOfDay();

        if ($visitDate->isSameDay($today)) {
            return 'today';
        } elseif ($visitDate->isSameDay($today->addDay())) {
            return 'tomorrow';
        } elseif ($visitDate->between($today->addDay(), $today->addDays(2))) {
            return 'next_3_days';
        }

        return 'normal';
    }

    // Methods
    public function confirm($confirmedDate = null, $confirmedTime = null, $response = null): void
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_date' => $confirmedDate ?? $this->preferred_date,
            'confirmed_time' => $confirmedTime ?? $this->preferred_time,
            'landlord_response' => $response
        ]);
    }

    public function cancel($cancelledBy, $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_by' => $cancelledBy,
            'cancellation_reason' => $reason
        ]);
    }

    public function markCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'visited_at' => now()
        ]);
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

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED])
                    ->where('confirmed_date', '>=', now()->toDateString());
    }

    public function scopeToday($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED])
                    ->where('confirmed_date', now()->toDateString());
    }

    public function scopeNext3Days($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED])
                    ->whereBetween('confirmed_date', [
                        now()->addDay()->toDateString(),
                        now()->addDays(3)->toDateString()
                    ]);
    }

    public function scopeImminentVisits($query)
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED])
                    ->whereBetween('confirmed_date', [
                        now()->toDateString(),
                        now()->addDays(3)->toDateString()
                    ])
                    ->orderBy('confirmed_date', 'asc')
                    ->orderBy('confirmed_time', 'asc');
    }
}