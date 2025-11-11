<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inquiry;
use App\Models\Favorite;
use App\Models\ScheduledVisit;
use Illuminate\Http\Request;

class HostProfileController extends Controller
{
    public function show($id)
    {
        // Find the host (landlord)
        $host = User::where('role', 'landlord')->findOrFail($id);

        // Get all host's properties with their details
        $properties = $host->properties()
            ->with(['images' => function ($query) {
                $query->where('is_cover', true)->orWhere(function ($q) {
                    $q->whereNull('is_cover')->orWhere('is_cover', false);
                })->orderBy('is_cover', 'desc')->limit(1);
            }])
            ->get();

        // Get statistics
        $propertyIds = $properties->pluck('id');

        $verifiedListings = $properties->where('approval_status', 'approved')->count();

        // Total Inquiries (not Bookings!)
        $totalInquiries = Inquiry::whereIn('property_id', $propertyIds)->count();

        // Total Unique Users who favorited any property
        $totalFavorites = Favorite::whereIn('property_id', $propertyIds)
            ->distinct('user_id')
            ->count('user_id');

        // Total Approved Visits
        $approvedVisits = ScheduledVisit::whereIn('property_id', $propertyIds)
            ->where('status', 'approved')
            ->count();

        // Check if user is currently active (active within last 5 minutes)
        $isActive = $host->last_active_at && $host->last_active_at->gt(now()->subMinutes(5));

        return view('host.profile', compact(
            'host',
            'properties',
            'verifiedListings',
            'totalInquiries',
            'totalFavorites',
            'approvedVisits',
            'isActive'
        ));
    }
}
