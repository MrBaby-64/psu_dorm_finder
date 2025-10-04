<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use App\Models\PropertyImage;
use App\Models\PropertyDeletionRequest;
use App\Models\AdminMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Cloudinary\Cloudinary;

/**
 * Landlord Property Controller
 * Manages property listings, creation, editing, and deletion
 */
class PropertyController extends Controller
{
    // List all properties owned by landlord
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this area');
        }

        // SIMPLEST APPROACH: Use Eloquent like localhost
        try {
            $query = Property::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc');

            // Apply search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('location_text', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('approval_status', $request->status);
            }

            $properties = $query->paginate(10)->withQueryString();

            $statuses = [
                'approved' => 'Approved',
                'pending' => 'Pending Approval',
                'rejected' => 'Rejected',
            ];

            return view('landlord.properties.index', compact('properties', 'statuses'));

        } catch (\Exception $e) {
            Log::error('Properties error: ' . $e->getMessage());

            // Return empty
            $properties = Property::where('id', 0)->paginate(10);
            $statuses = [
                'approved' => 'Approved',
                'pending' => 'Pending Approval',
                'rejected' => 'Rejected',
            ];

            return view('landlord.properties.index', compact('properties', 'statuses'));
        }
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        try {
            // Use direct DB query for PostgreSQL compatibility
            $amenities = DB::table('amenities')
                ->orderBy('name')
                ->get();

            // Get temp images from session if any
            $formToken = session('property_form_token');
            $tempImages = $formToken ? session("temp_images_{$formToken}", []) : [];

            return view('landlord.properties.create', compact('amenities', 'tempImages'));

        } catch (\Exception $e) {
            Log::error('Property create page error: ' . $e->getMessage());

            // Return with empty amenities on error
            $amenities = collect([]);
            $tempImages = [];

            return view('landlord.properties.create', compact('amenities', 'tempImages'));
        }
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
            // Handle image uploads - upload directly to Cloudinary if configured, otherwise use local storage
            if ($request->hasFile('images')) {
                // Check if Cloudinary is configured
                $useCloudinary = !empty(config('cloudinary.cloud_name'));

                if ($useCloudinary) {
                    // Upload directly to Cloudinary
                    $cloudinary = new Cloudinary([
                        'cloud' => [
                            'cloud_name' => config('cloudinary.cloud_name'),
                            'api_key' => config('cloudinary.api_key'),
                            'api_secret' => config('cloudinary.api_secret'),
                        ]
                    ]);

                    foreach ($request->file('images') as $index => $image) {
                        try {
                            $uploadResult = $cloudinary->uploadApi()->upload(
                                $image->getRealPath(),
                                [
                                    'folder' => config('cloudinary.folders.properties'),
                                    'public_id' => 'property_temp_' . time() . '_' . $index,
                                    'resource_type' => 'image',
                                    'transformation' => [
                                        'quality' => 'auto',
                                        'fetch_format' => 'auto'
                                    ]
                                ]
                            );

                            $uploadedFiles[] = [
                                'cloudinary_url' => $uploadResult['secure_url'],
                                'cloudinary_public_id' => $uploadResult['public_id'],
                                'index' => $index,
                                'original_name' => $image->getClientOriginalName()
                            ];
                        } catch (\Exception $e) {
                            Log::error('Cloudinary upload failed during property creation: ' . $e->getMessage());
                            throw new \Exception('Failed to upload images. Please try again.');
                        }
                    }
                } else {
                    // Local: Use temp storage
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
            }

        // Validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:50',
                'house_rules' => 'nullable|array',
                'house_rules.*' => 'nullable|string|max:500',
                'location_text' => 'required|string|max:255',
                'address_line' => 'required|string|max:255',
                'barangay' => 'required|string|max:255',
                'city' => 'required|in:Bacolor,San Fernando',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'price' => 'required|numeric|min:500|max:50000',
                'room_count' => 'required|integer|min:1|max:100',
                'rooms' => 'nullable|array',
                'rooms.*.name' => 'nullable|string|max:255',
                'rooms.*.capacity' => 'nullable|integer|min:1|max:10',
                'rooms.*.description' => 'nullable|string|max:500',
                'room_details' => 'nullable|array',
                'room_details.*' => 'nullable|array',
                'room_details.*.description' => 'nullable|string|max:1000',
                'room_details.*.size_sqm' => 'nullable|numeric|min:0',
                'room_details.*.price' => 'nullable|numeric|min:0',
                'room_details.*.furnished_status' => 'nullable|string|in:furnished,semi_furnished,unfurnished',
                'room_details.*.bathroom_type' => 'nullable|string|in:private,shared,communal',
                'room_details.*.flooring_type' => 'nullable|string|in:tile,wood,concrete,carpet,vinyl',
                'room_details.*.ac_type' => 'nullable|string|in:central,split,window,ceiling_fan,none',
                'room_details.*.internet_speed_mbps' => 'nullable|integer|min:0',
                'room_details.*.storage_space' => 'nullable|string|in:closet,wardrobe,built_in,none',
                'room_details.*.has_kitchenette' => 'nullable|boolean',
                'room_details.*.has_refrigerator' => 'nullable|boolean',
                'room_details.*.has_study_desk' => 'nullable|boolean',
                'room_details.*.has_balcony' => 'nullable|boolean',
                'room_details.*.security_deposit' => 'nullable|numeric|min:0',
                'room_details.*.advance_payment_months' => 'nullable|integer|min:1|max:12',
                'room_details.*.minimum_stay_months' => 'nullable|integer|min:1|max:24',
                'room_details.*.pets_allowed' => 'nullable|boolean',
                'room_details.*.smoking_allowed' => 'nullable|boolean',
                'room_details.*.house_rules' => 'nullable|string|max:1000',
                'room_details.*.included_utilities' => 'nullable|string',
                'room_images' => 'nullable|array',
                'room_images.*' => 'nullable|array|max:5',
                'room_images.*.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
                'amenities' => 'required|array|min:1',
                'amenities.*' => 'exists:amenities,id',
                'visit_schedule_enabled' => 'nullable',
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            ];

            // Add conditional validation for visit scheduling
            if ($request->boolean('visit_schedule_enabled')) {
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
                'rooms.required' => 'Room details are required.',
                'rooms.*.name.required' => 'Room name is required.',
                'rooms.*.capacity.required' => 'Room capacity is required.',
                'rooms.*.capacity.min' => 'Room capacity must be at least 1.',
                'rooms.*.capacity.max' => 'Room capacity cannot exceed 10.',
                'amenities.required' => 'At least one amenity must be selected.',
                'amenities.min' => 'Please select at least one amenity.',
                'images.required' => 'At least one property image is required.',
                'images.min' => 'Please upload at least one image.',
                'images.max' => 'Maximum 10 images allowed.',
                'images.*.image' => 'All files must be images.',
                'images.*.mimes' => 'Images must be JPEG, JPG, PNG, or WEBP format.',
                'images.*.max' => 'Each image must be under 5MB.',
                // Visit scheduling error messages
                'visit_days.required' => 'Please select at least one available day for visits when scheduling is enabled.',
                'visit_days.min' => 'Please select at least one available day for visits when scheduling is enabled.',
                'visit_time_start.required' => 'Start time is required when visit scheduling is enabled.',
                'visit_time_end.required' => 'End time is required when visit scheduling is enabled.',
                'visit_time_end.after' => 'End time must be after start time.',
                'visit_duration.required' => 'Visit duration is required when visit scheduling is enabled.',
                'visit_duration.in' => 'Visit duration must be 30, 45, 60, 90, or 120 minutes.',
            ]);

            // Custom validation for room details if provided
            if (!empty($validated['rooms'])) {
                foreach ($validated['rooms'] as $index => $room) {
                    if (empty($room['name']) || empty($room['capacity'])) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors([
                                'rooms' => 'If you provide room details, all room names and capacities are required. Otherwise, use the "Use Default Room Settings" button to skip room details.'
                            ]);
                    }

                    // Validate capacity is a positive integer
                    if (!is_numeric($room['capacity']) || $room['capacity'] < 1 || $room['capacity'] > 10) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors([
                                'rooms' => "Room capacity must be between 1 and 10 people for room '{$room['name']}'."
                            ]);
                    }
                }

                // Validate room count matches provided rooms
                if (count($validated['rooms']) > $validated['room_count']) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'rooms' => 'Number of room details provided exceeds the total room count specified.'
                        ]);
                }
            }

            // Wrap all operations in transaction
            DB::transaction(function () use ($validated, $uploadedFiles, $request) {
                // Prepare property data for creation - handle PostgreSQL differences
                $propertyData = [
                    'user_id' => auth()->id(),
                    'title' => $validated['title'],
                    'slug' => Property::generateUniqueSlug($validated['title']), // Use model method for better error handling
                    'description' => $validated['description'],
                    'house_rules' => !empty($validated['house_rules']) ? array_values(array_filter($validated['house_rules'])) : null,
                    'location_text' => $validated['location_text'],
                    'address_line' => $validated['address_line'],
                    'barangay' => $validated['barangay'],
                    'city' => $validated['city'],
                    'province' => 'Pampanga',
                    'latitude' => (float) $validated['latitude'], // Explicit type casting for PostgreSQL
                    'longitude' => (float) $validated['longitude'],
                    'price' => (float) $validated['price'],
                    'room_count' => (int) $validated['room_count'],
                    'visit_schedule_enabled' => $request->boolean('visit_schedule_enabled'),
                    'approval_status' => 'pending',
                ];

                // Handle visit scheduling data - JSON format for PostgreSQL
                if ($request->boolean('visit_schedule_enabled')) {
                    $propertyData['visit_days'] = !empty($validated['visit_days']) ?
                        json_encode(array_values($validated['visit_days'])) : null;
                    $propertyData['visit_time_start'] = $validated['visit_time_start'] ?? null;
                    $propertyData['visit_time_end'] = $validated['visit_time_end'] ?? null;
                    $propertyData['visit_duration'] = !empty($validated['visit_duration']) ?
                        (int) $validated['visit_duration'] : null;
                    $propertyData['visit_instructions'] = $validated['visit_instructions'] ?? null;
                } else {
                    $propertyData['visit_days'] = null;
                    $propertyData['visit_time_start'] = null;
                    $propertyData['visit_time_end'] = null;
                    $propertyData['visit_duration'] = null;
                    $propertyData['visit_instructions'] = null;
                }

                // Create the property
                $property = Property::create($propertyData);

                // Handle property images
                foreach ($uploadedFiles as $index => $fileData) {
                    if (config('app.env') === 'production') {
                        // Production: Images already in Cloudinary
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_path' => $fileData['cloudinary_url'],
                            'cloudinary_public_id' => $fileData['cloudinary_public_id'],
                            'alt_text' => $validated['title'] . ' - Image ' . ($index + 1),
                            'is_cover' => $index === 0,
                            'sort_order' => $index
                        ]);
                    } else {
                        // Local: Move from temp to permanent
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
                }

                // Create rooms for the property
                if (!empty($validated['rooms'])) {
                    // Create rooms from user input
                    foreach ($validated['rooms'] as $index => $roomData) {
                        // Prepare room data with enhanced details if available - PostgreSQL compatible
                        $roomCreateData = [
                            'property_id' => $property->id,
                            'room_number' => trim($roomData['name']),
                            'room_type' => 'shared', // Default room type
                            'price' => (float) $validated['price'], // Explicit float casting for PostgreSQL
                            'description' => !empty($roomData['description']) ? trim($roomData['description']) : null,
                            'capacity' => (int) $roomData['capacity'], // Ensure integer conversion
                            'status' => 'available',
                        ];

                        // Add enhanced room details if provided
                        $roomDetails = $validated['room_details'][$index] ?? null;
                        if ($roomDetails) {
                            // Override description with detailed description if provided
                            if (!empty($roomDetails['description'])) {
                                $roomCreateData['description'] = trim($roomDetails['description']);
                            }

                            // Override price with room-specific price if provided
                            if (!empty($roomDetails['price'])) {
                                $roomCreateData['price'] = (float) $roomDetails['price'];
                            }

                            // Add all enhanced fields
                            $enhancedFields = [
                                'size_sqm', 'furnished_status', 'bathroom_type', 'flooring_type',
                                'ac_type', 'internet_speed_mbps', 'storage_space', 'security_deposit',
                                'advance_payment_months', 'minimum_stay_months', 'house_rules'
                            ];

                            foreach ($enhancedFields as $field) {
                                if (isset($roomDetails[$field]) && $roomDetails[$field] !== '') {
                                    $roomCreateData[$field] = $roomDetails[$field];
                                }
                            }

                            // Handle boolean fields
                            $booleanFields = [
                                'has_kitchenette', 'has_refrigerator', 'has_study_desk',
                                'has_balcony', 'pets_allowed', 'smoking_allowed'
                            ];

                            foreach ($booleanFields as $field) {
                                if (isset($roomDetails[$field])) {
                                    $roomCreateData[$field] = (bool) $roomDetails[$field];
                                }
                            }

                            // Handle included utilities JSON
                            if (isset($roomDetails['included_utilities']) && $roomDetails['included_utilities'] !== '') {
                                $utilities = json_decode($roomDetails['included_utilities'], true);
                                $roomCreateData['included_utilities'] = $utilities ?: null;
                            }
                        }

                        $room = Room::create($roomCreateData);

                        // Handle room images if provided
                        if ($request->hasFile("room_images.{$index}")) {
                            // Initialize Cloudinary
                            $cloudinary = new Cloudinary([
                                'cloud' => [
                                    'cloud_name' => config('cloudinary.cloud_name'),
                                    'api_key' => config('cloudinary.api_key'),
                                    'api_secret' => config('cloudinary.api_secret'),
                                ]
                            ]);

                            $roomImages = $request->file("room_images.{$index}");
                            foreach ($roomImages as $imageIndex => $roomImage) {
                                try {
                                    // Upload to Cloudinary
                                    $uploadResult = $cloudinary->uploadApi()->upload(
                                        $roomImage->getRealPath(),
                                        [
                                            'folder' => config('cloudinary.folders.rooms'),
                                            'public_id' => 'room_' . $room->id . '_' . time() . '_' . $imageIndex,
                                            'resource_type' => 'image',
                                            'transformation' => [
                                                'quality' => 'auto',
                                                'fetch_format' => 'auto'
                                            ]
                                        ]
                                    );

                                    $cloudinaryUrl = $uploadResult['secure_url'];
                                    $publicId = $uploadResult['public_id'];

                                    RoomImage::create([
                                        'room_id' => $room->id,
                                        'image_path' => $cloudinaryUrl,
                                        'cloudinary_public_id' => $publicId,
                                        'alt_text' => trim($roomData['name']) . ' - Image ' . ($imageIndex + 1),
                                        'is_cover' => $imageIndex === 0, // First image is cover
                                        'sort_order' => $imageIndex,
                                    ]);
                                } catch (\Exception $e) {
                                    Log::error('Cloudinary room image upload failed: ' . $e->getMessage());
                                    continue;
                                }
                            }
                        }
                    }

                    // Create additional default rooms if user provided fewer rooms than room_count
                    $providedRoomsCount = count($validated['rooms']);
                    if ($providedRoomsCount < $validated['room_count']) {
                        for ($i = $providedRoomsCount + 1; $i <= $validated['room_count']; $i++) {
                            Room::create([
                                'property_id' => $property->id,
                                'room_number' => 'Room ' . $i,
                                'room_type' => 'shared',
                                'price' => (float) $validated['price'], // PostgreSQL compatible
                                'description' => null,
                                'capacity' => 2, // Default capacity for additional rooms
                                'status' => 'available',
                            ]);
                        }
                    }
                } else {
                    // Create default rooms based on room_count (simple mode)
                    for ($i = 1; $i <= $validated['room_count']; $i++) {
                        Room::create([
                            'property_id' => $property->id,
                            'room_number' => 'Room ' . $i,
                            'room_type' => 'shared', // Default room type
                            'price' => (float) $validated['price'], // PostgreSQL compatible
                            'description' => null,
                            'capacity' => 2, // Default capacity
                            'status' => 'available',
                        ]);
                    }
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

            // Enhanced logging for debugging PostgreSQL vs MySQL differences
            Log::error('Property creation failed - Production Error', [
                'user_id' => auth()->id(),
                'environment' => app()->environment(),
                'database_driver' => DB::connection()->getDriverName(),
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'form_data' => [
                    'title' => $request->input('title'),
                    'city' => $request->input('city'),
                    'price' => $request->input('price'),
                    'room_count' => $request->input('room_count'),
                    'visit_schedule_enabled' => $request->boolean('visit_schedule_enabled'),
                    'visit_days' => $request->input('visit_days'),
                    'has_images' => $request->hasFile('images'),
                    'has_rooms' => !empty($request->input('rooms')),
                ],
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Provide detailed error messages based on the actual error
            $errorMessage = 'Failed to create property. ';

            if (str_contains($e->getMessage(), 'SQLSTATE') || str_contains($e->getMessage(), 'constraint')) {
                $errorMessage .= 'Database validation error. This may be due to duplicate data or invalid field values. Please check your input and try again.';
            } elseif (str_contains($e->getMessage(), 'JSON') || str_contains($e->getMessage(), 'json')) {
                $errorMessage .= 'Data format error occurred. Please try again or contact support if the issue persists.';
            } elseif (str_contains($e->getMessage(), 'Storage') || str_contains($e->getMessage(), 'file')) {
                $errorMessage .= 'File upload error. Please check your images (max 5MB each) and try again.';
            } elseif (str_contains($e->getMessage(), 'slug') || str_contains($e->getMessage(), 'unique')) {
                $errorMessage .= 'A property with a similar name already exists. Please use a different title and try again.';
            } elseif (str_contains($e->getMessage(), 'rooms') || str_contains($e->getMessage(), 'room_details')) {
                $errorMessage .= 'Room configuration error. Please check your room details or use default settings.';
            } else {
                $errorMessage .= 'An unexpected error occurred. Our team has been notified. Please try again in a few minutes.';
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => $errorMessage]);
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
            'house_rules' => 'nullable|array',
            'house_rules.*' => 'nullable|string|max:500',
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
                    'house_rules' => !empty($validated['house_rules']) ? array_values(array_filter($validated['house_rules'])) : null,
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

                    // Check if Cloudinary is configured (by checking if cloud_name exists)
                    $useCloudinary = !empty(config('cloudinary.cloud_name'));

                    if ($useCloudinary) {
                        // Upload to Cloudinary
                        $cloudinary = new Cloudinary([
                            'cloud' => [
                                'cloud_name' => config('cloudinary.cloud_name'),
                                'api_key' => config('cloudinary.api_key'),
                                'api_secret' => config('cloudinary.api_secret'),
                            ]
                        ]);

                        foreach ($request->file('images') as $index => $image) {
                            try {
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

                                PropertyImage::create([
                                    'property_id' => $property->id,
                                    'image_path' => $uploadResult['secure_url'],
                                    'cloudinary_public_id' => $uploadResult['public_id'],
                                    'alt_text' => $property->title . ' - Image ' . ($currentImageCount + $index + 1),
                                    'is_cover' => $currentImageCount === 0 && $index === 0,
                                    'sort_order' => $currentImageCount + $index
                                ]);

                                Log::info('Cloudinary upload successful in property update', [
                                    'property_id' => $property->id,
                                    'image_url' => $uploadResult['secure_url']
                                ]);
                            } catch (\Exception $e) {
                                Log::error('Cloudinary upload failed during property update: ' . $e->getMessage());
                            }
                        }
                    } else {
                        // Local: Upload to local storage
                        foreach ($request->file('images') as $index => $image) {
                            $path = $image->store('properties', 'public');

                            PropertyImage::create([
                                'property_id' => $property->id,
                                'image_path' => $path,
                                'alt_text' => $property->title . ' - Image ' . ($currentImageCount + $index + 1),
                                'is_cover' => $currentImageCount === 0 && $index === 0,
                                'sort_order' => $currentImageCount + $index
                            ]);
                        }
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

    /**
     * Request property deletion with admin approval
     */
    public function requestDeletion(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this feature');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'reason' => 'required|string|max:1000'
        ]);

        // Verify landlord owns this property
        $property = Property::findOrFail($validated['property_id']);
        if ($property->user_id !== auth()->id()) {
            abort(403, 'You can only request deletion of your own properties');
        }

        try {
            // Check if there's already a pending deletion request for this property
            $existingRequest = PropertyDeletionRequest::where('property_id', $property->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return back()->withErrors(['general' => 'There is already a pending deletion request for this property. Please wait for admin review.']);
            }

            // Create the deletion request
            $deletionRequest = PropertyDeletionRequest::create([
                'property_id' => $property->id,
                'landlord_id' => auth()->id(),
                'reason' => $validated['reason'],
                'status' => 'pending'
            ]);

            // Log for audit trail
            Log::info('Property deletion request created', [
                'deletion_request_id' => $deletionRequest->id,
                'property_id' => $property->id,
                'property_title' => $property->title,
                'landlord_id' => auth()->id(),
                'landlord_name' => auth()->user()->name,
                'reason' => $validated['reason'],
                'requested_at' => now()
            ]);

            return redirect()->route('landlord.properties.index')
                ->with('success', '🗑️ Deletion Request Submitted Successfully! Your request to delete "' . $property->title . '" has been sent to the admin team. They will review your request within 24-48 hours and you\'ll be notified of their decision. You can check the status by looking at your property listing - it will show "Deletion Pending" while under review.');

        } catch (\Exception $e) {
            Log::error('Property deletion request failed', [
                'property_id' => $property->id,
                'landlord_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Failed to submit deletion request. Please try again.']);
        }
    }

    /**
     * Contact admin regarding property issues
     */
    public function contactAdmin(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this feature');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'subject' => 'nullable|string|max:200',
            'regarding_property_id' => 'nullable|exists:properties,id'
        ]);

        // If property ID provided, verify ownership
        if ($validated['regarding_property_id']) {
            $property = Property::findOrFail($validated['regarding_property_id']);
            if ($property->user_id !== auth()->id()) {
                abort(403, 'You can only contact admin about your own properties');
            }
        }

        try {
            // Create admin message record
            $adminMessage = AdminMessage::create([
                'sender_id' => auth()->id(),
                'subject' => $validated['subject'] ?? 'Property Deletion Inquiry',
                'message' => $validated['message'],
                'property_id' => $validated['regarding_property_id'],
                'status' => 'unread'
            ]);

            // Log for audit trail
            Log::info('Admin message created from landlord', [
                'message_id' => $adminMessage->id,
                'landlord_id' => auth()->id(),
                'landlord_name' => auth()->user()->name,
                'subject' => $adminMessage->subject,
                'property_id' => $validated['regarding_property_id'],
                'property_title' => $validated['regarding_property_id'] ? Property::find($validated['regarding_property_id'])->title : null,
                'submitted_at' => now()
            ]);

            return redirect()->route('landlord.properties.index')
                ->with('success', '📧 Message Sent Successfully! Your message has been delivered to the admin team. They typically respond within 24-48 hours during business days. You can expect to receive their reply via email or through your account notifications. Thank you for reaching out!');

        } catch (\Exception $e) {
            Log::error('Admin contact request failed', [
                'landlord_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Failed to send message to admin. Please try again.']);
        }
    }
}