<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Amenity;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this area');
        }

        $properties = Property::where('user_id', auth()->id())
            ->with('images')
            ->latest()
            ->paginate(10);

        $statuses = [
            'approved' => 'Approved',
            'pending' => 'Pending Approval',
            'rejected' => 'Rejected',
        ];

        return view('landlord.properties.index', compact('properties', 'statuses'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        $amenities = Amenity::orderBy('name')->get();

        // Get temp images from session if any
        $formToken = session('property_form_token');
        $tempImages = $formToken ? session("temp_images_{$formToken}", []) : [];

        return view('landlord.properties.create', compact('amenities', 'tempImages'));
    }

    public function removeTempImage(Request $request)
    {
        $formToken = session('property_form_token');
        if (!$formToken) {
            return response()->json(['success' => false]);
        }

        $tempImages = session("temp_images_{$formToken}", []);
        $imagePath = $request->image_path;
        $index = $request->index;

        // Remove from session
        $tempImages = array_filter($tempImages, function($img) use ($imagePath) {
            return $img['path'] !== $imagePath;
        });

        session(["temp_images_{$formToken}" => array_values($tempImages)]);

        // Delete file if exists
        if (\Storage::disk('public')->exists($imagePath)) {
            \Storage::disk('public')->delete($imagePath);
        }

        return response()->json(['success' => true]);
    }

    public function storeMapPosition(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        session([
            'map_latitude' => $request->latitude,
            'map_longitude' => $request->longitude
        ]);

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        $uploadedFiles = [];

        try {
            // Handle image uploads - simplified approach
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $tempPath = $image->store('temp/properties', 'public');

                    $uploadedFiles[] = [
                        'path' => $tempPath,
                        'filename' => $filename,
                        'index' => $index,
                        'original_name' => $image->getClientOriginalName()
                    ];
                }
            }

        // Validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:50',
                'location_text' => 'required|string|max:255',
                'address_line' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'city' => 'required|in:Bacolor,San Fernando',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'price' => 'required|numeric|min:500|max:50000',
                'room_count' => 'required|integer|min:1|max:100',
                'amenities' => 'required|array|min:1',
                'amenities.*' => 'exists:amenities,id',
                'visit_schedule_enabled' => 'nullable|boolean',
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            ];

            // Add conditional validation for visit scheduling
            if ($request->has('visit_schedule_enabled') && $request->visit_schedule_enabled) {
                $rules['visit_days'] = 'required|array|min:1';
                $rules['visit_days.*'] = 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday';
                $rules['visit_time_start'] = 'required|date_format:H:i';
                $rules['visit_time_end'] = 'required|date_format:H:i|after:visit_time_start';
                $rules['visit_duration'] = 'required|integer|in:30,45,60,90,120';
                $rules['visit_instructions'] = 'nullable|string|max:500';
            } else {
                $rules['visit_days'] = 'nullable|array';
                $rules['visit_days.*'] = 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday';
                $rules['visit_time_start'] = 'nullable|date_format:H:i';
                $rules['visit_time_end'] = 'nullable|date_format:H:i|after:visit_time_start';
                $rules['visit_duration'] = 'nullable|integer|in:30,45,60,90,120';
                $rules['visit_instructions'] = 'nullable|string|max:500';
            }

            $validated = $request->validate($rules, [
                'title.required' => 'Property title is required.',
                'description.required' => 'Property description is required.',
                'description.min' => 'Property description must be at least 50 characters.',
                'location_text.required' => 'Location description is required.',
                'address_line.required' => 'Street address is required.',
                'barangay.required' => 'Barangay is required.',
                'city.required' => 'City selection is required.',
                'city.in' => 'City must be either Bacolor or San Fernando.',
                'latitude.required' => 'Latitude coordinate is required.',
                'latitude.between' => 'Latitude must be between -90 and 90.',
                'longitude.required' => 'Longitude coordinate is required.',
                'longitude.between' => 'Longitude must be between -180 and 180.',
                'price.required' => 'Monthly rate is required.',
                'price.min' => 'Monthly rate must be at least ₱500.',
                'price.max' => 'Monthly rate cannot exceed ₱50,000.',
                'room_count.required' => 'Number of rooms is required.',
                'room_count.min' => 'Must have at least 1 room.',
                'room_count.max' => 'Cannot exceed 100 rooms.',
                'amenities.required' => 'At least one amenity must be selected.',
                'amenities.min' => 'Please select at least one amenity.',
                'images.required' => 'At least one property image is required.',
                'images.min' => 'Please upload at least one image.',
                'images.max' => 'Maximum 10 images allowed.',
                'images.*.image' => 'All files must be images.',
                'images.*.mimes' => 'Images must be JPEG, JPG, PNG, or WEBP format.',
                'images.*.max' => 'Each image must be under 5MB.',
                // Visit scheduling error messages
                'visit_days.required' => 'Please select at least one available day for visits.',
                'visit_days.min' => 'Please select at least one available day for visits.',
                'visit_time_start.required' => 'Start time is required when visit scheduling is enabled.',
                'visit_time_end.required' => 'End time is required when visit scheduling is enabled.',
                'visit_time_end.after' => 'End time must be after start time.',
                'visit_duration.required' => 'Visit duration is required when visit scheduling is enabled.',
            ]);

            // Wrap all operations in transaction
            DB::transaction(function () use ($validated, $uploadedFiles, $request) {
                // Create the property
                $property = Property::create([
                    'user_id' => auth()->id(),
                    'title' => $validated['title'],
                    'slug' => \Str::slug($validated['title']),
                    'description' => $validated['description'],
                    'location_text' => $validated['location_text'],
                    'address_line' => $validated['address_line'],
                    'barangay' => $validated['barangay'],
                    'city' => $validated['city'],
                    'province' => 'Pampanga',
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'price' => $validated['price'],
                    'room_count' => $validated['room_count'],
                    'visit_schedule_enabled' => $request->boolean('visit_schedule_enabled'),
                    'visit_days' => $request->boolean('visit_schedule_enabled') ? ($validated['visit_days'] ?? null) : null,
                    'visit_time_start' => $request->boolean('visit_schedule_enabled') ? ($validated['visit_time_start'] ?? null) : null,
                    'visit_time_end' => $request->boolean('visit_schedule_enabled') ? ($validated['visit_time_end'] ?? null) : null,
                    'visit_duration' => $request->boolean('visit_schedule_enabled') ? ($validated['visit_duration'] ?? null) : null,
                    'visit_instructions' => $request->boolean('visit_schedule_enabled') ? ($validated['visit_instructions'] ?? null) : null,
                    'approval_status' => 'pending', // Set to pending for admin approval
                ]);

                // Handle property images
                foreach ($uploadedFiles as $index => $fileData) {
                    // Move from temp to permanent
                    $permanentPath = str_replace('temp/properties', 'properties', $fileData['path']);
                    \Storage::disk('public')->move($fileData['path'], $permanentPath);

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'image_path' => $permanentPath,
                        'alt_text' => $validated['title'] . ' - Image ' . ($index + 1),
                        'is_cover' => $index === 0,
                        'sort_order' => $index
                    ]);
                }

                // Attach selected amenities
                if (!empty($validated['amenities'])) {
                    $property->amenities()->attach($validated['amenities']);
                }
            });

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property created successfully! It will be reviewed by admin before being published.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            // Clean up uploaded files on error
            foreach ($uploadedFiles as $fileData) {
                if (\Storage::disk('public')->exists($fileData['path'])) {
                    \Storage::disk('public')->delete($fileData['path']);
                }
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create property. Please try again.']);
        }
    }

    public function edit(Property $property)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $amenities = Amenity::orderBy('name')->get();

        return view('landlord.properties.edit', compact('property', 'amenities'));
    }

    public function update(Request $request, Property $property)
    {
        if ($property->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_text' => 'required|string|max:255',
            'address_line' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'price' => 'required|numeric|min:0',
            'room_count' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'visit_schedule_enabled' => 'nullable|boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        Log::info('Property update attempt', [
            'property_id' => $property->id,
            'landlord_id' => auth()->id(),
            'validated_keys' => array_keys($validated)
        ]);

        try {
            DB::transaction(function () use ($property, $validated, $request) {
                // Update property
                $property->update([
                    'title' => $validated['title'],
                    'slug' => \Str::slug($validated['title']),
                    'description' => $validated['description'],
                    'location_text' => $validated['location_text'],
                    'address_line' => $validated['address_line'],
                    'barangay' => $validated['barangay'],
                    'city' => $validated['city'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'price' => $validated['price'],
                    'room_count' => $validated['room_count'],
                    'visit_schedule_enabled' => $request->has('visit_schedule_enabled'),
                ]);

                // Update amenities
                if ($request->has('amenities')) {
                    $property->amenities()->sync($validated['amenities']);
                }

                // Handle new image uploads
                if ($request->hasFile('images')) {
                    $currentImageCount = $property->images->count();

                    foreach ($request->file('images') as $index => $image) {
                        $path = $image->store('properties', 'public');

                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_path' => $path,
                            'alt_text' => $property->title . ' - Image ' . ($currentImageCount + $index + 1),
                            'is_cover' => $currentImageCount === 0 && $index === 0, // First image is cover if no existing images
                            'sort_order' => $currentImageCount + $index
                        ]);
                    }
                }
            });

            Log::info('Property update successful', [
                'property_id' => $property->id,
                'landlord_id' => auth()->id(),
                'updated_fields' => array_keys($validated)
            ]);

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property updated successfully!');

        } catch (\Exception $e) {
            Log::error('Property update failed', [
                'property_id' => $property->id,
                'landlord_id' => auth()->id(),
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            return back()->withInput()->withErrors(['general' => 'Property update failed. Please try again.']);
        }
    }
}