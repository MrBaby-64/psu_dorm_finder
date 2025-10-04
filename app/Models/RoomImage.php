<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RoomImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
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

    // Relationships
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
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

        // For local storage images
        return Storage::disk('public')->url($this->image_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        // For Cloudinary URLs, use Cloudinary transformation for thumbnails
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            // Use Cloudinary URL transformation to create thumbnail
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
        // Remove cover from all other images of this room
        static::where('room_id', $this->room_id)
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
        // Note: Cloudinary deletion is handled in the controller

        // If this was the cover image, set another image as cover
        if ($this->is_cover) {
            $newCover = static::where('room_id', $this->room_id)
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
    public function scopeForRoom($query, $roomId)
    {
        return $query->where('room_id', $roomId);
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