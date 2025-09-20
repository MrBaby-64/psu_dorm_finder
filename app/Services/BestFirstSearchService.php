<?php
// app/Services/BestFirstSearchService.php

namespace App\Services;

class BestFirstSearchService
{
    private CampusOriginService $campusService;

    public function __construct(CampusOriginService $campusService)
    {
        $this->campusService = $campusService;
    }

    /**
     * Rank properties by proximity using Best-First Search heuristic
     *
     * @param float $originLat Origin latitude
     * @param float $originLng Origin longitude  
     * @param array $candidates Array of candidates with [id, lat, lng]
     * @return array Sorted array with [id, score, meters, heuristic]
     */
    public function rank(float $originLat, float $originLng, array $candidates): array
    {
        $results = [];

        foreach ($candidates as $candidate) {
            // Validate candidate structure
            if (!isset($candidate['id'], $candidate['lat'], $candidate['lng'])) {
                continue;
            }

            $distance = $this->calculateHaversineDistance(
                $originLat,
                $originLng,
                $candidate['lat'],
                $candidate['lng']
            );

            // Best-First Search uses distance as heuristic (lower = better)
            $heuristic = $distance;
            
            // Score is inverse of distance (higher = better for sorting)
            $score = $distance > 0 ? 1 / $distance : 999999;

            $results[] = [
                'id' => $candidate['id'],
                'score' => $score,
                'meters' => round($distance, 2),
                'heuristic' => $heuristic,
                'kilometers' => round($distance / 1000, 2)
            ];
        }

        // Sort by heuristic (ascending = closest first)
        usort($results, function ($a, $b) {
            return $a['heuristic'] <=> $b['heuristic'];
        });

        return $results;
    }

    /**
     * Rank properties by campus proximity
     *
     * @param string $campusCode Campus code ('bacolor', 'san_fernando')
     * @param array $candidates Array of candidates with [id, lat, lng]
     * @return array Sorted results
     */
    public function rankByCampus(string $campusCode, array $candidates): array
    {
        $coordinates = $this->campusService->getCoordinates($campusCode);
        
        if (!$coordinates) {
            // Fallback to Bacolor if invalid campus
            $coordinates = $this->campusService->getDefaultCoordinates();
        }

        return $this->rank($coordinates['lat'], $coordinates['lng'], $candidates);
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lng1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lng2 Longitude of second point
     * @return float Distance in meters
     */
    public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get formatted distance string
     *
     * @param float $meters
     * @return string
     */
    public function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . 'm';
        }
        
        return round($meters / 1000, 1) . 'km';
    }

    /**
     * Check if property is within reasonable distance (15km warning threshold)
     *
     * @param float $meters
     * @return bool
     */
    public function isWithinReasonableDistance(float $meters): bool
    {
        return $meters <= 15000; // 15km threshold
    }

    /**
     * Batch rank multiple properties against a campus
     *
     * @param string $campusCode
     * @param \Illuminate\Database\Eloquent\Collection $properties
     * @return array
     */
    public function rankProperties(string $campusCode, $properties): array
    {
        $candidates = $properties->map(function ($property) {
            return [
                'id' => $property->id,
                'lat' => (float) $property->latitude,
                'lng' => (float) $property->longitude
            ];
        })->toArray();

        return $this->rankByCampus($campusCode, $candidates);
    }
}