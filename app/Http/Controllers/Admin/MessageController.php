<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * MessageController
 *
 * Handles admin message operations including viewing landlord inquiries,
 * responding to messages, and managing message status (read/unread/resolved).
 */
class MessageController extends Controller
{
    /**
     * Verify user has admin role before proceeding
     */
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    /**
     * Display paginated list of messages sent to admin from landlords
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        try {
            // Load messages with sender information for display
            $query = AdminMessage::with('sender');

            // Apply status filter
            if ($request->filled('status') && in_array($request->status, ['unread', 'read', 'resolved'])) {
                $query->where('status', $request->status);
            }

            $messages = $query->orderBy('created_at', 'desc')->paginate(15);

            $statuses = [
                'unread' => 'Unread',
                'read' => 'Read',
                'resolved' => 'Resolved',
            ];

            return view('admin.messages.index', ['messages' => $messages, 'statuses' => $statuses]);

        } catch (\Exception $e) {
            Log::error('Admin messages index error: ' . $e->getMessage());

            $messages = AdminMessage::where('id', 0)->paginate(15);

            $statuses = [
                'unread' => 'Unread',
                'read' => 'Read',
                'resolved' => 'Resolved',
            ];

            return view('admin.messages.index', ['messages' => $messages, 'statuses' => $statuses])
                ->with('error', 'Unable to load messages.');
        }
    }

    public function show(AdminMessage $message)
    {
        $this->checkAdmin();

        $message->load(['sender', 'property', 'responder']);

        // Mark as read if it's unread
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', compact('message'));
    }

    public function respond(Request $request, AdminMessage $message)
    {
        $this->checkAdmin();

        $request->validate([
            'admin_response' => 'required|string|max:2000'
        ]);

        try {
            // Update the message
            $message->update([
                'status' => 'resolved',
                'admin_response' => $request->admin_response,
                'responded_by' => auth()->id(),
                'responded_at' => now()
            ]);

            // Create notification for the landlord
            $notification = Notification::create([
                'user_id' => $message->sender_id,
                'type' => Notification::TYPE_ADMIN_RESPONSE,
                'title' => 'Admin Response Received',
                'message' => 'The admin has responded to your message: "' . $message->subject . '"',
                'data' => [
                    'admin_message_id' => $message->id,
                    'admin_name' => auth()->user()->name,
                    'original_subject' => $message->subject,
                    'response_preview' => substr($request->admin_response, 0, 100) . (strlen($request->admin_response) > 100 ? '...' : ''),
                    'property_id' => $message->property_id,
                    'property_title' => $message->property ? $message->property->title : null
                ],
                'action_url' => null // Will be set below
            ]);

            // Update the action URL with the actual notification ID
            $notification->update([
                'action_url' => route('landlord.admin-response', [
                    'notification' => $notification->id,
                    'message' => $message->id
                ])
            ]);

            Log::info('Admin responded to message', [
                'message_id' => $message->id,
                'admin_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'sender_id' => $message->sender_id,
                'responded_at' => now(),
                'notification_sent' => true
            ]);

            return redirect()->route('admin.messages.index')
                ->with('success', 'Response sent successfully! The landlord has been notified and can view your response in their notifications.');

        } catch (\Exception $e) {
            Log::error('Failed to send admin response', [
                'message_id' => $message->id,
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['general' => 'Failed to send response. Please try again.']);
        }
    }

    public function markAsRead(AdminMessage $message)
    {
        $this->checkAdmin();

        $message->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function markAsResolved(AdminMessage $message)
    {
        $this->checkAdmin();

        $message->update(['status' => 'resolved']);

        return response()->json(['success' => true]);
    }
}
