<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Amenity;
use App\Models\Room;
use App\Models\PropertyImage;
use App\Models\PropertyDeletionRequest;
use App\Models\AdminMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PropertyControllerEnhanced extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this area');
        }

        try {
            // Start with basic query - error-proof approach
            $query = Property::where('user_id', auth()->id());

            // Safe relationship loading with table checks
            $relationships = [];

            if (Schema::hasTable('property_images')) {
                $relationships['images'] = function($query) {
                    $query->select('id', 'property_id', 'image_path', 'alt_text', 'is_cover')
                          ->orderBy('sort_order');
                };
            }

            if (Schema::hasTable('property_deletion_requests')) {
                $relationships['deletionRequest'] = function($q) {
                    $q->where('status', 'pending')
                      ->select('id', 'property_id', 'status', 'reason', 'created_at');
                };
            }

            if (!empty($relationships)) {
                $query->with($relationships);
            }

            // Apply filters safely
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('location_text', 'like', "%{$searchTerm}%");
                });
            }

            if ($request->filled('status') && in_array($request->status, ['approved', 'pending', 'rejected'])) {
                $query->where('approval_status', $request->status);
            }

            $query->latest();
            $properties = $query->paginate(10)->withQueryString();

            $statuses = [
                'approved' => 'Approved',
                'pending' => 'Pending Approval',
                'rejected' => 'Rejected',
            ];

            return view('landlord.properties.index-enhanced', compact('properties', 'statuses'));

        } catch (\Exception $e) {
            Log::error('Enhanced Properties Index Error: ' . $e->getMessage());

            // Fallback to simplified version on error
            try {
                $basicProperties = DB::table('properties')
                    ->where('user_id', auth()->id())
                    ->select(['id', 'title', 'approval_status', 'price', 'room_count', 'created_at', 'updated_at'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

                session()->flash('warning', 'Some features may be limited due to database issues.');

                return view('landlord.properties.index-simple', [
                    'properties' => $basicProperties
                ]);
            } catch (\Exception $fallbackError) {
                Log::error('Fallback also failed: ' . $fallbackError->getMessage());
                return response()->view('landlord.properties.error', [
                    'error' => 'Unable to load properties.',
                    'details' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
        }
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        try {
            // Load amenities safely
            $amenities = Schema::hasTable('amenities') ?
                Amenity::where('is_active', true)->orderBy('name')->get() :
                collect([]);

            // Get temp images from session if any
            $formToken = session('property_form_token') ?: uniqid();
            session(['property_form_token' => $formToken]);

            $tempImages = session("temp_images_{$formToken}", []);

            return view('landlord.properties.create-enhanced', compact('amenities', 'tempImages', 'formToken'));

        } catch (\Exception $e) {
            Log::error('Property create form error: ' . $e->getMessage());
            return response()->view('landlord.properties.error', [
                'error' => 'Unable to load property creation form.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        $uploadedFiles = [];

        try {
            // Enhanced validation rules
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
                'amenities' => 'nullable|array',
                'amenities.*' => 'exists:amenities,id',
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
                'visit_schedule_enabled' => 'nullable|boolean',
            ];

            // Add visit scheduling validation if enabled
            if ($request->boolean('visit_schedule_enabled')) {
                $rules['visit_days'] = 'required|array|min:1';
                $rules['visit_days.*'] = 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday';
                $rules['visit_time_start'] = 'required|date_format:H:i';
                $rules['visit_time_end'] = 'required|date_format:H:i|after:visit_time_start';
                $rules['visit_duration'] = 'required|integer|in:30,45,60,90,120';
                $rules['visit_instructions'] = 'nullable|string|max:500';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Create property with error-proof data preparation
            $propertyData = [
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'slug' => Property::generateUniqueSlug($validated['title']),
                'description' => $validated['description'],
                'location_text' => $validated['location_text'],
                'address_line' => $validated['address_line'],
                'barangay' => $validated['barangay'],
                'city' => $validated['city'],
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
                'price' => (float) $validated['price'],
                'room_count' => (int) $validated['room_count'],
                'approval_status' => 'pending',
                'visit_schedule_enabled' => $request->boolean('visit_schedule_enabled'),
            ];

            // Add visit scheduling data if enabled
            if ($request->boolean('visit_schedule_enabled')) {
                $propertyData['visit_days'] = json_encode($validated['visit_days']);
                $propertyData['visit_time_start'] = $validated['visit_time_start'];
                $propertyData['visit_time_end'] = $validated['visit_time_end'];
                $propertyData['visit_duration'] = $validated['visit_duration'];
                $propertyData['visit_instructions'] = $validated['visit_instructions'] ?? null;
            }

            $property = Property::create($propertyData);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $filename = time() . '_' . $index . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('properties/' . $property->id, $filename, 'public');

                    if (Schema::hasTable('property_images')) {
                        PropertyImage::create([
                            'property_id' => $property->id,
                            'image_path' => $path,
                            'alt_text' => $property->title . ' - Image ' . ($index + 1),
                            'is_cover' => $index === 0,
                            'sort_order' => $index
                        ]);
                    }
                }
            }

            // Attach amenities if provided and table exists
            if (!empty($validated['amenities']) && Schema::hasTable('amenity_property')) {
                $property->amenities()->sync($validated['amenities']);
            }

            DB::commit();

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property created successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files
            foreach ($uploadedFiles as $file) {
                if (Storage::disk('public')->exists($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            Log::error('Property store error: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'error' => 'Failed to create property: ' . $e->getMessage()
            ]);
        }
    }

    public function removeTempImage(Request $request)
    {
        try {
            $formToken = session('property_form_token');
            if (!$formToken) {
                return response()->json(['success' => false, 'message' => 'No active session']);
            }

            $tempImages = session("temp_images_{$formToken}", []);
            $imagePath = $request->image_path;

            // Remove from session
            $tempImages = array_filter($tempImages, function($img) use ($imagePath) {
                return $img['path'] !== $imagePath;
            });

            session(["temp_images_{$formToken}" => array_values($tempImages)]);

            // Delete file if exists
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Remove temp image error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to remove image']);
        }
    }

    public function storeMapPosition(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180'
            ]);

            session([
                'map_latitude' => $request->latitude,
                'map_longitude' => $request->longitude
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Store map position error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to store map position']);
        }
    }
}