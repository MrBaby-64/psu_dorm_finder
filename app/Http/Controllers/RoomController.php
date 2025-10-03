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
        try {
            // Check if user owns this room's property
            if (Auth::user()->id !== $room->property->user_id) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
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
                'security_deposit' => 'nullable|numeric|min:0',
                'minimum_stay_months' => 'nullable|integer|min:1|max:24',
                'house_rules' => 'nullable|string|max:1000',
                'included_utilities' => 'nullable|string', // Will be JSON string
            ]);

            // Handle advance_payment_months separately (convert empty to null)
            $validatedData['advance_payment_months'] = $request->input('advance_payment_months');
            if ($validatedData['advance_payment_months'] === '' || $validatedData['advance_payment_months'] === null) {
                $validatedData['advance_payment_months'] = null;
            } else {
                $validatedData['advance_payment_months'] = (int) $validatedData['advance_payment_months'];
            }

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

            // Handle checkboxes (get from request, not validated data, because unchecked boxes don't send data)
            $checkboxFields = ['has_kitchenette', 'has_refrigerator', 'has_study_desk', 'has_balcony', 'pets_allowed', 'smoking_allowed'];
            foreach ($checkboxFields as $field) {
                $validatedData[$field] = $request->has($field) ? true : false;
            }

            // Update the room
            $room->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Room details updated successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors()))
            ], 422);
        } catch (\Exception $e) {
            // Log error and return generic message
            \Log::error('Room update error', [
                'error' => $e->getMessage(),
                'room_id' => $room->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating room: ' . $e->getMessage()
            ], 500);
        }
    }
}
