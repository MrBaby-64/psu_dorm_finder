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

            // Safely get property stats using DB facade for PostgreSQL compatibility
            try {
                $stats['pending_properties'] = \DB::table('properties')->where('approval_status', 'pending')->count();
                $stats['approved_properties'] = \DB::table('properties')->where('approval_status', 'approved')->count();
                $stats['rejected_properties'] = \DB::table('properties')->where('approval_status', 'rejected')->count();
            } catch (\Exception $e) {
                \Log::warning('Property stats unavailable: ' . $e->getMessage());
            }

            // Safely get deletion request stats
            try {
                $stats['pending_deletion_requests'] = \DB::table('property_deletion_requests')->where('status', 'pending')->count();
                $stats['total_deletion_requests'] = \DB::table('property_deletion_requests')->count();
            } catch (\Exception $e) {
                \Log::warning('Deletion request stats unavailable: ' . $e->getMessage());
            }

            // Safely get user stats
            try {
                $stats['total_users'] = \DB::table('users')->count();
                $stats['landlords'] = \DB::table('users')->where('role', 'landlord')->count();
                $stats['tenants'] = \DB::table('users')->where('role', 'tenant')->count();
            } catch (\Exception $e) {
                \Log::warning('User stats unavailable: ' . $e->getMessage());
            }

            // Safely get booking stats
            try {
                $stats['total_bookings'] = \DB::table('bookings')->count();
                $stats['pending_bookings'] = \DB::table('bookings')->where('status', 'pending')->count();
            } catch (\Exception $e) {
                \Log::warning('Booking stats unavailable: ' . $e->getMessage());
            }

            // Safely get recent properties using DB facade for PostgreSQL
            $recentProperties = collect();
            try {
                $properties = \DB::table('properties')
                    ->join('users', 'properties.user_id', '=', 'users.id')
                    ->select('properties.*', 'users.name as landlord_name', 'users.email as landlord_email')
                    ->orderBy('properties.created_at', 'DESC')
                    ->limit(5)
                    ->get();
                $recentProperties = $properties;
            } catch (\Exception $e) {
                \Log::warning('Recent properties unavailable: ' . $e->getMessage());
            }

            // Safely get recent users using DB facade
            $recentUsers = collect();
            try {
                $recentUsers = \DB::table('users')
                    ->select('id', 'name', 'email', 'role', 'created_at')
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                \Log::warning('Recent users unavailable: ' . $e->getMessage());
            }

            // Safely get recent deletion requests using DB facade
            $recentDeletionRequests = collect();
            try {
                $requests = \DB::table('property_deletion_requests')
                    ->join('properties', 'property_deletion_requests.property_id', '=', 'properties.id')
                    ->join('users', 'property_deletion_requests.landlord_id', '=', 'users.id')
                    ->select(
                        'property_deletion_requests.*',
                        'properties.title as property_title',
                        'users.name as landlord_name',
                        'users.email as landlord_email'
                    )
                    ->where('property_deletion_requests.status', 'pending')
                    ->orderBy('property_deletion_requests.created_at', 'DESC')
                    ->limit(5)
                    ->get();
                $recentDeletionRequests = $requests;
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