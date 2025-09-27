<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_number',
        'room_type',
        'price',
        'size_sqm',
        'capacity',
        'occupied_count',
        'status',
        'description',
        'amenities'
    ];

    // Validation rules for room creation
    public static function getValidationRules(): array
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|string|in:single,shared,studio,one_bedroom,bedspace',
            'price' => 'required|numeric|min:0|max:100000',
            'size_sqm' => 'nullable|numeric|min:1|max:500',
            'capacity' => 'required|integer|min:1|max:10',
            'status' => 'required|string|in:available,occupied,maintenance,reserved',
            'description' => 'nullable|string|max:1000',
            'amenities' => 'nullable|array'
        ];
    }

    protected $casts = [
        'price' => 'decimal:2',
        'size_sqm' => 'decimal:1',
        'capacity' => 'integer',
        'occupied_count' => 'integer',
        'amenities' => 'array'
    ];

    // Room types
    const TYPE_SINGLE = 'single';
    const TYPE_SHARED = 'shared';
    const TYPE_STUDIO = 'studio';
    const TYPE_ONE_BEDROOM = 'one_bedroom';
    const TYPE_BEDSPACE = 'bedspace';

    // Room statuses
    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_MAINTENANCE = 'maintenance';
    const STATUS_RESERVED = 'reserved';

    public static function getTypes(): array
    {
        return [
            self::TYPE_SINGLE => 'Single Room',
            self::TYPE_SHARED => 'Shared Room',
            self::TYPE_STUDIO => 'Studio',
            self::TYPE_ONE_BEDROOM => 'One Bedroom',
            self::TYPE_BEDSPACE => 'Bedspace'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_OCCUPIED => 'Occupied',
            self::STATUS_MAINTENANCE => 'Under Maintenance',
            self::STATUS_RESERVED => 'Reserved'
        ];
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->orderBy('sort_order');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    public function getTypeNameAttribute(): string
    {
        return self::getTypes()[$this->room_type] ?? 'Unknown';
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_AVAILABLE => 'text-green-600 bg-green-100',
            self::STATUS_OCCUPIED => 'text-red-600 bg-red-100',
            self::STATUS_MAINTENANCE => 'text-yellow-600 bg-yellow-100',
            self::STATUS_RESERVED => 'text-blue-600 bg-blue-100'
        ];

        return $colors[$this->status] ?? 'text-gray-600 bg-gray-100';
    }

    public function getFullNameAttribute(): string
    {
        return $this->room_number . ' (' . $this->type_name . ')';
    }

    public function getCoverImageAttribute(): ?RoomImage
    {
        return $this->images()->where('is_cover', true)->first();
    }

    public function getCoverImageUrlAttribute(): string
    {
        $cover = $this->cover_image;
        return $cover ? asset('storage/' . $cover->image_path) : asset('images/placeholder-room.jpg');
    }

    public function getImageCountAttribute(): int
    {
        return $this->images()->count();
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function reserve(): void
    {
        $this->update(['status' => self::STATUS_RESERVED]);
    }

    public function occupy(): void
    {
        $this->update(['status' => self::STATUS_OCCUPIED]);
    }

    public function makeAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE]);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeOccupied($query)
    {
        return $query->where('status', self::STATUS_OCCUPIED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('room_type', $type);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    // Boot method for model events
    protected static function booted()
    {
        // Ensure data integrity before saving
        static::saving(function ($room) {
            // Ensure capacity is within valid range
            if ($room->capacity < 1) {
                $room->capacity = 1;
            } elseif ($room->capacity > 10) {
                $room->capacity = 10;
            }

            // Ensure price is not negative
            if ($room->price < 0) {
                $room->price = 0;
            }

            // Trim whitespace from string fields
            $room->room_number = trim($room->room_number);
            $room->description = $room->description ? trim($room->description) : null;
        });
    }
}