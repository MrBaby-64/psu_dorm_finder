<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'landlord') {
            // Show bookings for landlord's properties
            $bookings = Booking::whereHas('property', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['property', 'tenant', 'room'])
            ->latest()
            ->paginate(10);
        } else {
            // Show user's own bookings
            $bookings = Booking::where('user_id', $user->id)
                ->with(['property', 'room'])
                ->latest()
                ->paginate(10);
        }

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        Log::info('Booking creation attempt', [
            'user_id' => auth()->id(),
            'property_id' => $property->id,
            'validated_keys' => array_keys($validated)
        ]);

        try {
            $booking = DB::transaction(function () use ($validated, $property) {
                return Booking::create([
                    'property_id' => $property->id,
                    'user_id' => auth()->id(),
                    'room_id' => $validated['room_id'],
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out'],
                    'status' => 'pending',
                ]);
            });

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'property_id' => $property->id
            ]);

            return redirect()->back()->with('success', 'Booking request sent! The landlord will review it soon.');

        } catch (\Exception $e) {
            Log::error('Booking creation failed', [
                'user_id' => auth()->id(),
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            return back()->withInput()->withErrors(['general' => 'Failed to create booking. Please try again.']);
        }
    }

    public function approve(Booking $booking)
    {
        // Only property owner can approve
        $this->authorize('update', $booking);

        $booking->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Booking approved!');
    }

    public function reject(Booking $booking)
    {
        // Only property owner can reject
        $this->authorize('update', $booking);

        $booking->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Booking rejected.');
    }

    public function cancel(Booking $booking)
    {
        // Only tenant can cancel their own booking
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Booking cancelled.');
    }
}