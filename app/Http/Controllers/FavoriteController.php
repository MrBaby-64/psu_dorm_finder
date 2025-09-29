<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'tenant') {
                abort(403, 'Only tenants can manage favorites.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->favorites()
            ->with(['property' => function($q) {
                $q->with([
                    'images' => function($imageQuery) {
                        $imageQuery->orderBy('is_cover', 'desc')->orderBy('sort_order');
                    },
                    'landlord',
                    'amenities'
                ]);
            }]);

        // Search in favorites
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('property', function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('location_text', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->whereHas('property', function($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        $favorites = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get available cities for filter
        $cities = Property::select('city')
            ->whereHas('favorites', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('tenant.favorites', compact('favorites', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $property = Property::findOrFail($validated['property_id']);

        Log::info('Favorite creation attempt', [
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'validated_keys' => array_keys($validated)
        ]);

        try {
            $favorite = DB::transaction(function () use ($validated, $property) {
                // Check if already favorited
                $existing = Favorite::where('user_id', Auth::id())
                    ->where('property_id', $property->id)
                    ->first();

                if ($existing) {
                    throw new \Exception('Property is already in your favorites.');
                }

                // Create favorite
                return Favorite::create([
                    'user_id' => Auth::id(),
                    'property_id' => $property->id
                ]);
            });

            // Create notification for landlord (outside transaction as it's not critical)
            Notification::create([
                'user_id' => $property->user_id,
                'type' => Notification::TYPE_FAVORITE_ADDED,
                'title' => 'Property Favorited',
                'message' => Auth::user()->name . ' has added your property "' . $property->title . '" to their favorites.',
                'data' => [
                    'property_id' => $property->id,
                    'tenant_name' => Auth::user()->name,
                    'favorite_id' => $favorite->id
                ],
                'action_url' => route('properties.show', $property->slug)
            ]);

            Log::info('Favorite created successfully', [
                'favorite_id' => $favorite->id,
                'user_id' => Auth::id(),
                'property_id' => $property->id
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Property added to favorites!',
                    'favorited' => true
                ]);
            }

            return redirect()->back()->with('success', 'Property added to favorites!');

        } catch (\Exception $e) {
            Log::error('Favorite creation failed', [
                'user_id' => Auth::id(),
                'property_id' => $property->id,
                'error' => $e->getMessage(),
                'first_error' => $e->getMessage(),
                'validated_keys' => array_keys($validated)
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return back()->withErrors(['general' => $e->getMessage()]);
        }
    }

    public function destroy(Property $property)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->first();

        if (!$favorite) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property is not in your favorites.'
                ], 400);
            }

            return redirect()->back()->with('error', 'Property is not in your favorites.');
        }

        $favorite->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Property removed from favorites!',
                'favorited' => false
            ]);
        }

        return redirect()->back()->with('success', 'Property removed from favorites!');
    }

    public function toggle(Request $request)
    {
        try {
            $request->validate([
                'property_id' => 'required|exists:properties,id'
            ]);

            $property = Property::findOrFail($request->property_id);

            $favorite = Favorite::where('user_id', Auth::id())
                ->where('property_id', $property->id)
                ->first();

            if ($favorite) {
                // Remove from favorites
                $favorite->delete();
                $favorited = false;
                $message = 'Property removed from favorites!';
            } else {
                // Add to favorites
                Favorite::create([
                    'user_id' => Auth::id(),
                    'property_id' => $property->id
                ]);

                // Create notification for landlord (wrap in try-catch to prevent failure)
                try {
                    Notification::create([
                        'user_id' => $property->user_id,
                        'type' => Notification::TYPE_FAVORITE_ADDED,
                        'title' => 'Property Favorited',
                        'message' => Auth::user()->name . ' has added your property "' . $property->title . '" to their favorites.',
                        'data' => [
                            'property_id' => $property->id,
                            'tenant_name' => Auth::user()->name
                        ],
                        'action_url' => route('properties.show', $property->slug)
                    ]);
                } catch (\Exception $e) {
                    // Log notification error but don't fail the favorite action
                    \Log::warning('Failed to create favorite notification: ' . $e->getMessage());
                }

                $favorited = true;
                $message = 'Property added to favorites!';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'favorited' => $favorited
                ]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Favorite toggle error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update favorites. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update favorites. Please try again.');
        }
    }

    public function check(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id'
        ]);

        $favorited = Favorite::where('user_id', Auth::id())
            ->where('property_id', $request->property_id)
            ->exists();

        return response()->json([
            'favorited' => $favorited
        ]);
    }
}