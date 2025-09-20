<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'url',
        'alt',
        'is_cover',
        'sort_order'
    ];

    protected $casts = [
        'is_cover' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}