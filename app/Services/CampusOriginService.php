<?php
// app/Services/CampusOriginService.php

namespace App\Services;

class CampusOriginService
{
    /**
     * PSU Campus coordinates
     * These are the actual coordinates for Pampanga State University
     */
    private const CAMPUS_COORDINATES = [
        'bacolor' => [
            'lat' => 14.997609479592196,
            'lng' => 120.65313160859495,
            'name' => 'PSU Main Campus',
            'address' => 'Bacolor, Pampanga'
        ],
        'san_fernando' => [
            'lat' => 15.0394,
            'lng' => 120.6887,
            'name' => 'PSU San Fernando Campus',
            'address' => 'San Fernando, Pampanga'
        ]
    ];

    /**
     * Get coordinates for a specific campus
     *
     * @param string $code Campus code ('bacolor' or 'san_fernando')
     * @return array|null ['lat' => float, 'lng' => float] or null if not found
     */
    public function getCoordinates(string $code): ?array
    {
        $code = strtolower(trim($code));
        
        if (!isset(self::CAMPUS_COORDINATES[$code])) {
            return null;
        }

        $campus = self::CAMPUS_COORDINATES[$code];
        return [
            'lat' => $campus['lat'],
            'lng' => $campus['lng']
        ];
    }

    /**
     * Get full campus information
     *
     * @param string $code
     * @return array|null
     */
    public function getCampusInfo(string $code): ?array
    {
        $code = strtolower(trim($code));
        return self::CAMPUS_COORDINATES[$code] ?? null;
    }

    /**
     * Get all available campuses
     *
     * @return array
     */
    public function getAllCampuses(): array
    {
        return self::CAMPUS_COORDINATES;
    }

    /**
     * Get default campus (Bacolor)
     *
     * @return array
     */
    public function getDefaultCoordinates(): array
    {
        return $this->getCoordinates('bacolor');
    }

    /**
     * Validate if a campus code exists
     *
     * @param string $code
     * @return bool
     */
    public function isValidCampus(string $code): bool
    {
        return isset(self::CAMPUS_COORDINATES[strtolower(trim($code))]);
    }

    /**
     * Get campus options for forms/dropdowns
     *
     * @return array
     */
    public function getCampusOptions(): array
    {
        $options = [];
        foreach (self::CAMPUS_COORDINATES as $code => $campus) {
            $options[$code] = $campus['name'];
        }
        return $options;
    }
}