<?php
// app/Models/Property.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'location_text',
        'address_line',
        'barangay',
        'city',
        'province',
        'latitude',
        'longitude',
        'price',
        'room_count',
        'approval_status',
        'rejection_reason',
        'is_verified',
        'is_featured',
        'display_priority',
        'rating_avg',
        'rating_count',
        'visit_schedule_enabled',
        'visit_days',
        'visit_time_start',
        'visit_time_end',
        'visit_duration',
        'visit_instructions'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'visit_schedule_enabled' => 'boolean',
        'visit_days' => 'array'
    ];

    // Auto-generate slug when title changes
    protected static function booted()
    {
        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = static::generateUniqueSlug($property->title);
            }
        });

        static::updating(function ($property) {
            if ($property->isDirty('title')) {
                $property->slug = static::generateUniqueSlug($property->title, $property->id);
            }
        });
    }

    public static function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

// Add these relationships to your existing Property.php file

public function transactions(): HasMany
{
    return $this->hasMany(Transaction::class);
}

public function scheduledVisits(): HasMany
{
    return $this->hasMany(ScheduledVisit::class);
}

// Method to check if visit scheduling is enabled
public function isVisitSchedulingEnabled(): bool
{
    return $this->visit_schedule_enabled ?? false;
}

// Method to get pending visits count
public function getPendingVisitsCountAttribute(): int
{
    return $this->scheduledVisits()->pending()->count();
}

    // Relationships
    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function coverImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_cover', true);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function deletionRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PropertyDeletionRequest::class);
    }

    public function deletionRequests(): HasMany
    {
        return $this->hasMany(PropertyDeletionRequest::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeInCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    // Accessors
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getCoverImageUrlAttribute()
    {
        $cover = $this->coverImage;
        return $cover ? asset('storage/' . $cover->url) : asset('images/placeholder-property.jpg');
    }

    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    public function getDistanceFromCampusAttribute()
    {
        // This will be set dynamically by BFS service
        return $this->attributes['distance_meters'] ?? null;
    }

    // Methods
    public function updateRatingCache()
    {
        $reviews = $this->reviews;
        $this->update([
            'rating_avg' => $reviews->avg('rating') ?: 0,
            'rating_count' => $reviews->count()
        ]);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
    public function inquiries()
    {
    return $this->hasMany(Inquiry::class);
    }
}