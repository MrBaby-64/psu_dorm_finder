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
            // Use raw DB queries for PostgreSQL compatibility
            $stats = [
                'pending_properties' => \DB::table('properties')->where('approval_status', 'pending')->count(),
                'approved_properties' => \DB::table('properties')->where('approval_status', 'approved')->count(),
                'rejected_properties' => \DB::table('properties')->where('approval_status', 'rejected')->count(),
                'pending_deletion_requests' => \DB::table('property_deletion_requests')->where('status', 'pending')->count(),
                'total_deletion_requests' => \DB::table('property_deletion_requests')->count(),
                'total_users' => \DB::table('users')->count(),
                'landlords' => \DB::table('users')->where('role', 'landlord')->count(),
                'tenants' => \DB::table('users')->where('role', 'tenant')->count(),
                'total_bookings' => \DB::table('bookings')->count(),
                'pending_bookings' => \DB::table('bookings')->where('status', 'pending')->count(),
            ];

            // Get recent properties with landlord using JOIN
            $recentProperties = \DB::table('properties')
                ->join('users', 'properties.user_id', '=', 'users.id')
                ->select(
                    'properties.*',
                    'users.name as landlord_name'
                )
                ->orderBy('properties.created_at', 'DESC')
                ->limit(5)
                ->get();

            // Get recent users
            $recentUsers = \DB::table('users')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();

            // Get recent deletion requests with relationships using JOINs
            $recentDeletionRequests = \DB::table('property_deletion_requests')
                ->leftJoin('properties', 'property_deletion_requests.property_id', '=', 'properties.id')
                ->leftJoin('users', 'property_deletion_requests.landlord_id', '=', 'users.id')
                ->select(
                    'property_deletion_requests.*',
                    'properties.title as property_title',
                    'users.name as landlord_name'
                )
                ->where('property_deletion_requests.status', 'pending')
                ->orderBy('property_deletion_requests.created_at', 'DESC')
                ->limit(5)
                ->get();

            // Add computed attributes for deletion requests
            $recentDeletionRequests = $recentDeletionRequests->map(function ($request) {
                $request->status_color = match($request->status) {
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800'
                };
                $request->status_name = ucfirst($request->status);
                return $request;
            });

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));

        } catch (\Exception $e) {
            \Log::error('Admin Dashboard Error: ' . $e->getMessage());

            // Show detailed error in production to debug
            return response()->json([
                'error' => 'Dashboard failed',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'database' => config('database.default')
            ], 500);

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