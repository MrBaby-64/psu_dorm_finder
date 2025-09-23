<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'booking_id',
        'rating',
        'comment',
        'cleanliness_rating',
        'location_rating',
        'value_rating',
        'communication_rating',
        'is_verified',
        'landlord_reply',
        'landlord_reply_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'cleanliness_rating' => 'integer',
        'location_rating' => 'integer',
        'value_rating' => 'integer',
        'communication_rating' => 'integer',
        'is_verified' => 'boolean',
        'landlord_reply_at' => 'datetime'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Accessors
    public function getStarRatingAttribute(): string
    {
        return str_repeat('⭐', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getRatingColorAttribute(): string
    {
        if ($this->rating >= 4) return 'text-green-600';
        if ($this->rating >= 3) return 'text-yellow-600';
        if ($this->rating >= 2) return 'text-orange-600';
        return 'text-red-600';
    }

    public function getOverallScoreAttribute(): float
    {
        $scores = array_filter([
            $this->rating,
            $this->cleanliness_rating,
            $this->location_rating,
            $this->value_rating,
            $this->communication_rating
        ]);

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
    }

    public function getReviewerNameAttribute(): string
    {
        // For privacy, show only first name and last initial
        $nameParts = explode(' ', $this->user->name);
        if (count($nameParts) > 1) {
            return $nameParts[0] . ' ' . substr($nameParts[count($nameParts) - 1], 0, 1) . '.';
        }
        return $nameParts[0];
    }

    public function hasLandlordReply(): bool
    {
        return !empty($this->landlord_reply);
    }

    // Methods
    public function addLandlordReply(string $reply): void
    {
        $this->update([
            'landlord_reply' => $reply,
            'landlord_reply_at' => now()
        ]);
    }

    public function markVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    // Scopes
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeMinimumRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeWithLandlordReply($query)
    {
        return $query->whereNotNull('landlord_reply');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Static methods for analytics
    public static function getAverageRatingForProperty($propertyId): float
    {
        return static::where('property_id', $propertyId)->avg('rating') ?: 0;
    }

    public static function getRatingBreakdownForProperty($propertyId): array
    {
        $breakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $breakdown[$i] = static::where('property_id', $propertyId)
                                  ->where('rating', $i)
                                  ->count();
        }
        return $breakdown;
    }
}