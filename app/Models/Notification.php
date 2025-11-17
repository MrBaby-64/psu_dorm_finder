<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
        'action_url'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Notification types
    const TYPE_INQUIRY_RECEIVED = 'inquiry_received';
    const TYPE_INQUIRY_REPLIED = 'inquiry_replied';
    const TYPE_BOOKING_RECEIVED = 'booking_received';
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_APPROVED = 'booking_approved';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_COMPLETED = 'booking_completed';
    const TYPE_VISIT_SCHEDULED = 'visit_scheduled';
    const TYPE_VISIT_CONFIRMED = 'visit_confirmed';
    const TYPE_REVIEW_RECEIVED = 'review_received';
    const TYPE_PROPERTY_APPROVED = 'property_approved';
    const TYPE_PROPERTY_REJECTED = 'property_rejected';
    const TYPE_DELETION_REJECTED = 'deletion_rejected';
    const TYPE_MESSAGE_RECEIVED = 'message_received';
    const TYPE_ADMIN_RESPONSE = 'admin_response';
    const TYPE_FAVORITE_ADDED = 'favorite_added';

    public static function getTypes(): array
    {
        return [
            self::TYPE_INQUIRY_RECEIVED => 'New Inquiry Received',
            self::TYPE_INQUIRY_REPLIED => 'Inquiry Reply',
            self::TYPE_BOOKING_RECEIVED => 'New Booking Request',
            self::TYPE_BOOKING_CREATED => 'Booking Created',
            self::TYPE_BOOKING_APPROVED => 'Booking Approved',
            self::TYPE_BOOKING_REJECTED => 'Booking Rejected',
            self::TYPE_BOOKING_CANCELLED => 'Booking Cancelled',
            self::TYPE_BOOKING_COMPLETED => 'Booking Completed',
            self::TYPE_VISIT_SCHEDULED => 'Visit Scheduled',
            self::TYPE_VISIT_CONFIRMED => 'Visit Confirmed',
            self::TYPE_REVIEW_RECEIVED => 'New Review Received',
            self::TYPE_PROPERTY_APPROVED => 'Property Approved',
            self::TYPE_PROPERTY_REJECTED => 'Property Rejected',
            self::TYPE_DELETION_REJECTED => 'Deletion Request Rejected',
            self::TYPE_MESSAGE_RECEIVED => 'New Message',
            self::TYPE_ADMIN_RESPONSE => 'Admin Response',
            self::TYPE_FAVORITE_ADDED => 'Property Favorited'
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getTypeNameAttribute(): string
    {
        return self::getTypes()[$this->type] ?? 'Unknown';
    }

    public function getIconAttribute(): string
    {
        $icons = [
            self::TYPE_INQUIRY_RECEIVED => '💬',
            self::TYPE_INQUIRY_REPLIED => '↩️',
            self::TYPE_BOOKING_RECEIVED => '📋',
            self::TYPE_BOOKING_CREATED => '📋',
            self::TYPE_BOOKING_APPROVED => '✅',
            self::TYPE_BOOKING_REJECTED => '❌',
            self::TYPE_BOOKING_CANCELLED => '⏹️',
            self::TYPE_BOOKING_COMPLETED => '🏁',
            self::TYPE_VISIT_SCHEDULED => '📅',
            self::TYPE_VISIT_CONFIRMED => '✅',
            self::TYPE_REVIEW_RECEIVED => '⭐',
            self::TYPE_PROPERTY_APPROVED => '🎉',
            self::TYPE_PROPERTY_REJECTED => '⚠️',
            self::TYPE_MESSAGE_RECEIVED => '💌',
            self::TYPE_ADMIN_RESPONSE => '🛡️',
            self::TYPE_FAVORITE_ADDED => '❤️'
        ];

        return $icons[$this->type] ?? '🔔';
    }

    public function getColorAttribute(): string
    {
        $colors = [
            self::TYPE_INQUIRY_RECEIVED => 'text-blue-600',
            self::TYPE_INQUIRY_REPLIED => 'text-blue-600',
            self::TYPE_BOOKING_RECEIVED => 'text-yellow-600',
            self::TYPE_BOOKING_CREATED => 'text-yellow-600',
            self::TYPE_BOOKING_APPROVED => 'text-green-600',
            self::TYPE_BOOKING_REJECTED => 'text-red-600',
            self::TYPE_BOOKING_CANCELLED => 'text-gray-600',
            self::TYPE_BOOKING_COMPLETED => 'text-purple-600',
            self::TYPE_VISIT_SCHEDULED => 'text-purple-600',
            self::TYPE_VISIT_CONFIRMED => 'text-green-600',
            self::TYPE_REVIEW_RECEIVED => 'text-orange-600',
            self::TYPE_PROPERTY_APPROVED => 'text-green-600',
            self::TYPE_PROPERTY_REJECTED => 'text-red-600',
            self::TYPE_MESSAGE_RECEIVED => 'text-pink-600',
            self::TYPE_ADMIN_RESPONSE => 'text-indigo-600',
            self::TYPE_FAVORITE_ADDED => 'text-red-600'
        ];

        return $colors[$this->type] ?? 'text-gray-600';
    }

    // Methods
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}