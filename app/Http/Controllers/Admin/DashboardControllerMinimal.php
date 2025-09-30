<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardControllerMinimal extends Controller
{
    public function index()
    {
        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Only administrators can access this area');
        }

        try {
            // Ultra-simple stats with raw counts - PostgreSQL safe
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

            // Get counts one by one with error handling
            try {
                $stats['pending_properties'] = DB::select("SELECT COUNT(*) as count FROM properties WHERE approval_status = 'pending'")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Pending properties count failed: ' . $e->getMessage());
            }

            try {
                $stats['approved_properties'] = DB::select("SELECT COUNT(*) as count FROM properties WHERE approval_status = 'approved'")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Approved properties count failed: ' . $e->getMessage());
            }

            try {
                $stats['rejected_properties'] = DB::select("SELECT COUNT(*) as count FROM properties WHERE approval_status = 'rejected'")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Rejected properties count failed: ' . $e->getMessage());
            }

            try {
                $stats['total_users'] = DB::select("SELECT COUNT(*) as count FROM users")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Total users count failed: ' . $e->getMessage());
            }

            try {
                $stats['landlords'] = DB::select("SELECT COUNT(*) as count FROM users WHERE role = 'landlord'")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Landlords count failed: ' . $e->getMessage());
            }

            try {
                $stats['tenants'] = DB::select("SELECT COUNT(*) as count FROM users WHERE role = 'tenant'")[0]->count ?? 0;
            } catch (\Exception $e) {
                Log::warning('Tenants count failed: ' . $e->getMessage());
            }

            // Empty collections for recent items
            $recentProperties = collect([]);
            $recentUsers = collect([]);
            $recentDeletionRequests = collect([]);

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'));

        } catch (\Exception $e) {
            Log::error('Minimal Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Absolute fallback with zeros
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

            return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers', 'recentDeletionRequests'))
                ->with('error', 'Some dashboard data may be unavailable.');
        }
    }
}