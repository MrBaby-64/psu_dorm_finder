<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
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

        $stats = [
            'pending_properties' => Property::where('approval_status', 'pending')->count(),
            'approved_properties' => Property::where('approval_status', 'approved')->count(),
            'rejected_properties' => Property::where('approval_status', 'rejected')->count(),
            'total_users' => User::count(),
            'landlords' => User::where('role', 'landlord')->count(),
            'tenants' => User::where('role', 'tenant')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        $recentProperties = Property::with(['landlord'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentProperties', 'recentUsers'));
    }
}