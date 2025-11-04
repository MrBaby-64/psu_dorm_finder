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
            ->approved()
            ->whereHas('landlord'); // Only show properties with existing landlords

        // Add filters
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

        // Handle price range filtering
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;

            if ($priceRange === '0-5000') {
                $query->where('price', '<=', 5000);
            } elseif ($priceRange === '5000-10000') {
                $query->whereBetween('price', [5000, 10000]);
            } elseif ($priceRange === '10000-15000') {
                $query->whereBetween('price', [10000, 15000]);
            } elseif ($priceRange === '15000+') {
                $query->where('price', '>=', 15000);
            }
        }

        // Handle individual price min/max (for custom filters)
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->filled('room_count')) {
            $roomCount = (int) $request->room_count;
            $query->where('room_count', '>=', $roomCount)
                  ->whereNotNull('room_count')
                  ->where('room_count', '>', 0);
        }

        if ($request->boolean('is_verified')) {
            $query->verified();
        }

        // Handle sorting
        $sort = $request->get('sort', 'newest');
        $originCampus = 'bacolor'; // Always use PSU Main Campus for distance calculations

        // No auto-sorting needed since filters are removed

        if ($sort === 'nearest') {
            // Retrieve all matching properties first
            $properties = $query->get();
            
            // Rank by distance
            $ranked = $this->bfsService->rankProperties($originCampus, $properties);
            
            // Create a map of id => distance
            $distanceMap = collect($ranked)->pluck('meters', 'id');
            
            // Fetch property IDs in order
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
                case 'room_asc':
                    $query->orderBy('room_count', 'asc')->orderBy('price', 'asc');
                    break;
                case 'room_desc':
                    $query->orderBy('room_count', 'desc')->orderBy('price', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $paginator = $query->paginate(12)->withQueryString();
        }

        // Fetch amenities for filter
        $amenities = Amenity::orderBy('name')->get();

        return view('properties.browse', [
            'properties' => $paginator,
            'amenities' => $amenities,
            'filters' => $request->all(),
            'campuses' => $this->campusService->getCampusOptions(),
            'sort' => $sort,
            'originCampus' => $originCampus,
            'autoSorted' => false,
            'autoSortType' => null
        ]);
    }

    /**
     * Display property details
     */
    public function show(Property $property)
    {
        // Fetch relationships
        $property->load([
            'images',
            'amenities',
            'rooms.images',
            'landlord',
            'reviews.user'
        ]);

        // Fetch similar nearby properties
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

    // Add these methods to your existing PropertyController.php

public function searchSuggestions(Request $request)
{
    $query = $request->get('q');

    if (strlen($query) < 1) {
        return response()->json([]);
    }

    $searchTerm = strtolower($query);

    $suggestions = Property::approved()
        ->select('title', 'slug', 'city', 'price')
        ->where(function($q) use ($searchTerm) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(location_text) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(city) LIKE ?', ["%{$searchTerm}%"]);
        })
        // Order by: titles starting with query first, then others
        ->orderByRaw("CASE
            WHEN LOWER(title) LIKE ? THEN 1
            WHEN LOWER(title) LIKE ? THEN 2
            ELSE 3
        END", ["{$searchTerm}%", "%{$searchTerm}%"])
        ->limit(8)
        ->get()
        ->map(function($property) {
            return [
                'title' => $property->title,
                'location' => $property->city,
                'price' => '₱' . number_format($property->price),
                'url' => route('properties.show', $property->slug)
            ];
        });

    return response()->json($suggestions);
}

public function checkAvailability(Request $request, Property $property)
{
    $request->validate([
        'check_in' => 'required|date|after:today',
        'check_out' => 'nullable|date|after:check_in',
        'room_id' => 'nullable|exists:rooms,id'
    ]);

    $available = true;
    $message = 'Property is available for your selected dates.';

    // Check specific room availability if room_id is provided
    if ($request->room_id) {
        $room = $property->rooms()->find($request->room_id);
        if (!$room || $room->status !== 'available') {
            $available = false;
            $message = 'Selected room is not available.';
        }
    } else {
        // Check if any room is available
        $availableRooms = $property->rooms()->where('status', 'available')->count();
        if ($availableRooms === 0) {
            $available = false;
            $message = 'No rooms are currently available.';
        }
    }

    return response()->json([
        'available' => $available,
        'message' => $message
    ]);
    }
}