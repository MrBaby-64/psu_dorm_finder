<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get all conversations for this user
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver', 'property', 'inquiry'])
        ->latest()
        ->get()
        ->groupBy(function($message) use ($user) {
            // Group by the other person in the conversation
            return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
        });

        return view('messages.index', compact('messages'));
    }

    public function conversation(Request $request, $userId, $propertyId)
    {
        $user = auth()->user();
        $otherUser = \App\Models\User::findOrFail($userId);
        $property = Property::findOrFail($propertyId);

        // Get conversation messages between these users for this property
        $messages = Message::where('property_id', $propertyId)
            ->where(function($query) use ($user, $userId) {
                $query->where(function($q) use ($user, $userId) {
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $userId);
                })->orWhere(function($q) use ($user, $userId) {
                    $q->where('sender_id', $userId)
                      ->where('receiver_id', $user->id);
                });
            })
            ->with(['sender', 'receiver', 'inquiry'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('receiver_id', $user->id)
            ->where('sender_id', $userId)
            ->where('property_id', $propertyId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.conversation', compact('messages', 'otherUser', 'property'));
    }

    public function sendDirectMessage(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'message' => 'required|string|max:1000',
        ]);

        try {
            $property = Property::findOrFail($validated['property_id']);

            // Create direct message to landlord
            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $property->user_id, // Landlord
                'property_id' => $validated['property_id'],
                'body' => $validated['message']
            ]);

            // Create notification for landlord
            Notification::create([
                'user_id' => $property->user_id,
                'type' => Notification::TYPE_MESSAGE_RECEIVED,
                'title' => 'New Message',
                'message' => auth()->user()->name . ' sent you a message about "' . $property->title . '".',
                'data' => [
                    'message_id' => $message->id,
                    'property_id' => $property->id,
                    'sender_name' => auth()->user()->name,
                    'sender_id' => auth()->id()
                ],
                'action_url' => route('messages.conversation', [auth()->id(), $property->id])
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Direct message failed: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message. Please try again.'
                ], 422);
            }

            return back()->withInput()->withErrors(['general' => 'Failed to send message. Please try again.']);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'receiver_id' => 'nullable|exists:users,id',
            'body' => 'required|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        // Determine receiver - use receiver_id if provided, otherwise use property owner
        $receiverId = $validated['receiver_id'] ?? $property->user_id;

        Log::info('Message creation attempt', [
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverId,
            'property_id' => $property->id,
            'validated_keys' => array_keys($validated)
        ]);

        try {
            $message = DB::transaction(function () use ($validated, $property, $receiverId) {
                return Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $receiverId,
                    'property_id' => $property->id,
                    'body' => $validated['body'],
                ]);
            });

            // Create notification for receiver about new message
            Notification::create([
                'user_id' => $receiverId,
                'type' => Notification::TYPE_MESSAGE_RECEIVED,
                'title' => 'New Message',
                'message' => auth()->user()->name . ' sent you a message about "' . $property->title . '".',
                'data' => [
                    'message_id' => $message->id,
                    'property_id' => $property->id,
                    'sender_name' => auth()->user()->name,
                    'sender_id' => auth()->id(),
                    'message_preview' => substr($validated['body'], 0, 100) . (strlen($validated['body']) > 100 ? '...' : '')
                ],
                'action_url' => route('messages.conversation', [
                    'userId' => auth()->id(),
                    'propertyId' => $property->id
                ])
            ]);

            Log::info('Message created successfully', [
                'message_id' => $message->id,
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'property_id' => $property->id
            ]);

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Message creation failed', [
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            return back()->withInput()->withErrors(['general' => 'Failed to send message. Please try again.']);
        }
    }

    public function markAsRead(Message $message)
    {
        // Only receiver can mark as read
        if ($message->receiver_id === auth()->id()) {
            $message->update(['read_at' => now()]);
        }

        return redirect()->back();
    }
}