<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

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

        $inquiry = Inquiry::create([
            'user_id' => auth()->id(),
            'property_id' => $validated['property_id'],
            'room_id' => $validated['room_id'] ?? null,
            'move_in_date' => $validated['move_in_date'] ?? null,
            'move_out_date' => $validated['move_out_date'] ?? null,
            'message' => $validated['message'],
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Your inquiry has been sent successfully!');
    }
}