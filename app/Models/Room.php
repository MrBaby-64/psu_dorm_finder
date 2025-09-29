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
        'amenities',
        // Physical Details
        'furnished_status',
        'bathroom_type',
        'has_balcony',
        'window_count',
        'flooring_type',
        'ceiling_height',
        // Utilities & Features
        'ac_type',
        'internet_speed_mbps',
        'electrical_outlets',
        'storage_space',
        'has_kitchenette',
        'has_refrigerator',
        'has_study_desk',
        // Safety & Security
        'has_smoke_detector',
        'has_security_camera',
        'has_window_grills',
        'emergency_exit_access',
        // Accessibility
        'wheelchair_accessible',
        'is_ground_floor',
        'elevator_access',
        'floor_level',
        // Maintenance & Condition
        'last_renovated',
        'condition_rating',
        'maintenance_notes',
        'last_inspection',
        // Booking & Policies
        'minimum_stay_months',
        'maximum_stay_months',
        'security_deposit',
        'advance_payment_months',
        'pets_allowed',
        'smoking_allowed',
        'house_rules',
        // Additional Details
        'view_description',
        'included_utilities',
        'special_features',
        'room_orientation'
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
        'amenities' => 'array',
        // Physical Details
        'has_balcony' => 'boolean',
        'window_count' => 'integer',
        'ceiling_height' => 'decimal:2',
        // Utilities & Features
        'internet_speed_mbps' => 'integer',
        'electrical_outlets' => 'integer',
        'has_kitchenette' => 'boolean',
        'has_refrigerator' => 'boolean',
        'has_study_desk' => 'boolean',
        // Safety & Security
        'has_smoke_detector' => 'boolean',
        'has_security_camera' => 'boolean',
        'has_window_grills' => 'boolean',
        'emergency_exit_access' => 'boolean',
        // Accessibility
        'wheelchair_accessible' => 'boolean',
        'is_ground_floor' => 'boolean',
        'elevator_access' => 'boolean',
        'floor_level' => 'integer',
        // Maintenance & Condition
        'last_renovated' => 'date',
        'condition_rating' => 'decimal:1',
        'last_inspection' => 'date',
        // Booking & Policies
        'minimum_stay_months' => 'integer',
        'maximum_stay_months' => 'integer',
        'security_deposit' => 'decimal:2',
        'advance_payment_months' => 'decimal:1',
        'pets_allowed' => 'boolean',
        'smoking_allowed' => 'boolean',
        // Additional Details
        'included_utilities' => 'array'
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

    // Furnished status constants
    const FURNISHED_STATUS_FURNISHED = 'furnished';
    const FURNISHED_STATUS_SEMI_FURNISHED = 'semi_furnished';
    const FURNISHED_STATUS_UNFURNISHED = 'unfurnished';

    // Bathroom type constants
    const BATHROOM_TYPE_PRIVATE = 'private';
    const BATHROOM_TYPE_SHARED = 'shared';
    const BATHROOM_TYPE_COMMUNAL = 'communal';

    // AC type constants
    const AC_TYPE_CENTRAL = 'central';
    const AC_TYPE_WINDOW = 'window';
    const AC_TYPE_SPLIT = 'split';
    const AC_TYPE_CEILING_FAN = 'ceiling_fan';
    const AC_TYPE_NONE = 'none';

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

    public static function getFurnishedStatuses(): array
    {
        return [
            self::FURNISHED_STATUS_FURNISHED => 'Fully Furnished',
            self::FURNISHED_STATUS_SEMI_FURNISHED => 'Semi Furnished',
            self::FURNISHED_STATUS_UNFURNISHED => 'Unfurnished'
        ];
    }

    public static function getBathroomTypes(): array
    {
        return [
            self::BATHROOM_TYPE_PRIVATE => 'Private Bathroom',
            self::BATHROOM_TYPE_SHARED => 'Shared Bathroom',
            self::BATHROOM_TYPE_COMMUNAL => 'Communal Bathroom'
        ];
    }

    public static function getAcTypes(): array
    {
        return [
            self::AC_TYPE_CENTRAL => 'Central AC',
            self::AC_TYPE_WINDOW => 'Window AC',
            self::AC_TYPE_SPLIT => 'Split AC',
            self::AC_TYPE_CEILING_FAN => 'Ceiling Fan',
            self::AC_TYPE_NONE => 'No AC/Fan'
        ];
    }

    public static function getFlooringTypes(): array
    {
        return [
            'tile' => 'Tile',
            'wood' => 'Wood',
            'concrete' => 'Concrete',
            'carpet' => 'Carpet',
            'vinyl' => 'Vinyl',
            'laminate' => 'Laminate'
        ];
    }

    public static function getStorageTypes(): array
    {
        return [
            'closet' => 'Closet',
            'wardrobe' => 'Wardrobe',
            'built_in' => 'Built-in Storage',
            'none' => 'No Storage'
        ];
    }

    public static function getViewTypes(): array
    {
        return [
            'city' => 'City View',
            'garden' => 'Garden View',
            'courtyard' => 'Courtyard View',
            'street' => 'Street View',
            'mountain' => 'Mountain View',
            'parking' => 'Parking View'
        ];
    }

    public static function getRoomOrientations(): array
    {
        return [
            'north' => 'North',
            'south' => 'South',
            'east' => 'East',
            'west' => 'West',
            'northeast' => 'Northeast',
            'northwest' => 'Northwest',
            'southeast' => 'Southeast',
            'southwest' => 'Southwest'
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

    // New Enhanced Accessors
    public function getFurnishedStatusNameAttribute(): string
    {
        return self::getFurnishedStatuses()[$this->furnished_status] ?? 'Unknown';
    }

    public function getBathroomTypeNameAttribute(): string
    {
        return self::getBathroomTypes()[$this->bathroom_type] ?? 'Unknown';
    }

    public function getAcTypeNameAttribute(): string
    {
        return self::getAcTypes()[$this->ac_type] ?? 'None';
    }

    public function getFlooringTypeNameAttribute(): string
    {
        return self::getFlooringTypes()[$this->flooring_type] ?? 'Not specified';
    }

    public function getStorageSpaceNameAttribute(): string
    {
        return self::getStorageTypes()[$this->storage_space] ?? 'Not specified';
    }

    public function getViewDescriptionNameAttribute(): string
    {
        return self::getViewTypes()[$this->view_description] ?? 'Not specified';
    }

    public function getRoomOrientationNameAttribute(): string
    {
        return self::getRoomOrientations()[$this->room_orientation] ?? 'Not specified';
    }

    public function getFormattedSecurityDepositAttribute(): string
    {
        return $this->security_deposit ? '₱' . number_format($this->security_deposit, 2) : 'Not required';
    }

    public function getAvailableSpacesAttribute(): int
    {
        return max(0, $this->capacity - $this->occupied_count);
    }

    public function getOccupancyRateAttribute(): float
    {
        return $this->capacity > 0 ? ($this->occupied_count / $this->capacity) * 100 : 0;
    }

    public function getFormattedConditionRatingAttribute(): string
    {
        $rating = (float) $this->condition_rating;
        $stars = str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating);
        return $stars . ' (' . number_format($rating, 1) . '/5.0)';
    }

    public function getUtilitiesListAttribute(): array
    {
        return $this->included_utilities ?? [];
    }

    public function hasUtilityAttribute(): \Closure
    {
        return function (string $utility) {
            return in_array($utility, $this->getUtilitiesListAttribute());
        };
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