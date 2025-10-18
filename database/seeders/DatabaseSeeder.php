<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Only seed essential data for production
        $this->call([
            AmenitySeeder::class,  // Essential: Amenity list for the system
            UserSeeder::class,     // Essential: Creates permanent admin account (psuteam001@gmail.com)
            // PropertySeeder disabled - properties created via registration
        ]);
    }
}