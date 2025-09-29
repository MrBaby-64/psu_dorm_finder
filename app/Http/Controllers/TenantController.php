<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Notification;
use App\Models\ScheduledVisit;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'tenant') {
                abort(403, 'Only tenants can access this area');
            }
            return $next($request);
        });
    }

    // Account Dashboard
    public function account()
    {
        $user = Auth::user();
        
        // Get summary statistics
        $stats = [
            'favorites_count' => $user->favorites()->count(),
            'scheduled_visits_count' => $user->scheduledVisits()->count(),
            'reviews_count' => $user->reviews()->count(),
            'unread_notifications' => $user->unread_notifications_count
        ];

        $upcomingVisits = $user->scheduledVisits()
            ->with('property')
            ->confirmed()
            ->upcoming()
            ->limit(3)
            ->get();

        $recentNotifications = $user->notifications()
            ->latest()
            ->limit(5)
            ->get();

        return view('tenant.account', compact(
            'user',
            'stats',
            'upcomingVisits',
            'recentNotifications'
        ));
    }


    // Favorites
    public function favorites(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->favorites()
            ->with(['property' => function($q) {
                $q->with(['images', 'landlord']);
            }]);

        // Search in favorites
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('property', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('location_text', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        $favorites = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('tenant.favorites', compact('favorites'));
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

        // Mark all as read if requested
        if ($request->has('mark_all_read')) {
            $user->notifications()->unread()->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return redirect()->route('tenant.notifications')
                ->with('success', 'All notifications marked as read.');
        }

        // Get notification types for filter
        $notificationTypes = Notification::getTypes();

        return view('tenant.notifications', compact('notifications', 'notificationTypes'));
    }

    // Test method to check authentication
    public function markAllNotificationsReadTest()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'authenticated' => false,
                'message' => 'User not authenticated'
            ]);
        }

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'user_role' => $user->role,
            'unread_count' => $unreadCount,
            'message' => 'Authentication working - ready to mark notifications as read'
        ]);
    }

    // Mark all notifications as read
    public function markAllNotificationsRead(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Unauthenticated'], 401);
                }
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Simple and safe approach - just mark notifications as read
            $updated = $user->notifications()->where('is_read', false)->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            $message = $updated > 0
                ? "Successfully marked {$updated} notification(s) as read."
                : 'No unread notifications found.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'updated' => $updated
                ]);
            }

            return redirect()->route('tenant.notifications')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error marking notifications as read: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to mark notifications as read'], 500);
            }

            return redirect()->route('tenant.notifications')->with('error', 'Failed to mark notifications as read. Please try again.');
        }
    }

    // Mark single notification as read
    public function markNotificationRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    // Scheduled Visits
    public function scheduledVisits(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->scheduledVisits()->with(['property', 'property.landlord']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('preferred_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('preferred_date', '<=', $request->to_date);
        }

        $visits = $query->orderBy('preferred_date', 'desc')->paginate(15);

        // Get status options
        $statuses = ScheduledVisit::getStatuses();

        return view('tenant.scheduled-visits', compact('visits', 'statuses'));
    }

    // Cancel visit
    public function cancelVisit(ScheduledVisit $visit, Request $request)
    {
        if ($visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This visit cannot be cancelled.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $visit->cancel(Auth::id(), $request->reason);

        return redirect()->back()
            ->with('success', 'Visit cancelled successfully.');
    }

    // Reviews (To Review)
    public function reviews(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->reviews()->with('property');

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by property name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('property', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tenant.reviews', compact('reviews'));
    }

    // Store new review
    public function storeReview(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'location_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5'
        ]);

        // Check if user already reviewed this property
        $existingReview = Review::where('user_id', Auth::id())
            ->where('property_id', $request->property_id)
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'You have already reviewed this property.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'property_id' => $request->property_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'cleanliness_rating' => $request->cleanliness_rating,
            'location_rating' => $request->location_rating,
            'value_rating' => $request->value_rating,
            'communication_rating' => $request->communication_rating
        ]);

        return redirect()->back()
            ->with('success', 'Review posted successfully!');
    }
}