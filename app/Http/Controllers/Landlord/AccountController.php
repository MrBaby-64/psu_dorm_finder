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

        // Get landlord properties
        $properties = $user->properties()->with('images')->get();

        // Get summary statistics
        $stats = [
            'total_properties' => $properties->count(),
            'pending_inquiries' => $this->getPendingInquiriesCount(),
            'scheduled_visits' => $this->getPendingVisitsCount(),
            'unread_notifications' => $user->unreadNotifications()->count(),
            'new_reviews' => $this->getNewReviewsCount(),
            'admin_messages' => $this->getAdminMessagesCount(),
            'admin_responses' => $this->getAdminResponsesCount()
        ];

        // Get recent activities
        $recentInquiries = Inquiry::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['user', 'property'])
            ->latest()
            ->limit(5)
            ->get();

        // Get pending visit requests (need landlord action)
        $pendingVisitRequests = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        // Get confirmed upcoming visits
        $upcomingVisits = ScheduledVisit::whereHas('property', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['tenant', 'property'])
            ->confirmed()
            ->upcoming()
            ->orderBy('confirmed_date', 'asc')
            ->orderBy('confirmed_time', 'asc')
            ->get();

        // Get imminent visits (today and next 3 days) for urgent notifications
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
}