<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    const STATUS_REPLIED = 'replied';
    const STATUS_CLOSED = 'closed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
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

    // Accessors
    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'text-yellow-600 bg-yellow-100',
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
        return $this->status !== self::STATUS_CLOSED;
    }

    // Methods
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
}