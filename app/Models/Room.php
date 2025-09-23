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
        'status',
        'description',
        'amenities'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'size_sqm' => 'decimal:1',
        'capacity' => 'integer',
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
}