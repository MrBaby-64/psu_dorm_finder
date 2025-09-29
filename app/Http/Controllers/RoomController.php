<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Get room data for editing
     */
    public function getData(Room $room)
    {
        // Check if user owns this room's property
        if (Auth::user()->id !== $room->property->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($room);
    }

    /**
     * Update room details
     */
    public function update(Request $request, Room $room)
    {
        // Check if user owns this room's property
        if (Auth::user()->id !== $room->property->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the request
        $validatedData = $request->validate([
            'room_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1|max:20',
            'price' => 'required|numeric|min:0',
            'size_sqm' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'furnished_status' => 'nullable|string|in:furnished,semi_furnished,unfurnished',
            'bathroom_type' => 'nullable|string|in:private,shared,communal',
            'ac_type' => 'nullable|string|in:central,split,window,ceiling_fan,none',
            'internet_speed_mbps' => 'nullable|integer|min:0',
            'storage_space' => 'nullable|string|in:closet,wardrobe,built_in,none',
            'flooring_type' => 'nullable|string|in:tile,wood,concrete,carpet,vinyl',
            'advance_payment_months' => 'nullable|integer|min:1|max:12',
            'security_deposit' => 'nullable|numeric|min:0',
            'minimum_stay_months' => 'nullable|integer|min:1|max:24',
            'house_rules' => 'nullable|string|max:1000',
            'has_kitchenette' => 'nullable|boolean',
            'has_refrigerator' => 'nullable|boolean',
            'has_study_desk' => 'nullable|boolean',
            'has_balcony' => 'nullable|boolean',
            'pets_allowed' => 'nullable|boolean',
            'smoking_allowed' => 'nullable|boolean',
            'included_utilities' => 'nullable|string', // Will be JSON string
        ]);

        // Handle included utilities JSON
        if (isset($validatedData['included_utilities'])) {
            $utilities = json_decode($validatedData['included_utilities'], true);
            $validatedData['included_utilities'] = $utilities ?: null;
        }

        // Convert empty strings to null for nullable fields
        foreach (['furnished_status', 'bathroom_type', 'ac_type', 'storage_space', 'flooring_type', 'house_rules'] as $field) {
            if (isset($validatedData[$field]) && $validatedData[$field] === '') {
                $validatedData[$field] = null;
            }
        }

        // Update the room
        $room->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Room details updated successfully!'
        ]);
    }
}
