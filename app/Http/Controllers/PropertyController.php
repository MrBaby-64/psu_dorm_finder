<?php
// app/Http/Controllers/PropertyController.php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Amenity;
use App\Services\BestFirstSearchService;
use App\Services\CampusOriginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    protected $bfsService;
    protected $campusService;

    public function __construct(BestFirstSearchService $bfsService, CampusOriginService $campusService)
    {
        $this->bfsService = $bfsService;
        $this->campusService = $campusService;
    }

    /**
     * Display property browse/search page
     */
    public function browse(Request $request)
    {
        $query = Property::query()
            ->with(['coverImage', 'amenities', 'landlord'])
            ->approved();

        // Apply filters
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('location_text', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('room_count')) {
            $query->where('room_count', '>=', $request->room_count);
        }

        if ($request->boolean('is_verified')) {
            $query->verified();
        }

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        $originCampus = $request->get('origin_campus', 'bacolor');

        if ($sort === 'nearest') {
            // Get all matching properties first
            $properties = $query->get();
            
            // Rank by distance
            $ranked = $this->bfsService->rankProperties($originCampus, $properties);
            
            // Create a map of id => distance
            $distanceMap = collect($ranked)->pluck('meters', 'id');
            
            // Get property IDs in order
            $orderedIds = collect($ranked)->pluck('id')->toArray();
            
            // Re-query with proper ordering
            $properties = Property::with(['coverImage', 'amenities', 'landlord'])
                ->whereIn('id', $orderedIds)
                ->get()
                ->sortBy(function($property) use ($distanceMap) {
                    return $distanceMap[$property->id];
                });
            
            // Attach distance to each property
            $properties->transform(function($property) use ($distanceMap) {
                $property->distance_meters = $distanceMap[$property->id];
                return $property;
            });

            // Manual pagination
            $perPage = 12;
            $currentPage = $request->get('page', 1);
            $total = $properties->count();
            $properties = $properties->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $properties,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        } else {
            // Standard sorting
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $paginator = $query->paginate(12)->withQueryString();
        }

        // Get amenities for filter
        $amenities = Amenity::orderBy('name')->get();

        return view('properties.browse', [
            'properties' => $paginator,
            'amenities' => $amenities,
            'filters' => $request->all(),
            'campuses' => $this->campusService->getCampusOptions(),
            'sort' => $sort,
            'originCampus' => $originCampus
        ]);
    }

    /**
     * Display property details
     */
    public function show(Property $property)
    {
        // Load relationships
        $property->load([
            'images',
            'amenities',
            'rooms',
            'landlord',
            'reviews.user'
        ]);

        // Get similar nearby properties
        $nearbyProperties = $this->getNearbyProperties($property);

        // Calculate distance from default campus
        $campusCoords = $this->campusService->getCoordinates(
            $property->city === 'Bacolor' ? 'bacolor' : 'san_fernando'
        );
        
        $distance = $this->bfsService->calculateHaversineDistance(
            $campusCoords['lat'],
            $campusCoords['lng'],
            $property->latitude,
            $property->longitude
        );

        return view('properties.show', [
            'property' => $property,
            'nearbyProperties' => $nearbyProperties,
            'distanceFromCampus' => $this->bfsService->formatDistance($distance)
        ]);
    }

    /**
     * Get nearby properties
     */
    protected function getNearbyProperties(Property $property, $limit = 4)
    {
        $allProperties = Property::approved()
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->with(['coverImage'])
            ->get();

        if ($allProperties->isEmpty()) {
            return collect();
        }

        $candidates = $allProperties->map(fn($p) => [
            'id' => $p->id,
            'lat' => (float) $p->latitude,
            'lng' => (float) $p->longitude
        ])->toArray();

        $ranked = $this->bfsService->rank(
            $property->latitude,
            $property->longitude,
            $candidates
        );

        $nearbyIds = collect($ranked)->take($limit)->pluck('id');
        
        return Property::with(['coverImage'])
            ->whereIn('id', $nearbyIds)
            ->get();
    }

    /**
     * Home page with featured properties
     */
    public function home()
    {
        $featuredProperties = Property::approved()
            ->featured()
            ->with(['coverImage', 'amenities'])
            ->orderBy('display_priority', 'desc')
            ->take(6)
            ->get();

        $recentProperties = Property::approved()
            ->with(['coverImage'])
            ->latest()
            ->take(8)
            ->get();

        return view('home', [
            'featuredProperties' => $featuredProperties,
            'recentProperties' => $recentProperties,
            'campuses' => $this->campusService->getCampusOptions()
        ]);
    }
}