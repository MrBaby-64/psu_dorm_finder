<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Notification;
use App\Models\ScheduledVisit;
use App\Models\Review;
use App\Models\Inquiry;
use App\Models\Message;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Landlord Account Controller
 * Manages landlord dashboard and account overview
 */
class AccountController extends Controller
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

    public function index(): View
    {
        $user = Auth::user();

        // Fetch landlord properties
        $properties = $user->properties()->with('images')->get();

        // Fetch summary statistics
        $stats = [
            'total_properties' => $properties->count(),
            'pending_inquiries' => $this->getPendingInquiriesCount(),
            'approved_inquiries' => $this->getApprovedInquiriesCount(),
            'scheduled_visits' => $this->getPendingVisitsCount(),
            'unread_notifications' => $user->unreadNotifications()->count(),
            'new_reviews' => $this->getNewReviewsCount(),
            'admin_messages' => $this->getAdminMessagesCount(),
            'admin_responses' => $this->getAdminResponsesCount(),
            // Add booking statistics
            'pending_bookings' => $this->getPendingBookingsCount(),
            'approved_bookings' => $this->getApprovedBookingsCount(),
            'active_bookings' => $this->getActiveBookingsCount(),
            'completed_bookings' => $this->getCompletedBookingsCount(),
            'total_bookings' => $this->getTotalBookingsCount(),
        ];

        // Fetch recent activities
        $recentInquiries = Inquiry::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property'])
            ->latest()
            ->limit(5)
            ->get();

        // Fetch pending visit requests (need landlord action)
        $pendingVisitRequests = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch confirmed upcoming visits
        $upcomingVisits = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->confirmed()
            ->upcoming()
            ->orderBy('confirmed_date', 'asc')
            ->orderBy('confirmed_time', 'asc')
            ->get();

        // Fetch imminent visits (today and next 3 days) for urgent notifications
        $imminentVisits = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->imminentVisits()
            ->get();

        $todayVisits = $imminentVisits->filter(function($visit) {
            return $visit->isToday();
        });

        $next3DaysVisits = $imminentVisits->filter(function($visit) {
            return !$visit->isToday() && $visit->isUrgent();
        });

        // Add counts to stats
        $stats['today_visits'] = $todayVisits->count();
        $stats['next_3_days_visits'] = $next3DaysVisits->count();
        $stats['imminent_visits'] = $imminentVisits->count();
        $stats['pending_visit_requests'] = $pendingVisitRequests->count();

        return view('landlord.account', compact(
            'user',
            'properties',
            'stats',
            'recentInquiries',
            'upcomingVisits',
            'imminentVisits',
            'todayVisits',
            'next3DaysVisits',
            'pendingVisitRequests'
        ));
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


    private function getNewReviewsCount(): int
    {
        return Review::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereNull('landlord_reply')
            ->count();
    }

    private function getAdminMessagesCount(): int
    {
        return \App\Models\AdminMessage::where('sender_id', Auth::id())->count();
    }

    private function getAdminResponsesCount(): int
    {
        // Count admin responses that haven't been read yet
        // We check for notifications with admin_response type that are unread
        return Notification::where('user_id', Auth::id())
            ->where('type', Notification::TYPE_ADMIN_RESPONSE)
            ->where('is_read', false)
            ->count();
    }

    private function getApprovedInquiriesCount(): int
    {
        return Inquiry::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', 'approved')
            ->count();
    }

    private function getPendingBookingsCount(): int
    {
        return Booking::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', Booking::STATUS_PENDING)
            ->count();
    }

    private function getApprovedBookingsCount(): int
    {
        return Booking::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', Booking::STATUS_APPROVED)
            ->count();
    }

    private function getActiveBookingsCount(): int
    {
        return Booking::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', Booking::STATUS_ACTIVE)
            ->count();
    }

    private function getCompletedBookingsCount(): int
    {
        return Booking::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();
    }

    private function getTotalBookingsCount(): int
    {
        // Count all bookings that have been approved (includes approved, active, and completed)
        return Booking::whereHas('property', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereIn('status', [
                Booking::STATUS_APPROVED,
                Booking::STATUS_ACTIVE,
                Booking::STATUS_COMPLETED
            ])
            ->count();
    }
}