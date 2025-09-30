<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PropertyControllerMinimal extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403, 'Only landlords can access this area');
        }

        try {
            // Ultra-simple query - PostgreSQL compatible
            $userId = auth()->id();

            // Use raw SQL SELECT for maximum compatibility
            $sql = "SELECT id, title, description, price, room_count, approval_status, created_at, updated_at
                    FROM properties
                    WHERE user_id = ?
                    ORDER BY created_at DESC
                    LIMIT 10";

            $properties = DB::select($sql, [$userId]);

            // Convert to collection for pagination compatibility
            $properties = collect($properties);

            // Create manual paginator
            $perPage = 10;
            $currentPage = $request->get('page', 1);
            $total = DB::select("SELECT COUNT(*) as count FROM properties WHERE user_id = ?", [$userId])[0]->count ?? 0;

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $properties,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            $statuses = [
                'approved' => 'Approved',
                'pending' => 'Pending Approval',
                'rejected' => 'Rejected',
            ];

            return view('landlord.properties.index', [
                'properties' => $paginator,
                'statuses' => $statuses
            ]);

        } catch (\Exception $e) {
            Log::error('Landlord Properties Minimal Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty paginator
            $properties = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                10,
                1,
                ['path' => $request->url()]
            );

            $statuses = [
                'approved' => 'Approved',
                'pending' => 'Pending Approval',
                'rejected' => 'Rejected',
            ];

            return view('landlord.properties.index', [
                'properties' => $properties,
                'statuses' => $statuses,
                'error' => 'Unable to load properties. Please try again.'
            ]);
        }
    }

    public function create()
    {
        if (auth()->user()->role !== 'landlord') {
            abort(403);
        }

        try {
            // Simple amenities query
            $amenities = DB::select("SELECT * FROM amenities ORDER BY name");
            $amenities = collect($amenities);

            $tempImages = [];

            return view('landlord.properties.create', compact('amenities', 'tempImages'));

        } catch (\Exception $e) {
            Log::error('Property create minimal error: ' . $e->getMessage());

            $amenities = collect([]);
            $tempImages = [];

            return view('landlord.properties.create', compact('amenities', 'tempImages'));
        }
    }
}