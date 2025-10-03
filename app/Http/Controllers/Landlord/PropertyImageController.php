<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        foreach ($request->file('images') as $index => $image) {
            // Store image
            $path = $image->store('properties', 'public');

            // Get current count and max sort order (PostgreSQL compatible)
            $currentCount = PropertyImage::where('property_id', $property->id)->count();
            $maxSortOrder = PropertyImage::where('property_id', $property->id)->max('sort_order');

            // Handle NULL from max() in PostgreSQL when no records exist
            $nextSortOrder = ($maxSortOrder === null) ? 0 : ($maxSortOrder + 1);

            // Create image record
            $propertyImage = PropertyImage::create([
                'property_id' => $property->id,
                'image_path' => $path,
                'alt_text' => $property->title . ' - Image ' . ($index + 1),
                'is_cover' => $currentCount === 0, // First image is cover
                'sort_order' => $nextSortOrder,
            ]);

            $uploadedImages[] = $propertyImage;
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

        // Delete file from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
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