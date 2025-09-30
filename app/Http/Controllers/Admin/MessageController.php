<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    private function checkAdmin()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        try {
            // Use DB query for PostgreSQL compatibility
            $query = \DB::table('admin_messages')
                ->leftJoin('users', 'admin_messages.sender_id', '=', 'users.id')
                ->leftJoin('properties', 'admin_messages.property_id', '=', 'properties.id')
                ->select(
                    'admin_messages.*',
                    'users.name as sender_name',
                    'users.email as sender_email',
                    'properties.title as property_title'
                );

            // Apply status filter
            if ($request->filled('status')) {
                $status = $request->status;
                if (in_array($status, ['unread', 'read', 'resolved'])) {
                    $query->where('admin_messages.status', $status);
                }
            }

            // Apply search filter (PostgreSQL case-insensitive)
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(admin_messages.subject) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(admin_messages.message) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(users.name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                      ->orWhereRaw('LOWER(users.email) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
                });
            }

            $query->orderBy('admin_messages.created_at', 'DESC');
            $messages = $query->paginate(15)->withQueryString();

            $statuses = [
                'unread' => 'Unread',
                'read' => 'Read',
                'resolved' => 'Resolved',
            ];

            return view('admin.messages.index', compact('messages', 'statuses'));

        } catch (\Exception $e) {
            Log::error('Admin messages index error: ' . $e->getMessage());

            $messages = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                15,
                1,
                ['path' => request()->url()]
            );

            $statuses = [
                'unread' => 'Unread',
                'read' => 'Read',
                'resolved' => 'Resolved',
            ];

            return view('admin.messages.index', compact('messages', 'statuses'))
                ->with('error', 'Unable to load messages. Please try again.');
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
