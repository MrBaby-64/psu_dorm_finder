<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDeletionRequest;
use App\Models\User;
use App\Models\Booking;
use App\Models\Message;

/**
 * DashboardController
 *
 * Handles admin dashboard operations including statistics display,
 * property approval metrics, user counts, and deletion request summaries.
 */
class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with comprehensive platform statistics
     */
    public function index()
    {
        // Restrict access to admin role only
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }

        try {
            // Gather platform statistics using Eloquent queries
            $stats = [
                'pending_properties' => Property::where('approval_status', 'pending')->count(),
                'approved_properties' => Property::where('approval_status', 'approved')->count(),
                'rejected_properties' => Property::where('approval_status', 'rejected')->count(),
                'pending_deletion_requests' => PropertyDeletionRequest::where('status', 'pending')->count(),
                'total_deletion_requests' => PropertyDeletionRequest::count(),
                'total_users' => User::count(),
                'landlords' => User::where('role', 'landlord')->count(),
                'tenants' => User::where('role', 'tenant')->count(),
                'total_bookings' => Booking::where('status', 'approved')->count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
            ];

            // Fetch recent properties with landlord information for dashboard display
            $recentProperties = Property::with('landlord:id,name')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            // Fetch recently registered users
            $recentUsers = User::orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            // Fetch pending deletion requests with related property and landlord data
            $recentDeletionRequests = PropertyDeletionRequest::with(['property:id,title', 'landlord:id,name'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));

        } catch (\Exception $e) {
            // Log the error with full context for debugging
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());

            // Return detailed error response for troubleshooting
            return response()->json([
                'error' => 'Dashboard failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'database' => config('database.default')
            ], 500);
        }
    }
}