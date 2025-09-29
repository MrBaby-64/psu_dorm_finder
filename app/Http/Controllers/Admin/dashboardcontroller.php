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
            // Initialize stats with safe defaults
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

            // Safely get property stats
            try {
                $stats['pending_properties'] = Property::where('approval_status', 'pending')->count();
                $stats['approved_properties'] = Property::where('approval_status', 'approved')->count();
                $stats['rejected_properties'] = Property::where('approval_status', 'rejected')->count();
            } catch (\Exception $e) {
                \Log::warning('Property stats unavailable: ' . $e->getMessage());
            }

            // Safely get deletion request stats if table exists
            try {
                if (\Schema::hasTable('property_deletion_requests')) {
                    $stats['pending_deletion_requests'] = PropertyDeletionRequest::where('status', 'pending')->count();
                    $stats['total_deletion_requests'] = PropertyDeletionRequest::count();
                }
            } catch (\Exception $e) {
                \Log::warning('Deletion request stats unavailable: ' . $e->getMessage());
            }

            // Safely get user stats
            try {
                $stats['total_users'] = User::count();
                $stats['landlords'] = User::where('role', 'landlord')->count();
                $stats['tenants'] = User::where('role', 'tenant')->count();
            } catch (\Exception $e) {
                \Log::warning('User stats unavailable: ' . $e->getMessage());
            }

            // Safely get booking stats if table exists
            try {
                if (\Schema::hasTable('bookings')) {
                    $stats['total_bookings'] = Booking::count();
                    $stats['pending_bookings'] = Booking::where('status', 'pending')->count();
                }
            } catch (\Exception $e) {
                \Log::warning('Booking stats unavailable: ' . $e->getMessage());
            }

            // Safely get recent properties
            $recentProperties = collect();
            try {
                $recentProperties = Property::with(['landlord' => function($query) {
                        $query->select('id', 'name', 'email');
                    }])
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Recent properties unavailable: ' . $e->getMessage());
            }

            // Safely get recent users
            $recentUsers = collect();
            try {
                $recentUsers = User::select('id', 'name', 'email', 'role', 'created_at')
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Recent users unavailable: ' . $e->getMessage());
            }

            // Safely get recent deletion requests
            $recentDeletionRequests = collect();
            try {
                if (\Schema::hasTable('property_deletion_requests')) {
                    $recentDeletionRequests = PropertyDeletionRequest::with([
                            'property' => function($query) {
                                $query->select('id', 'title', 'user_id');
                            },
                            'landlord' => function($query) {
                                $query->select('id', 'name', 'email');
                            }
                        ])
                        ->where('status', 'pending')
                        ->latest()
                        ->take(5)
                        ->get();
                }
            } catch (\Exception $e) {
                \Log::warning('Recent deletion requests unavailable: ' . $e->getMessage());
            }

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