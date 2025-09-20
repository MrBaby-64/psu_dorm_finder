<?php
// database/seeders/AmenitySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wi-Fi', 'icon' => 'wifi'],
            ['name' => 'Laundry', 'icon' => 'washing-machine'],
            ['name' => 'Air Conditioning', 'icon' => 'air-vent'],
            ['name' => 'Study Area', 'icon' => 'book-open'],
            ['name' => 'Near Jeepney Route', 'icon' => 'bus'],
            ['name' => '24/7 Security', 'icon' => 'shield-check'],
            ['name' => 'Water Included', 'icon' => 'droplets'],
            ['name' => 'Kitchen Access', 'icon' => 'chef-hat'],
            ['name' => 'Parking Space', 'icon' => 'car'],
            ['name' => 'CCTV Surveillance', 'icon' => 'video'],
            ['name' => 'Common Room', 'icon' => 'users'],
            ['name' => 'Balcony/Terrace', 'icon' => 'building'],
            ['name' => 'Near Convenience Store', 'icon' => 'shopping-bag'],
            ['name' => 'Electric Included', 'icon' => 'zap'],
            ['name' => 'Hot Shower', 'icon' => 'shower-head'],
            ['name' => 'Refrigerator', 'icon' => 'refrigerator'],
            ['name' => 'Near Eatery/Carinderia', 'icon' => 'utensils'],
            ['name' => 'Furnished', 'icon' => 'sofa'],
            ['name' => 'Cable TV', 'icon' => 'tv'],
            ['name' => 'Generator Backup', 'icon' => 'battery']
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}