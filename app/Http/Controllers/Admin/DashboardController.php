<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDeletionRequest;
use App\Models\User;
use App\Models\Booking;
use App\Models\Message;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }

        try {
            // Use Eloquent models - same as localhost
            $stats = [
                'pending_properties' => Property::where('approval_status', 'pending')->count(),
                'approved_properties' => Property::where('approval_status', 'approved')->count(),
                'rejected_properties' => Property::where('approval_status', 'rejected')->count(),
                'pending_deletion_requests' => PropertyDeletionRequest::where('status', 'pending')->count(),
                'total_deletion_requests' => PropertyDeletionRequest::count(),
                'total_users' => User::count(),
                'landlords' => User::where('role', 'landlord')->count(),
                'tenants' => User::where('role', 'tenant')->count(),
                'total_bookings' => Booking::count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
            ];

            // Get recent properties with landlord - simpler
            $recentProperties = Property::with('landlord')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            // Get recent users
            $recentUsers = User::orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            // Get recent deletion requests with relationships
            $recentDeletionRequests = PropertyDeletionRequest::with(['property', 'landlord'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));

        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());

            // Fallback with empty data
            $stats = [
                'pending_properties' => 0,
                'approved_properties' => 0,
                'rejected_properties' => 0,
                'pending_deletion_requests' => 0,
                'total_deletion_requests' => 0,
                'total_users' => 0,
                'landlords' => 0,
                'tenants' => 0,
                'total_bookings' => 0,
                'pending_bookings' => 0,
            ];

            $recentProperties = collect([]);
            $recentUsers = collect([]);
            $recentDeletionRequests = collect([]);

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));
        }
    }
}