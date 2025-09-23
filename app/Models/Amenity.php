<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Amenity categories
    const CATEGORY_BASIC = 'basic';
    const CATEGORY_COMFORT = 'comfort';
    const CATEGORY_ENTERTAINMENT = 'entertainment';
    const CATEGORY_SECURITY = 'security';
    const CATEGORY_FACILITIES = 'facilities';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_BASIC => 'Basic Necessities',
            self::CATEGORY_COMFORT => 'Comfort Features',
            self::CATEGORY_ENTERTAINMENT => 'Entertainment',
            self::CATEGORY_SECURITY => 'Security Features',
            self::CATEGORY_FACILITIES => 'Common Facilities'
        ];
    }

    // Relationships
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    // Accessors
    public function getCategoryNameAttribute(): string
    {
        return self::getCategories()[$this->category] ?? 'Other';
    }

    public function getIconHtmlAttribute(): string
    {
        return $this->icon ? "<i class='{$this->icon}'></i>" : '';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}