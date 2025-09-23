<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
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
        ->with(['sender', 'receiver', 'property'])
        ->latest()
        ->get()
        ->groupBy(function($message) use ($user) {
            // Group by the other person in the conversation
            return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
        });

        return view('messages.index', compact('messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'body' => 'required|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        Log::info('Message creation attempt', [
            'sender_id' => auth()->id(),
            'receiver_id' => $property->user_id,
            'property_id' => $property->id,
            'validated_keys' => array_keys($validated)
        ]);

        try {
            $message = DB::transaction(function () use ($validated, $property) {
                return Message::create([
                    'sender_id' => auth()->id(),
                    'receiver_id' => $property->user_id, // landlord
                    'property_id' => $property->id,
                    'body' => $validated['body'],
                ]);
            });

            Log::info('Message created successfully', [
                'message_id' => $message->id,
                'sender_id' => auth()->id(),
                'receiver_id' => $property->user_id,
                'property_id' => $property->id
            ]);

            return redirect()->back()->with('success', 'Message sent successfully!');

        } catch (\Exception $e) {
            Log::error('Message creation failed', [
                'sender_id' => auth()->id(),
                'receiver_id' => $property->user_id,
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