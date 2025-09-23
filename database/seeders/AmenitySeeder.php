<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wi-Fi', 'icon' => 'wifi', 'category' => 'basic'],
            ['name' => 'Laundry', 'icon' => 'washing-machine', 'category' => 'facilities'],
            ['name' => 'Air Conditioning', 'icon' => 'air-vent', 'category' => 'comfort'],
            ['name' => 'Study Area', 'icon' => 'book-open', 'category' => 'facilities'],
            ['name' => 'Near Jeepney Route', 'icon' => 'bus', 'category' => 'basic'],
            ['name' => '24/7 Security', 'icon' => 'shield-check', 'category' => 'security'],
            ['name' => 'Water Included', 'icon' => 'droplets', 'category' => 'basic'],
            ['name' => 'Kitchen Access', 'icon' => 'chef-hat', 'category' => 'facilities'],
            ['name' => 'Parking Space', 'icon' => 'car', 'category' => 'facilities'],
            ['name' => 'CCTV Surveillance', 'icon' => 'video', 'category' => 'security'],
            ['name' => 'Common Room', 'icon' => 'users', 'category' => 'facilities'],
            ['name' => 'Balcony/Terrace', 'icon' => 'building', 'category' => 'comfort'],
            ['name' => 'Near Convenience Store', 'icon' => 'shopping-bag', 'category' => 'basic'],
            ['name' => 'Electric Included', 'icon' => 'zap', 'category' => 'basic'],
            ['name' => 'Hot Shower', 'icon' => 'shower-head', 'category' => 'comfort'],
            ['name' => 'Refrigerator', 'icon' => 'refrigerator', 'category' => 'comfort'],
            ['name' => 'Near Eatery/Carinderia', 'icon' => 'utensils', 'category' => 'basic'],
            ['name' => 'Furnished', 'icon' => 'sofa', 'category' => 'comfort'],
            ['name' => 'Cable TV', 'icon' => 'tv', 'category' => 'entertainment'],
            ['name' => 'Generator Backup', 'icon' => 'battery', 'category' => 'basic']
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                [
                    'icon' => $amenity['icon'],
                    'category' => $amenity['category'],
                    'is_active' => true
                ]
            );
        }
    }
}