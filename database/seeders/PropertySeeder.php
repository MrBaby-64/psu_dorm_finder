<?php
// database/seeders/PropertySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyImage;
use App\Models\User;
use App\Models\Amenity;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Message;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $landlords = User::where('role', User::ROLE_LANDLORD)->get();
        $tenants = User::where('role', User::ROLE_TENANT)->get();
        
        // Properties near PSU Bacolor Campus (14.6692, 120.6306)
        $bacolorProperties = [
            [
                'title' => 'Cozy Student Apartment near PSU Bacolor',
                'location_text' => '5 minutes walk to PSU Bacolor Campus',
                'address_line' => '123 Jose Rizal Street',
                'barangay' => 'Cabambangan',
                'city' => 'Bacolor',
                'lat' => 14.6705,
                'lng' => 120.6298,
                'price' => 3500,
                'room_count' => 2,
                'description' => 'Perfect for PSU students! This cozy 2-bedroom apartment is just a 5-minute walk from PSU Bacolor campus. Features include shared kitchen, study area, and 24/7 security. Ideal for students who want convenience and safety.',
                'amenities' => ['Wi-Fi', 'Study Area', '24/7 Security', 'Kitchen Access', 'Water Included']
            ],
            [
                'title' => 'Budget-Friendly Dormitory - PSU Bacolor',
                'location_text' => 'Walking distance to university',
                'address_line' => '456 Maharlika Highway',
                'barangay' => 'San Vicente',
                'city' => 'Bacolor',
                'lat' => 14.6681,
                'lng' => 120.6315,
                'price' => 2800,
                'room_count' => 4,
                'description' => 'Affordable shared dormitory perfect for budget-conscious students. Clean facilities, friendly environment, and very close to PSU Bacolor. Includes bed, study desk, and storage space.',
                'amenities' => ['Wi-Fi', 'Laundry', 'Study Area', 'Near Jeepney Route', 'CCTV Surveillance']
            ],
            [
                'title' => 'Modern Studio Unit - Bacolor Center',
                'location_text' => '10 minutes to PSU Bacolor',
                'address_line' => '789 MacArthur Highway',
                'barangay' => 'Cabetican',
                'city' => 'Bacolor',
                'lat' => 14.6710,
                'lng' => 120.6290,
                'price' => 5000,
                'room_count' => 1,
                'description' => 'Fully furnished modern studio unit with air conditioning, private bathroom, and kitchenette. Perfect for students who prefer privacy and modern amenities.',
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Kitchen Access', 'Furnished', 'Hot Shower', 'CCTV Surveillance']
            ]
        ];

        // Properties near PSU San Fernando Campus (15.0394, 120.6887)
        $sanFernandoProperties = [
            [
                'title' => 'Premium Student Housing - San Fernando',
                'location_text' => 'Near PSU San Fernando Campus',
                'address_line' => '321 Lazatin Boulevard',
                'barangay' => 'Del Rosario',
                'city' => 'San Fernando',
                'lat' => 15.0381,
                'lng' => 120.6901,
                'price' => 4200,
                'room_count' => 3,
                'description' => 'Premium student accommodation with excellent facilities. Features spacious rooms, common areas, and modern amenities. Close to restaurants and shopping centers.',
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Study Area', 'Common Room', 'Parking Space', '24/7 Security']
            ],
            [
                'title' => 'Affordable Bedspacer - San Fernando',
                'location_text' => '15 minutes walk to PSU San Fernando',
                'address_line' => '654 Sindalan Road',
                'barangay' => 'Sindalan',
                'city' => 'San Fernando',
                'lat' => 15.0405,
                'lng' => 120.6875,
                'price' => 2500,
                'room_count' => 6,
                'description' => 'Clean and affordable bedspacer accommodation. Shared facilities with other students. Great location with easy access to transportation and local eateries.',
                'amenities' => ['Wi-Fi', 'Laundry', 'Near Jeepney Route', 'Near Convenience Store', 'Water Included']
            ],
            [
                'title' => 'Family-Style Boarding House',
                'location_text' => 'Quiet area near PSU San Fernando',
                'address_line' => '987 Santos Street',
                'barangay' => 'Telabastagan',
                'city' => 'San Fernando',
                'lat' => 15.0410,
                'lng' => 120.6895,
                'price' => 3800,
                'room_count' => 4,
                'description' => 'Family-operated boarding house with home-cooked meals available. Quiet residential area perfect for studying. Motherly care and guidance for students away from home.',
                'amenities' => ['Wi-Fi', 'Kitchen Access', 'Study Area', 'Near Eatery/Carinderia', 'CCTV Surveillance', 'Common Room']
            ]
        ];

        // Additional properties for variety
        $additionalProperties = [
            [
                'title' => 'Executive Studio - Bacolor Business District',
                'location_text' => 'Near government offices and PSU',
                'address_line' => '111 Government Center Road',
                'barangay' => 'Cabambangan',
                'city' => 'Bacolor',
                'lat' => 14.6725,
                'lng' => 120.6285,
                'price' => 6500,
                'room_count' => 1,
                'description' => 'Upscale studio unit perfect for graduate students or working students. Located in the business district with easy access to government offices and commercial establishments.',
                'amenities' => ['Wi-Fi', 'Air Conditioning', 'Furnished', 'Parking Space', 'Cable TV', 'Hot Shower']
            ],
            [
                'title' => 'Simple Bedspacer - Budget Option',
                'location_text' => '20 minutes to PSU Bacolor via jeep',
                'address_line' => '222 Rural Road',
                'barangay' => 'Calibutbut',
                'city' => 'Bacolor',
                'lat' => 14.6650,
                'lng' => 120.6340,
                'price' => 2200,
                'room_count' => 8,
                'description' => 'Most affordable option for students on tight budget. Basic but clean accommodation. Regular jeepney service to university.',
                'amenities' => ['Wi-Fi', 'Near Jeepney Route', 'Water Included', 'Laundry']
            ]
        ];

        // Combine all properties
        $allProperties = array_merge($bacolorProperties, $sanFernandoProperties, $additionalProperties);

        foreach ($allProperties as $index => $propertyData) {
            $landlord = $landlords->random();
            
            // Create property
            $property = Property::create([
                'user_id' => $landlord->id,
                'title' => $propertyData['title'],
                'description' => $propertyData['description'],
                'location_text' => $propertyData['location_text'],
                'address_line' => $propertyData['address_line'],
                'barangay' => $propertyData['barangay'],
                'city' => $propertyData['city'],
                'province' => 'Pampanga',
                'latitude' => $propertyData['lat'],
                'longitude' => $propertyData['lng'],
                'price' => $propertyData['price'],
                'room_count' => $propertyData['room_count'],
                'approval_status' => 'approved', // Most approved for demo
                'is_verified' => $landlord->is_verified,
                'is_featured' => $index < 3, // First 3 are featured
                'display_priority' => $index < 3 ? (10 - $index) : 0,
            ]);

            // Create rooms
            for ($i = 1; $i <= $property->room_count; $i++) {
                Room::create([
                    'property_id' => $property->id,
                    'name' => $property->room_count == 1 ? 'Main Room' : "Room {$i}",
                    'capacity' => rand(1, 2),
                    'price' => $property->price,
                    'is_available' => rand(0, 1) ? true : false,
                ]);
            }

            // Skip creating property images for now - they don't exist
            // PropertyImage::create([...])
            
            // We'll add images later when we set up proper image upload

            // Attach amenities
            $amenityNames = $propertyData['amenities'];
            $amenities = Amenity::whereIn('name', $amenityNames)->pluck('id');
            $property->amenities()->attach($amenities);

            // Create some reviews (ensure unique user-property combinations)
            $reviewCount = rand(1, min(4, $tenants->count()));
            $reviewTenants = $tenants->random($reviewCount);
            
            foreach ($reviewTenants as $tenant) {
                Review::create([
                    'property_id' => $property->id,
                    'user_id' => $tenant->id,
                    'rating' => rand(3, 5),
                    'comment' => $this->getRandomReviewComment(),
                ]);
            }

            // Update rating cache
            $property->updateRatingCache();
        }

        // Create some sample bookings and messages
        $properties = Property::all();
        
        foreach ($properties->take(5) as $property) {
            // Sample booking
            Booking::create([
                'property_id' => $property->id,
                'tenant_id' => $tenants->random()->id,
                'room_id' => $property->rooms->random()->id,
                'check_in' => now()->addDays(rand(1, 30)),
                'check_out' => now()->addDays(rand(31, 365)),
                'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
            ]);

            // Sample message
            Message::create([
                'sender_id' => $tenants->random()->id,
                'receiver_id' => $property->landlord->id,
                'property_id' => $property->id,
                'body' => "Hi! I'm interested in your property. Is it still available for the next semester?",
            ]);
        }
    }

    private function getRandomReviewComment(): string
    {
        $comments = [
            'Great place to stay! Very convenient location and friendly landlord.',
            'Clean facilities and good Wi-Fi. Highly recommended for PSU students.',
            'Budget-friendly and close to campus. Perfect for students.',
            'Nice and quiet place. Good for studying.',
            'Excellent accommodation with modern amenities.',
            'Affordable and accessible. Great value for money.',
            'Safe and secure environment. Good for female students.',
            'Helpful landlord and well-maintained facilities.'
        ];

        return $comments[array_rand($comments)];
    }
}