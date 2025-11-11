<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'landlord_id',
        'property_id',
        'reason',
        'description',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    // Report statuses
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    // Report reasons
    const REASON_FRAUD = 'fraud';
    const REASON_HARASSMENT = 'harassment';
    const REASON_MISLEADING_INFO = 'misleading_info';
    const REASON_UNPROFESSIONAL = 'unprofessional';
    const REASON_SAFETY_CONCERN = 'safety_concern';
    const REASON_OTHER = 'other';

    public static function getReasons(): array
    {
        return [
            self::REASON_FRAUD => 'Fraud or Scam',
            self::REASON_HARASSMENT => 'Harassment',
            self::REASON_MISLEADING_INFO => 'Misleading Information',
            self::REASON_UNPROFESSIONAL => 'Unprofessional Conduct',
            self::REASON_SAFETY_CONCERN => 'Safety Concern',
            self::REASON_OTHER => 'Other'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_REVIEWED => 'Under Review',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed'
        ];
    }

    // Relationships
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors
    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getReasonNameAttribute(): string
    {
        return self::getReasons()[$this->reason] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_REVIEWED => 'bg-blue-100 text-blue-800',
            self::STATUS_RESOLVED => 'bg-green-100 text-green-800',
            self::STATUS_DISMISSED => 'bg-gray-100 text-gray-800'
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
