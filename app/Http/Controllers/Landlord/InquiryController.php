<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ScheduledVisit;
use Illuminate\Http\Request;

/**
 * Landlord Inquiry Controller
 * Manages tenant inquiries and visit requests
 */
class InquiryController extends Controller
{
    // List all inquiries for landlord
    public function index()
    {
        $inquiries = Inquiry::whereHas('property', function($query) {
            $query->where('user_id', auth()->id());
        })
        ->with(['user', 'property', 'room'])
        ->latest()
        ->paginate(20);


        // Get pending visits count for the shortcut badge
        $pendingVisitsCount = ScheduledVisit::whereHas('property', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->where('status', 'pending')
            ->count();

        return view('landlord.inquiries.index', compact('inquiries', 'pendingVisitsCount'));
    }

    public function approve(Inquiry $inquiry)
    {
        // Check if landlord owns the property
        if ($inquiry->property->user_id !== auth()->id()) {
            abort(403);
        }

        $inquiry->approve();

        // Create a message notification for the tenant
        Message::create([
            'sender_id' => auth()->id(), // Landlord
            'receiver_id' => $inquiry->user_id, // Tenant
            'property_id' => $inquiry->property_id,
            'inquiry_id' => $inquiry->id,
            'body' => "🎉 Great news! Your inquiry for \"{$inquiry->property->title}\" has been approved. The landlord is interested in your application. Please check your messages for further communication."
        ]);

        // Create notification for tenant about approval
        Notification::create([
            'user_id' => $inquiry->user_id, // Tenant
            'type' => Notification::TYPE_BOOKING_APPROVED,
            'title' => 'Inquiry Approved!',
            'message' => 'Your inquiry for "' . $inquiry->property->title . '" has been approved by the landlord.',
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'landlord_name' => auth()->user()->name,
                'landlord_id' => auth()->id()
            ],
            'action_url' => route('messages.index')
        ]);

        // Create notification for landlord (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Landlord
            'type' => Notification::TYPE_BOOKING_APPROVED,
            'title' => 'Inquiry Approved',
            'message' => 'You approved the inquiry from ' . $inquiry->user->name . ' for "' . $inquiry->property->title . '".',
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'tenant_name' => $inquiry->user->name,
                'tenant_id' => $inquiry->user_id
            ],
            'action_url' => route('landlord.inquiries.index')
        ]);

        return back()->with('success', 'Inquiry approved successfully! Message sent to tenant.');
    }

    public function reject(Request $request, Inquiry $inquiry)
    {
        // Check if landlord owns the property
        if ($inquiry->property->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $inquiry->reject($request->reason);

        // Create a message notification for the tenant
        $messageBody = "❌ Your inquiry for \"{$inquiry->property->title}\" has been declined.";
        if ($request->reason) {
            $messageBody .= "\n\nReason: " . $request->reason;
        }
        $messageBody .= "\n\nYou can now submit new inquiries for other properties.";

        Message::create([
            'sender_id' => auth()->id(), // Landlord
            'receiver_id' => $inquiry->user_id, // Tenant
            'property_id' => $inquiry->property_id,
            'inquiry_id' => $inquiry->id,
            'body' => $messageBody
        ]);

        // Create notification for tenant about rejection
        Notification::create([
            'user_id' => $inquiry->user_id, // Tenant
            'type' => Notification::TYPE_BOOKING_REJECTED,
            'title' => 'Inquiry Declined',
            'message' => 'Your inquiry for "' . $inquiry->property->title . '" has been declined by the landlord.' .
                        ($request->reason ? ' Reason: ' . $request->reason : ''),
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'landlord_name' => auth()->user()->name,
                'landlord_id' => auth()->id(),
                'reason' => $request->reason
            ],
            'action_url' => route('properties.browse')
        ]);

        // Create notification for landlord (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Landlord
            'type' => Notification::TYPE_BOOKING_REJECTED,
            'title' => 'Inquiry Declined',
            'message' => 'You declined the inquiry from ' . $inquiry->user->name . ' for "' . $inquiry->property->title . '".',
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'tenant_name' => $inquiry->user->name,
                'tenant_id' => $inquiry->user_id,
                'reason' => $request->reason
            ],
            'action_url' => route('landlord.inquiries.index')
        ]);

        return back()->with('success', 'Inquiry rejected. Message sent to tenant.');
    }

    public function reply(Request $request, Inquiry $inquiry)
    {
        // Check if landlord owns the property
        if ($inquiry->property->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'reply' => 'required|string|max:1000'
        ]);

        if (!$inquiry->canBeReplied()) {
            return back()->withErrors(['reply' => 'This inquiry cannot be replied to.']);
        }

        $inquiry->reply($request->reply);

        // Create a message for the tenant
        Message::create([
            'sender_id' => auth()->id(), // Landlord
            'receiver_id' => $inquiry->user_id, // Tenant
            'property_id' => $inquiry->property_id,
            'inquiry_id' => $inquiry->id,
            'body' => "💬 Reply about \"{$inquiry->property->title}\":\n\n" . $request->reply
        ]);

        // Create notification for tenant about reply
        Notification::create([
            'user_id' => $inquiry->user_id, // Tenant
            'type' => Notification::TYPE_INQUIRY_REPLIED,
            'title' => 'Landlord Replied',
            'message' => 'The landlord replied to your inquiry for "' . $inquiry->property->title . '". Check your messages for details.',
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'landlord_name' => auth()->user()->name,
                'landlord_id' => auth()->id(),
                'reply_preview' => substr($request->reply, 0, 100) . (strlen($request->reply) > 100 ? '...' : '')
            ],
            'action_url' => route('messages.index')
        ]);

        // Create notification for landlord (confirmation)
        Notification::create([
            'user_id' => auth()->id(), // Landlord
            'type' => Notification::TYPE_INQUIRY_REPLIED,
            'title' => 'Reply Sent',
            'message' => 'You replied to ' . $inquiry->user->name . '\'s inquiry for "' . $inquiry->property->title . '".',
            'data' => [
                'inquiry_id' => $inquiry->id,
                'property_id' => $inquiry->property_id,
                'tenant_name' => $inquiry->user->name,
                'tenant_id' => $inquiry->user_id,
                'reply_preview' => substr($request->reply, 0, 100) . (strlen($request->reply) > 100 ? '...' : '')
            ],
            'action_url' => route('messages.index')
        ]);

        // Redirect to messages page instead of back
        return redirect()->route('messages.index')->with('success', 'Reply sent successfully! Continue the conversation in messages.');
    }
}