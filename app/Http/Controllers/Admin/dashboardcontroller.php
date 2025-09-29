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

            $recentProperties = Property::with(['landlord' => function($query) {
                    $query->select('id', 'name', 'email');
                }])
                ->latest()
                ->take(5)
                ->get();

            $recentUsers = User::select('id', 'name', 'email', 'role', 'created_at')
                ->latest()
                ->take(5)
                ->get();

            $recentDeletionRequests = PropertyDeletionRequest::with([
                    'property' => function($query) {
                        $query->select('id', 'title', 'user_id');
                    },
                    'landlord' => function($query) {
                        $query->select('id', 'name', 'email');
                    }
                ])
                ->latest()
                ->take(5)
                ->get();

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));

        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());

            // Provide fallback data
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

            session()->flash('warning', 'Dashboard data could not be fully loaded. Please check the system configuration.');

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));
        }
    }
}