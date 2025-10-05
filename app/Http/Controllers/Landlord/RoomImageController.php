<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;

/**
 * Room Image Controller
 * Handles room image uploads and management
 */
class RoomImageController extends Controller
{
    // Upload room images
    public function upload(Request $request, Property $property, Room $room)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure room belongs to property
        if ($room->property_id !== $property->id) {
            abort(403);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|file|mimes:jpeg,jpg,png,webp,heic,heif|max:40960', // 40MB max - supports high-quality camera photos
        ], [
            'images.*.mimes' => '📸 Only image files (JPEG, PNG, WebP, HEIC) are allowed. Please select valid image files.',
            'images.*.max' => '📏 Image file too large! Each image must not exceed 40MB. Try compressing your image or use a smaller resolution.',
            'images.required' => '🖼️ Please select at least one image for this room.',
            'images.max' => '📊 Too many images! You can upload maximum 10 images per room.',
        ]);

        $uploadedImages = [];

        // Initialize Cloudinary
        $cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ]
        ]);

        foreach ($request->file('images') as $index => $image) {
            try {
                // Upload to Cloudinary with automatic optimization
                $uploadResult = $cloudinary->uploadApi()->upload(
                    $image->getRealPath(),
                    [
                        'folder' => config('cloudinary.folders.rooms'),
                        'public_id' => 'room_' . $room->id . '_' . time() . '_' . $index,
                        'resource_type' => 'image',
                        'transformation' => [
                            'quality' => 'auto:good', // Automatic quality optimization
                            'fetch_format' => 'auto', // Automatic format selection (WebP when supported)
                        ],
                        // Server-side compression settings
                        'eager' => [
                            ['width' => 1920, 'height' => 1080, 'crop' => 'limit', 'quality' => 'auto:good'],
                            ['width' => 800, 'height' => 600, 'crop' => 'limit', 'quality' => 'auto:good']
                        ],
                        'eager_async' => true,
                    ]
                );

                // Get Cloudinary URL
                $cloudinaryUrl = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];

                // Get current count and max sort order (PostgreSQL compatible)
                $currentCount = RoomImage::where('room_id', $room->id)->count();
                $maxSortOrder = RoomImage::where('room_id', $room->id)->max('sort_order');

                // Handle NULL from max() in PostgreSQL when no records exist
                $nextSortOrder = ($maxSortOrder === null) ? 0 : ($maxSortOrder + 1);

                // Create image record with Cloudinary URL
                $roomImage = RoomImage::create([
                    'room_id' => $room->id,
                    'image_path' => $cloudinaryUrl,
                    'cloudinary_public_id' => $publicId,
                    'alt_text' => $room->room_number . ' - Image ' . ($index + 1),
                    'is_cover' => $currentCount === 0, // First image is cover
                    'sort_order' => $nextSortOrder,
                ]);

                $uploadedImages[] = $roomImage;
            } catch (\Cloudinary\Api\Exception\ApiError $e) {
                // Handle Cloudinary-specific errors
                \Log::error('Cloudinary upload failed: ' . $e->getMessage(), [
                    'room_id' => $room->id,
                    'image_index' => $index,
                    'error_code' => $e->getCode()
                ]);
                continue;
            } catch (\Exception $e) {
                // Handle general errors (e.g., file too large, memory issues)
                \Log::error('Image upload failed: ' . $e->getMessage(), [
                    'room_id' => $room->id,
                    'image_index' => $index
                ]);
                continue;
            }
        }

        if (count($uploadedImages) === 0) {
            return redirect()->back()->with('error', 'Failed to upload images. Please try again.');
        }

        return redirect()->back()->with('success', count($uploadedImages) . ' images uploaded successfully for ' . $room->room_number . '!');
    }

    public function setCover(Property $property, Room $room, RoomImage $image)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure room belongs to property
        if ($room->property_id !== $property->id) {
            abort(403);
        }

        // Ensure image belongs to room
        if ($image->room_id !== $room->id) {
            abort(403);
        }

        // Remove cover from all images
        RoomImage::where('room_id', $room->id)
            ->update(['is_cover' => false]);

        // Set new cover
        $image->update(['is_cover' => true]);

        return redirect()->back()->with('success', 'Cover image updated for ' . $room->room_number . '!');
    }

    public function delete(Property $property, Room $room, RoomImage $image)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure room belongs to property
        if ($room->property_id !== $property->id) {
            abort(403);
        }

        // Ensure image belongs to room
        if ($image->room_id !== $room->id) {
            abort(403);
        }

        try {
            // Delete from Cloudinary if cloudinary_public_id exists
            if ($image->cloudinary_public_id) {
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ]
                ]);

                $cloudinary->uploadApi()->destroy($image->cloudinary_public_id);
            } else {
                // Fallback: Delete from local storage (for legacy images)
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to delete image from Cloudinary: ' . $e->getMessage());
        }

        // Store is_cover status before deletion
        $wasCover = $image->is_cover;

        // Delete record
        $image->delete();

        // If this was the cover, set another image as cover
        if ($wasCover) {
            $newCover = RoomImage::where('room_id', $room->id)
                ->orderBy('sort_order')
                ->first();
            if ($newCover) {
                $newCover->update(['is_cover' => true]);
            }
        }

        return redirect()->back()->with('success', 'Image deleted successfully from ' . $room->room_number . '!');
    }
}
