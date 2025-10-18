<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'room_id',
        'move_in_date',
        'move_out_date',
        'message',
        'status',
        'landlord_reply',
        'replied_at'
    ];

    protected $casts = [
        'move_in_date' => 'date',
        'move_out_date' => 'date',
        'replied_at' => 'datetime'
    ];

    // Inquiry statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REPLIED = 'replied';
    const STATUS_CLOSED = 'closed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_CLOSED => 'Closed'
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

    public function messages()
    {
        return $this->hasMany(Message::class);
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
            self::STATUS_APPROVED => 'text-green-600 bg-green-100',
            self::STATUS_REJECTED => 'text-red-600 bg-red-100',
            self::STATUS_REPLIED => 'text-blue-600 bg-blue-100',
            self::STATUS_CLOSED => 'text-gray-600 bg-gray-100'
        ];

        return $colors[$this->status] ?? 'text-gray-600 bg-gray-100';
    }

    public function hasReply(): bool
    {
        return !empty($this->landlord_reply);
    }

    public function canBeReplied(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REPLIED]);
    }

    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    // Methods
    public function approve(): void
    {
        DB::transaction(function () {
            $this->update(['status' => self::STATUS_APPROVED]);

            // If inquiry has a room selection, increase the occupied count
            if ($this->room_id) {
                $room = $this->room;
                if ($room) {
                    $room->increment('occupied_count');

                    // If room is now at capacity, mark it as occupied
                    if ($room->occupied_count >= $room->capacity) {
                        $room->update(['status' => 'occupied']);
                    }
                }
            }
        });
    }

    public function reject(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'landlord_reply' => $reason,
            'replied_at' => now()
        ]);
    }

    public function reply(string $reply): void
    {
        $this->update([
            'landlord_reply' => $reply,
            'status' => self::STATUS_REPLIED,
            'replied_at' => now()
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    public function releaseRoom(): void
    {
        DB::transaction(function () {
            // If inquiry was approved and has room, decrease occupied count
            if ($this->room_id && $this->status === self::STATUS_APPROVED) {
                $room = $this->room;
                if ($room && $room->occupied_count > 0) {
                    $room->decrement('occupied_count');

                    // If room has available space again, mark it as available
                    if ($room->occupied_count < $room->capacity) {
                        $room->update(['status' => 'available']);
                    }
                }
            }

            $this->update(['status' => self::STATUS_CLOSED]);
        });
    }

    // Scopes
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeForLandlord($query, $landlordId)
    {
        return $query->whereHas('property', function($q) use ($landlordId) {
            $q->where('user_id', $landlordId);
        });
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('user_id', $tenantId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    // Static method to check if tenant has active inquiries (pending or approved)
    public static function tenantHasActiveInquiry($tenantId): bool
    {
        return self::where('user_id', $tenantId)
                   ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
                   ->exists();
    }

    // Static method to get tenant's active inquiry
    public static function getTenantActiveInquiry($tenantId)
    {
        return self::where('user_id', $tenantId)
                   ->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED])
                   ->with(['property', 'room'])
                   ->first();
    }

    // Legacy methods for backward compatibility
    public static function tenantHasPendingInquiry($tenantId): bool
    {
        return self::tenantHasActiveInquiry($tenantId);
    }

    public static function getTenantPendingInquiry($tenantId)
    {
        return self::getTenantActiveInquiry($tenantId);
    }

    // Determine if inquiry blocks new inquiries
    public function preventsNewInquiries(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }
}