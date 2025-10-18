<?php
// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Notification;
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

            return view('bookings.index', compact('bookings'));
        } else {
            // For tenants, show comprehensive history with both bookings and inquiries
            $bookings = Booking::where('user_id', $user->id)
                ->with(['property', 'room', 'property.user'])
                ->latest()
                ->get();

            $inquiries = \App\Models\Inquiry::where('user_id', $user->id)
                ->with(['property', 'room', 'property.user', 'messages'])
                ->latest()
                ->get();

            // Get scheduled visits
            $scheduledVisits = \App\Models\ScheduledVisit::where('user_id', $user->id)
                ->with(['property', 'property.user'])
                ->latest()
                ->get();

            // Get statistics for dashboard
            $stats = [
                'total_inquiries' => $inquiries->count(),
                'pending_inquiries' => $inquiries->where('status', 'pending')->count(),
                'approved_inquiries' => $inquiries->where('status', 'approved')->count(),
                'total_bookings' => $bookings->count(),
                'pending_bookings' => $bookings->where('status', 'pending')->count(),
                'active_bookings' => $bookings->where('status', 'active')->count(),
                'scheduled_visits' => $scheduledVisits->count(),
                'pending_visits' => $scheduledVisits->where('status', 'pending')->count(),
            ];

            return view('bookings.history', compact('bookings', 'inquiries', 'scheduledVisits', 'stats'));
        }
    }

    public function store(Request $request)
    {
        // Check if user is a tenant
        if (auth()->user()->role !== 'tenant') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only tenants can submit booking requests.'
                ], 403);
            }
            return redirect()->back()->withErrors(['general' => 'Only tenants can submit booking requests.']);
        }

        // Check if tenant already has an active booking (pending, approved, or active)
        if (Booking::tenantHasActiveBooking(auth()->id())) {
            $activeBooking = Booking::getTenantActiveBooking(auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "You already have an active booking for \"{$activeBooking->property->title}\". Please wait for landlord action on your current booking before submitting another one."
                ], 422);
            }

            return redirect()->back()->withErrors([
                'booking_restriction' => "You already have an active booking for \"{$activeBooking->property->title}\". Please wait for landlord action on your current booking before submitting another one."
            ])->with('active_booking', $activeBooking);
        }

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

            // Create notification for landlord about new booking
            Notification::create([
                'user_id' => $property->user_id, // Landlord
                'type' => Notification::TYPE_BOOKING_RECEIVED,
                'title' => 'New Booking Request',
                'message' => auth()->user()->name . ' has requested to book your property "' . $property->title . '".',
                'data' => [
                    'booking_id' => $booking->id,
                    'property_id' => $property->id,
                    'tenant_name' => auth()->user()->name,
                    'tenant_id' => auth()->id(),
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out']
                ],
                'action_url' => route('bookings.index')
            ]);

            // Create notification for tenant (confirmation)
            Notification::create([
                'user_id' => auth()->id(), // Tenant
                'type' => Notification::TYPE_BOOKING_RECEIVED,
                'title' => 'Booking Request Sent',
                'message' => 'Your booking request for "' . $property->title . '" has been sent to the landlord. You will be notified when they respond.',
                'data' => [
                    'booking_id' => $booking->id,
                    'property_id' => $property->id,
                    'landlord_name' => $property->user->name,
                    'landlord_id' => $property->user_id,
                    'check_in' => $validated['check_in'],
                    'check_out' => $validated['check_out']
                ],
                'action_url' => route('bookings.index')
            ]);

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'property_id' => $property->id
            ]);

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking request sent! The landlord will review it soon.',
                    'booking_id' => $booking->id
                ]);
            }

            return redirect()->back()->with('success', 'Booking request sent! The landlord will review it soon.');

        } catch (\Exception $e) {
            Log::error('Booking creation failed', [
                'user_id' => auth()->id(),
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create booking. Please try again.'
                ], 422);
            }

            return back()->withInput()->withErrors(['general' => 'Failed to create booking. Please try again.']);
        }
    }

    public function approve(Booking $booking)
    {
        // Only property owner can approve
        $this->authorize('update', $booking);

        $booking->update(['status' => 'approved']);

        // Create notification for tenant about approval
        Notification::create([
            'user_id' => $booking->user_id, // Tenant
            'type' => Notification::TYPE_BOOKING_APPROVED,
            'title' => 'Booking Approved!',
            'message' => 'Your booking for "' . $booking->property->title . '" has been approved by the landlord.',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'landlord_name' => auth()->user()->name,
                'landlord_id' => auth()->id(),
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out
            ],
            'action_url' => route('bookings.index')
        ]);

        // Create notification for landlord (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Landlord
            'type' => Notification::TYPE_BOOKING_APPROVED,
            'title' => 'Booking Approved',
            'message' => 'You approved the booking from ' . $booking->tenant->name . ' for "' . $booking->property->title . '".',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'tenant_name' => $booking->tenant->name,
                'tenant_id' => $booking->user_id
            ],
            'action_url' => route('bookings.index')
        ]);

        return redirect()->back()->with('success', 'Booking approved!');
    }

    public function reject(Booking $booking)
    {
        // Only property owner can reject
        $this->authorize('update', $booking);

        $booking->update(['status' => 'rejected']);

        // Create notification for tenant about rejection
        Notification::create([
            'user_id' => $booking->user_id, // Tenant
            'type' => Notification::TYPE_BOOKING_REJECTED,
            'title' => 'Booking Declined',
            'message' => 'Your booking for "' . $booking->property->title . '" has been declined by the landlord.',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'landlord_name' => auth()->user()->name,
                'landlord_id' => auth()->id(),
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out
            ],
            'action_url' => route('properties.browse')
        ]);

        // Create notification for landlord (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Landlord
            'type' => Notification::TYPE_BOOKING_REJECTED,
            'title' => 'Booking Declined',
            'message' => 'You declined the booking from ' . $booking->tenant->name . ' for "' . $booking->property->title . '".',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'tenant_name' => $booking->tenant->name,
                'tenant_id' => $booking->user_id
            ],
            'action_url' => route('bookings.index')
        ]);

        return redirect()->back()->with('success', 'Booking rejected.');
    }

    public function cancel(Booking $booking)
    {
        // Only tenant can cancel their own booking
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        // Create notification for landlord about cancellation
        Notification::create([
            'user_id' => $booking->property->user_id, // Landlord
            'type' => Notification::TYPE_BOOKING_CANCELLED,
            'title' => 'Booking Cancelled',
            'message' => auth()->user()->name . ' has cancelled their booking for "' . $booking->property->title . '".',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'tenant_name' => auth()->user()->name,
                'tenant_id' => auth()->id(),
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out
            ],
            'action_url' => route('bookings.index')
        ]);

        // Create notification for tenant (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Tenant
            'type' => Notification::TYPE_BOOKING_CANCELLED,
            'title' => 'Booking Cancelled',
            'message' => 'You have cancelled your booking for "' . $booking->property->title . '".',
            'data' => [
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'landlord_name' => $booking->property->user->name,
                'landlord_id' => $booking->property->user_id
            ],
            'action_url' => route('properties.browse')
        ]);

        return redirect()->back()->with('success', 'Booking cancelled.');
    }
}