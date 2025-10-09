<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoutingController extends Controller
{
    /**
     * Get multiple route alternatives from origin to destination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoutes(Request $request)
    {
        $request->validate([
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'dest_lat' => 'required|numeric',
            'dest_lng' => 'required|numeric',
            'profile' => 'sometimes|in:car,foot,bike'
        ]);

        $originLat = $request->origin_lat;
        $originLng = $request->origin_lng;
        $destLat = $request->dest_lat;
        $destLng = $request->dest_lng;
        $profile = $request->profile ?? 'car';

        try {
            // Use GraphHopper public API (free tier)
            $response = Http::timeout(15)->get('https://graphhopper.com/api/1/route', [
                'point' => [
                    "{$originLat},{$originLng}",
                    "{$destLat},{$destLng}"
                ],
                'profile' => $profile,
                'locale' => 'en',
                'instructions' => true,
                'calc_points' => true,
                'points_encoded' => false,
                'alternative_route.max_paths' => 3, // Get up to 3 alternative routes
                'algorithm' => 'alternative_route',
                'key' => config('services.graphhopper.api_key', '')
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['paths']) && count($data['paths']) > 0) {
                    $routes = [];

                    foreach ($data['paths'] as $index => $path) {
                        $routes[] = [
                            'id' => $index,
                            'distance' => $path['distance'], // in meters
                            'duration' => $path['time'], // in milliseconds
                            'duration_text' => $this->formatDuration($path['time']),
                            'distance_text' => $this->formatDistance($path['distance']),
                            'geometry' => $path['points']['coordinates'] ?? [],
                            'instructions' => $this->formatInstructions($path['instructions'] ?? [])
                        ];
                    }

                    return response()->json([
                        'success' => true,
                        'routes' => $routes,
                        'use_google_maps' => false
                    ]);
                }
            }

            // Check if API limit exceeded or error
            $statusCode = $response->status();
            if ($statusCode === 429 || $statusCode >= 400) {
                Log::warning('GraphHopper API limit exceeded or failed. Status: ' . $statusCode);

                // Return flag to use Google Maps fallback
                return response()->json([
                    'success' => false,
                    'use_google_maps' => true,
                    'message' => 'Routing service temporarily unavailable. Opening Google Maps for directions.'
                ]);
            }

            // If no routes found but API worked, use Google Maps
            return response()->json([
                'success' => false,
                'use_google_maps' => true,
                'message' => 'Unable to calculate route. Opening Google Maps for directions.'
            ]);

        } catch (\Exception $e) {
            Log::error('Routing API error: ' . $e->getMessage());

            // Return flag to use Google Maps on error
            return response()->json([
                'success' => false,
                'use_google_maps' => true,
                'message' => 'Routing service unavailable. Opening Google Maps for directions.'
            ]);
        }
    }

    /**
     * Generate a simple fallback route using straight line
     */
    private function generateFallbackRoute($originLat, $originLng, $destLat, $destLng)
    {
        // Calculate straight-line distance using Haversine formula
        $distance = $this->calculateDistance($originLat, $originLng, $destLat, $destLng);

        // Estimate driving time (assuming average speed of 30 km/h in city)
        $durationMinutes = ($distance / 1000) / 30 * 60;
        $durationMs = $durationMinutes * 60 * 1000;

        return [
            'id' => 0,
            'distance' => $distance,
            'duration' => $durationMs,
            'duration_text' => $this->formatDuration($durationMs),
            'distance_text' => $this->formatDistance($distance),
            'geometry' => [
                [$originLng, $originLat],
                [$destLng, $destLat]
            ],
            'instructions' => [
                [
                    'text' => 'Head to destination',
                    'distance' => $distance,
                    'time' => $durationMs
                ]
            ]
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Format duration from milliseconds to human-readable text
     */
    private function formatDuration($milliseconds)
    {
        $minutes = round($milliseconds / 1000 / 60);

        if ($minutes < 60) {
            return $minutes . ' min';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes == 0) {
            return $hours . ' hr';
        }

        return $hours . ' hr ' . $remainingMinutes . ' min';
    }

    /**
     * Format distance from meters to human-readable text
     */
    private function formatDistance($meters)
    {
        if ($meters < 1000) {
            return round($meters) . ' m';
        }

        $kilometers = $meters / 1000;
        return number_format($kilometers, 1) . ' km';
    }

    /**
     * Format turn-by-turn instructions
     */
    private function formatInstructions($instructions)
    {
        $formatted = [];

        foreach ($instructions as $instruction) {
            $formatted[] = [
                'text' => $instruction['text'] ?? 'Continue',
                'distance' => $instruction['distance'] ?? 0,
                'time' => $instruction['time'] ?? 0
            ];
        }

        return $formatted;
    }
}
