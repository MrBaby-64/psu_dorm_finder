<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suspension extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'suspended_by',
        'duration_type',
        'suspended_at',
        'expires_at',
        'reason',
        'admin_notes',
        'warning_number',
        'is_active',
        'lifted_at',
        'lifted_by'
    ];

    protected $casts = [
        'suspended_at' => 'datetime',
        'expires_at' => 'datetime',
        'lifted_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who was suspended
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who suspended the user
     */
    public function suspendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    /**
     * Get the admin who lifted the suspension
     */
    public function liftedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lifted_by');
    }

    /**
     * Scope for active suspensions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if suspension has expired
     */
    public function hasExpired(): bool
    {
        if ($this->duration_type === 'permanent') {
            return false;
        }

        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    /**
     * Get duration in human readable format
     */
    public function getDurationTextAttribute(): string
    {
        return match($this->duration_type) {
            '1_day' => '1 Day',
            '3_days' => '3 Days',
            'permanent' => 'Permanent Ban',
            default => 'Unknown'
        };
    }

    /**
     * Get warning level text
     */
    public function getWarningLevelAttribute(): string
    {
        return match($this->warning_number) {
            1 => '1st Warning',
            2 => '2nd Warning',
            3 => '3rd Warning (Final)',
            default => $this->warning_number . 'th Warning'
        };
    }
}
