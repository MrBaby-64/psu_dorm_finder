<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Amenity;
use Illuminate\Http\Request;

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

        return view('landlord.properties.index', compact('properties'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        $amenities = Amenity::orderBy('name')->get();
        return view('landlord.properties.create', compact('amenities'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
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
        ]);

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
            'visit_schedule_enabled' => $request->has('visit_schedule_enabled'),
            'approval_status' => 'pending',
        ]);

        if ($request->has('amenities')) {
            $property->amenities()->attach($validated['amenities']);
        }

        return redirect()->route('landlord.properties.index')
            ->with('success', 'Property created successfully!');
    }

    public function edit(Property $property)
    {
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
        ]);

        $property->update([
            'title' => $validated['title'],
            'slug' => \Str::slug($validated['title']), // ADDED THIS LINE - IMPORTANT!
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

        if ($request->has('amenities')) {
            $property->amenities()->sync($validated['amenities']);
        }

        return redirect()->route('landlord.properties.index')
            ->with('success', 'Property updated successfully!');
    }
}