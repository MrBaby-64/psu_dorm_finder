<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'move_in_date' => 'nullable|date',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'message' => 'required|string|max:1000',
        ]);

        Log::info('Inquiry creation attempt', [
            'user_id' => auth()->id(),
            'property_id' => $validated['property_id'],
            'validated_keys' => array_keys($validated)
        ]);

        try {
            $inquiry = DB::transaction(function () use ($validated) {
                return Inquiry::create([
                    'user_id' => auth()->id(),
                    'property_id' => $validated['property_id'],
                    'room_id' => $validated['room_id'] ?? null,
                    'move_in_date' => $validated['move_in_date'] ?? null,
                    'move_out_date' => $validated['move_out_date'] ?? null,
                    'message' => $validated['message'],
                    'status' => 'pending'
                ]);
            });

            Log::info('Inquiry created successfully', [
                'inquiry_id' => $inquiry->id,
                'user_id' => auth()->id(),
                'property_id' => $validated['property_id']
            ]);

            return redirect()->back()->with('success', 'Your inquiry has been sent successfully!');

        } catch (\Exception $e) {
            Log::error('Inquiry creation failed', [
                'user_id' => auth()->id(),
                'property_id' => $validated['property_id'],
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            return back()->withInput()->withErrors(['general' => 'Failed to send inquiry. Please try again.']);
        }
    }
}
