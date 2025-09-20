<?php
// app/Http/Controllers/Landlord/PropertyImageController.php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends Controller
{
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

            // Create image record
            $propertyImage = PropertyImage::create([
                'property_id' => $property->id,
                'url' => $path,
                'alt' => $property->title . ' - Image ' . ($index + 1),
                'is_cover' => PropertyImage::where('property_id', $property->id)->count() === 0, // First image is cover
                'sort_order' => PropertyImage::where('property_id', $property->id)->max('sort_order') + 1,
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
        Storage::disk('public')->delete($image->url);

        // Delete record
        $image->delete();

        // If this was the cover, set another image as cover
        if ($image->is_cover) {
            $newCover = PropertyImage::where('property_id', $property->id)->first();
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