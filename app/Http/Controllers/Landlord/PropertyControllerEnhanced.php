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
            // Use simple direct DB query for PostgreSQL compatibility
            $userId = Auth::id();

            // Simple query without complex relationships
            $properties = DB::table('properties')
                ->where('user_id', $userId)
                ->select([
                    'id',
                    'title',
                    'approval_status',
                    'price',
                    'room_count',
                    'created_at',
                    'updated_at'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('landlord.properties.index', compact('properties'));

        } catch (\Exception $e) {
            Log::error('Landlord properties index error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty collection on error
            $properties = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => request()->url()]
            );

            return view('landlord.properties.index', [
                'properties' => $properties,
                'error' => 'Unable to load properties. Please try again.'
            ]);
        }
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        try {
            // Load amenities with simple query for PostgreSQL
            $amenities = DB::table('amenities')
                ->orderBy('name')
                ->get();

            // Get temp images from session if any
            $formToken = session('property_form_token') ?: uniqid();
            session(['property_form_token' => $formToken]);

            $tempImages = session("temp_images_{$formToken}", []);

            return view('landlord.properties.create', compact('amenities', 'tempImages'));

        } catch (\Exception $e) {
            Log::error('Property create form error: ' . $e->getMessage());

            // Return basic view with empty amenities on error
            $amenities = collect([]);
            $tempImages = [];

            return view('landlord.properties.create', compact('amenities', 'tempImages'));
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        try {
            // Basic validation
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'room_count' => 'required|integer|min:1',
                'location_text' => 'required|string|max:255'
            ]);

            // Simple property creation with PostgreSQL-safe types
            $slug = \Illuminate\Support\Str::slug($validated['title'] . '-' . time());

            $propertyData = [
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location_text' => $validated['location_text'],
                'price' => (float) $validated['price'],
                'room_count' => (int) $validated['room_count'],
                'approval_status' => 'pending',
                'slug' => $slug,
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Insert directly via DB for PostgreSQL compatibility
            $propertyId = DB::table('properties')->insertGetId($propertyData);

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property created successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            Log::error('Property store error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors([
                'error' => 'Failed to create property. Please try again.'
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