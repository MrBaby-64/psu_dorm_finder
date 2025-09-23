<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\ScheduledVisit;
use App\Models\Review;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'landlord') {
                abort(403, 'Only landlords can access this area');
            }
            return $next($request);
        });
    }

    // Account Dashboard
    public function account()
    {
        $user = Auth::user();
        
        // Get landlord properties
        $properties = $user->properties()->with('images')->get();
        
        // Get summary statistics
        $stats = [
            'total_properties' => $properties->count(),
            'pending_inquiries' => $this->getPendingInquiriesCount(),
            'scheduled_visits' => $this->getPendingVisitsCount(),
            'total_earnings' => $this->getTotalEarnings(),
            'unread_notifications' => $user->unread_notifications_count,
            'new_reviews' => $this->getNewReviewsCount()
        ];

        // Get recent activities
        $recentInquiries = Inquiry::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property'])
            ->latest()
            ->limit(5)
            ->get();

        $upcomingVisits = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->confirmed()
            ->upcoming()
            ->limit(3)
            ->get();

        $recentTransactions = Transaction::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('property')
            ->latest()
            ->limit(5)
            ->get();

        return view('landlord.account', compact(
            'user', 
            'properties',
            'stats', 
            'recentInquiries',
            'upcomingVisits',
            'recentTransactions'
        ));
    }

    // Properties Management
    public function properties(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->properties()->with(['images', 'reviews']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        // Search by title or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('location_text', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        $properties = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get status options
        $statuses = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];

        return view('landlord.properties.index', compact('properties', 'statuses'));
    }

    // Inquiries Management
    public function inquiries(Request $request)
    {
        $user = Auth::user();
        
        $query = Inquiry::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Search by tenant name or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $inquiries = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $userProperties = $user->properties()->get(['id', 'title']);
        $statuses = [
            'pending' => 'Pending',
            'replied' => 'Replied',
            'closed' => 'Closed'
        ];

        return view('landlord.inquiries', compact('inquiries', 'userProperties', 'statuses'));
    }

    // Reply to inquiry
    public function replyInquiry(Inquiry $inquiry, Request $request)
    {
        // Verify landlord owns this property
        if ($inquiry->property->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reply' => 'required|string|max:1000'
        ]);

        $inquiry->update([
            'landlord_reply' => $request->reply,
            'status' => 'replied',
            'replied_at' => now()
        ]);

        // Create notification for tenant
        Notification::create([
            'user_id' => $inquiry->user_id,
            'type' => Notification::TYPE_INQUIRY_REPLIED,
            'title' => 'Inquiry Reply',
            'message' => 'The landlord has replied to your inquiry for ' . $inquiry->property->title,
            'action_url' => route('properties.show', $inquiry->property->slug)
        ]);

        return redirect()->back()
            ->with('success', 'Reply sent successfully!');
    }

    // Scheduled Visits Management
    public function scheduledVisits(Request $request)
    {
        $user = Auth::user();
        
        $query = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('preferred_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('preferred_date', '<=', $request->to_date);
        }

        $visits = $query->orderBy('preferred_date', 'desc')->paginate(15);

        // Get filter options
        $userProperties = $user->properties()->get(['id', 'title']);
        $statuses = ScheduledVisit::getStatuses();

        return view('landlord.scheduled-visits', compact('visits', 'userProperties', 'statuses'));
    }

    // Confirm visit
    public function confirmVisit(ScheduledVisit $visit, Request $request)
    {
        // Verify landlord owns this property
        if ($visit->property->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeConfirmed()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be confirmed.');
        }

        $request->validate([
            'confirmed_date' => 'required|date|after_or_equal:today',
            'confirmed_time' => 'required|string',
            'response' => 'nullable|string|max:500'
        ]);

        $visit->confirm(
            $request->confirmed_date,
            $request->confirmed_time,
            $request->response
        );

        // Create notification for tenant
        Notification::create([
            'user_id' => $visit->user_id,
            'type' => Notification::TYPE_VISIT_CONFIRMED,
            'title' => 'Visit Confirmed',
            'message' => 'Your visit to ' . $visit->property->title . ' has been confirmed.',
            'action_url' => route('tenant.scheduled-visits')
        ]);

        return redirect()->back()
            ->with('success', 'Visit confirmed successfully!');
    }

    // Transactions
    public function transactions(Request $request)
    {
        $user = Auth::user();
        
        $query = Transaction::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $userProperties = $user->properties()->get(['id', 'title']);
        $statuses = Transaction::getStatuses();
        $types = Transaction::getTypes();

        // Get summary data
        $totalEarnings = Transaction::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', 'completed')
            ->sum('amount');

        $pendingAmount = Transaction::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', 'pending')
            ->sum('amount');

        return view('landlord.transactions', compact(
            'transactions',
            'userProperties',
            'statuses',
            'types',
            'totalEarnings',
            'pendingAmount'
        ));
    }

    // Reviews Management
    public function reviews(Request $request)
    {
        $user = Auth::user();
        
        $query = Review::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by property
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        // Filter by replied/unreplied
        if ($request->filled('replied')) {
            if ($request->replied === '1') {
                $query->whereNotNull('landlord_reply');
            } else {
                $query->whereNull('landlord_reply');
            }
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $userProperties = $user->properties()->get(['id', 'title']);

        return view('landlord.reviews', compact('reviews', 'userProperties'));
    }

    // Reply to review
    public function replyReview(Review $review, Request $request)
    {
        // Verify landlord owns this property
        if ($review->property->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reply' => 'required|string|max:1000'
        ]);

        $review->addLandlordReply($request->reply);

        return redirect()->back()
            ->with('success', 'Reply posted successfully!');
    }

    // Notifications
    public function notifications(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->notifications();

        // Filter by read/unread
        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get notification types for filter
        $notificationTypes = Notification::getTypes();

        return view('landlord.notifications', compact('notifications', 'notificationTypes'));
    }

    // Helper methods
    private function getPendingInquiriesCount(): int
    {
        return Inquiry::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 'pending')
            ->count();
    }

    private function getPendingVisitsCount(): int
    {
        return ScheduledVisit::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 'pending')
            ->count();
    }

    private function getTotalEarnings(): float
    {
        return Transaction::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 'completed')
            ->sum('amount');
    }

    private function getNewReviewsCount(): int
    {
        return Review::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereNull('landlord_reply')
            ->count();
    }
}