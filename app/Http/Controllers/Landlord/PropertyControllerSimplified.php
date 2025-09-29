<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyControllerSimplified extends Controller
{
    public function index()
    {
        try {
            // Get current user properties with minimal queries
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
                ->get();

            return view('landlord.properties.index-simple', compact('properties'));

        } catch (\Exception $e) {
            Log::error('Landlord properties index error: ' . $e->getMessage());

            return response()->view('landlord.properties.error', [
                'error' => 'Unable to load properties. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function create()
    {
        try {
            // Just show the basic creation form
            return view('landlord.properties.create-simple');

        } catch (\Exception $e) {
            Log::error('Property create form error: ' . $e->getMessage());

            return response()->view('landlord.properties.error', [
                'error' => 'Unable to load create form. Please try again.',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Basic validation
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'room_count' => 'required|integer|min:1',
                'location_text' => 'required|string|max:255'
            ]);

            // Simple property creation
            $propertyData = [
                'user_id' => Auth::id(),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'location_text' => $request->input('location_text'),
                'price' => (float) $request->input('price'),
                'room_count' => (int) $request->input('room_count'),
                'approval_status' => 'pending',
                'slug' => \Illuminate\Support\Str::slug($request->input('title') . '-' . time()),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Insert directly via DB
            $propertyId = DB::table('properties')->insertGetId($propertyData);

            return redirect()->route('landlord.properties.index')
                ->with('success', 'Property created successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            Log::error('Property store error: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'error' => 'Failed to create property. Please try again.'
            ]);
        }
    }
}