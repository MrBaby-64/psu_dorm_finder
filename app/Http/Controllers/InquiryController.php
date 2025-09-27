<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Property;
use App\Models\Room;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        // Check if user is a tenant
        if (auth()->user()->role !== 'tenant') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only tenants can submit inquiries.'
                ], 403);
            }
            return redirect()->back()->withErrors(['general' => 'Only tenants can submit inquiries.']);
        }

        // Check if tenant already has a pending inquiry
        if (Inquiry::tenantHasPendingInquiry(auth()->id())) {
            $pendingInquiry = Inquiry::getTenantPendingInquiry(auth()->id());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "You already have a pending inquiry for \"{$pendingInquiry->property->title}\". Please wait for landlord approval before submitting another inquiry."
                ], 422);
            }

            return redirect()->back()->withErrors([
                'inquiry_restriction' => "You already have a pending inquiry for \"{$pendingInquiry->property->title}\". Please wait for landlord approval before submitting another inquiry."
            ])->with('pending_inquiry', $pendingInquiry);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'room_id' => 'nullable|exists:rooms,id',
            'move_in_date' => 'nullable|date',
            'move_out_date' => 'nullable|date|after:move_in_date',
            'message' => 'required|string|max:1000',
        ]);

        // Additional validation for room selection
        if ($validated['room_id']) {
            $room = Room::find($validated['room_id']);

            // Check if room belongs to the property
            if (!$room || $room->property_id != $validated['property_id']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['room_id' => 'Selected room does not belong to this property.']);
            }

            // Check if room has available capacity (handle missing occupied_count gracefully)
            $occupiedCount = $room->occupied_count ?? 0;
            if ($occupiedCount >= $room->capacity) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['room_id' => 'Selected room is fully occupied. Please choose a different room.']);
            }

            // Check if room is available
            if ($room->status !== 'available') {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['room_id' => 'Selected room is not available. Please choose a different room.']);
            }
        }


        try {
            $inquiry = DB::transaction(function () use ($validated) {
                $inquiry = Inquiry::create([
                    'user_id' => auth()->id(),
                    'property_id' => $validated['property_id'],
                    'room_id' => $validated['room_id'] ?? null,
                    'move_in_date' => $validated['move_in_date'] ?? null,
                    'move_out_date' => $validated['move_out_date'] ?? null,
                    'message' => $validated['message'],
                    'status' => 'pending'
                ]);

                // Get property and landlord info
                $property = Property::with('user')->find($validated['property_id']);

                // Create initial message to landlord
                $messageBody = "📝 New inquiry for \"{$property->title}\":\n\n" . $validated['message'] . "\n\n";

                if ($validated['room_id']) {
                    $selectedRoom = Room::find($validated['room_id']);
                    if ($selectedRoom) {
                        $messageBody .= "Selected Room: {$selectedRoom->room_number}\n";
                        if ($selectedRoom->description) {
                            $messageBody .= "Room Description: {$selectedRoom->description}\n";
                        }
                        $occupiedCount = $selectedRoom->occupied_count ?? 0;
                        $availableSpaces = $selectedRoom->capacity - $occupiedCount;
                        $messageBody .= "Room Capacity: {$selectedRoom->capacity} (Available spaces: {$availableSpaces})\n";
                        $messageBody .= "Room Price: {$selectedRoom->formatted_price}\n";
                    }
                }

                $messageBody .= ($validated['move_in_date'] ? "Move-in Date: " . $validated['move_in_date'] . "\n" : "") .
                               ($validated['move_out_date'] ? "Move-out Date: " . $validated['move_out_date'] . "\n" : "") .
                               "\nPlease review and respond to this inquiry.";

                Message::create([
                    'sender_id' => auth()->id(), // Tenant
                    'receiver_id' => $property->user_id, // Landlord
                    'property_id' => $validated['property_id'],
                    'inquiry_id' => $inquiry->id,
                    'body' => $messageBody
                ]);

                // Create notification for landlord about new inquiry
                Notification::create([
                    'user_id' => $property->user_id, // Landlord
                    'type' => Notification::TYPE_INQUIRY_RECEIVED,
                    'title' => 'New Property Inquiry',
                    'message' => auth()->user()->name . ' has sent an inquiry for your property "' . $property->title . '".',
                    'data' => [
                        'inquiry_id' => $inquiry->id,
                        'property_id' => $property->id,
                        'tenant_name' => auth()->user()->name,
                        'tenant_id' => auth()->id(),
                        'move_in_date' => $validated['move_in_date'],
                        'move_out_date' => $validated['move_out_date']
                    ],
                    'action_url' => route('landlord.inquiries.index')
                ]);

                // Create notification for tenant (confirmation)
                Notification::create([
                    'user_id' => auth()->id(), // Tenant
                    'type' => Notification::TYPE_INQUIRY_RECEIVED,
                    'title' => 'Inquiry Sent Successfully',
                    'message' => 'Your inquiry for "' . $property->title . '" has been sent to the landlord. You will be notified when they respond.',
                    'data' => [
                        'inquiry_id' => $inquiry->id,
                        'property_id' => $property->id,
                        'landlord_name' => $property->user->name,
                        'landlord_id' => $property->user_id
                    ],
                    'action_url' => route('properties.show', $property->slug)
                ]);

                return $inquiry;
            });


            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your inquiry has been sent successfully! Please wait for landlord approval. Check your messages for updates.',
                    'inquiry_id' => $inquiry->id
                ]);
            }

            return redirect()->back()->with('success', 'Your inquiry has been sent successfully! Please wait for landlord approval. Check your messages for updates.');

        } catch (\Exception $e) {
            Log::error('Inquiry creation failed: ' . $e->getMessage());

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send inquiry. Please try again.',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()->withInput()->withErrors(['general' => 'Failed to send inquiry. Please try again.']);
        }
    }
}
