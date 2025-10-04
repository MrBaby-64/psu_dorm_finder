<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'image_path',
        'cloudinary_public_id',
        'alt_text',
        'is_cover',
        'sort_order'
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Add accessor for description (alias for alt_text)
    public function getDescriptionAttribute(): ?string
    {
        return $this->alt_text;
    }

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Accessors
    public function getFullUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return '';
        }

        // Check if it's a Cloudinary URL (starts with http:// or https://)
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path; // Return Cloudinary URL directly
        }

        // For local storage images, check if file exists before generating URL
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        // If file doesn't exist, return empty string to trigger fallback
        return '';
    }

    public function getThumbnailUrlAttribute(): string
    {
        // For Cloudinary URLs, use Cloudinary transformation for thumbnails
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            // Use Cloudinary URL transformation to create thumbnail
            // Replace /upload/ with /upload/w_300,h_200,c_fill/ for automatic thumbnail
            return str_replace('/upload/', '/upload/w_300,h_200,c_fill/', $this->image_path);
        }

        // For local storage, generate thumbnail path
        $pathInfo = pathinfo($this->image_path);
        $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];

        if (Storage::disk('public')->exists($thumbnailPath)) {
            return Storage::disk('public')->url($thumbnailPath);
        }

        return $this->full_url; // Fallback to original image
    }

    public function getFileSizeAttribute(): string
    {
        $bytes = Storage::disk('public')->size($this->image_path);
        return $this->formatBytes($bytes);
    }

    // Methods
    public function setCover(): void
    {
        // Remove cover from all other images of this property
        static::where('property_id', $this->property_id)
              ->where('id', '!=', $this->id)
              ->update(['is_cover' => false]);

        // Set this image as cover
        $this->update(['is_cover' => true]);
    }

    public function delete(): bool
    {
        // Only delete from local storage if it's NOT a Cloudinary URL
        if (!str_starts_with($this->image_path, 'http://') && !str_starts_with($this->image_path, 'https://')) {
            // Delete the file from local storage
            if (Storage::disk('public')->exists($this->image_path)) {
                Storage::disk('public')->delete($this->image_path);
            }

            // Delete thumbnail if it exists
            $pathInfo = pathinfo($this->image_path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }
        // Note: Cloudinary deletion is handled in the PropertyImageController

        // If this was the cover image, set another image as cover
        if ($this->is_cover) {
            $newCover = static::where('property_id', $this->property_id)
                             ->where('id', '!=', $this->id)
                             ->orderBy('sort_order')
                             ->first();
            if ($newCover) {
                $newCover->setCover();
            }
        }

        return parent::delete();
    }

    // Helper method to format bytes
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    // Scopes
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeCover($query)
    {
        return $query->where('is_cover', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}