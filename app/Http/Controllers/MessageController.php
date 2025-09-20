<?php
// app/Http/Controllers/MessageController.php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Property;
use Illuminate\Http\Request;

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
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'body' => 'required|string|max:1000',
        ]);

        $property = Property::findOrFail($request->property_id);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $property->user_id, // landlord
            'property_id' => $property->id,
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'Message sent successfully!');
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