<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Cloudinary\Api\Upload\UploadApi;

/**
 * Property Image Controller
 * Handles property image uploads and management
 */
class PropertyImageController extends Controller
{
    // Upload property images
    public function upload(Request $request, Property $property)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
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
                // Upload to Cloudinary
                $uploadResult = $cloudinary->uploadApi()->upload(
                    $image->getRealPath(),
                    [
                        'folder' => config('cloudinary.folders.properties'),
                        'public_id' => 'property_' . $property->id . '_' . time() . '_' . $index,
                        'resource_type' => 'image',
                        'transformation' => [
                            'quality' => 'auto',
                            'fetch_format' => 'auto'
                        ]
                    ]
                );

                // Get Cloudinary URL
                $cloudinaryUrl = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];

                // Get current count and max sort order (PostgreSQL compatible)
                $currentCount = PropertyImage::where('property_id', $property->id)->count();
                $maxSortOrder = PropertyImage::where('property_id', $property->id)->max('sort_order');

                // Handle NULL from max() in PostgreSQL when no records exist
                $nextSortOrder = ($maxSortOrder === null) ? 0 : ($maxSortOrder + 1);

                // Create image record with Cloudinary URL
                $propertyImage = PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $cloudinaryUrl,
                    'cloudinary_public_id' => $publicId,
                    'alt_text' => $property->title . ' - Image ' . ($index + 1),
                    'is_cover' => $currentCount === 0, // First image is cover
                    'sort_order' => $nextSortOrder,
                ]);

                $uploadedImages[] = $propertyImage;
            } catch (\Exception $e) {
                // Log error and continue with next image
                \Log::error('Cloudinary upload failed: ' . $e->getMessage());
                continue;
            }
        }

        if (count($uploadedImages) === 0) {
            return redirect()->back()->with('error', 'Failed to upload images. Please try again.');
        }

        return redirect()->back()->with('success', count($uploadedImages) . ' images uploaded successfully!');
    }

    public function setCover(Property $property, PropertyImage $image)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        // Remove cover from all images
        PropertyImage::where('property_id', $property->id)
            ->update(['is_cover' => false]);

        // Set new cover
        $image->update(['is_cover' => true]);

        return redirect()->back()->with('success', 'Cover image updated!');
    }

    public function delete(Property $property, PropertyImage $image)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
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
            $newCover = PropertyImage::where('property_id', $property->id)
                ->orderBy('sort_order')
                ->first();
            if ($newCover) {
                $newCover->update(['is_cover' => true]);
            }
        }

        return redirect()->back()->with('success', 'Image deleted successfully!');
    }

    public function reorder(Request $request, Property $property)
    {
        // Ensure landlord owns this property
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:property_images,id',
            'images.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->images as $imageData) {
            PropertyImage::where('id', $imageData['id'])
                ->update(['sort_order' => $imageData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}