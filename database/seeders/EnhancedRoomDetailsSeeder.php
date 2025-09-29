<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Property;

class EnhancedRoomDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing rooms and populate with enhanced details
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->info('No existing rooms found. Creating sample data first...');

            // Create a sample property if none exists
            $property = Property::first();
            if (!$property) {
                $this->command->error('No properties found. Please create properties first.');
                return;
            }

            // Create sample rooms
            $this->createSampleRooms($property);
            $rooms = Room::all();
        }

        foreach ($rooms as $room) {
            $this->populateEnhancedDetails($room);
        }

        $this->command->info('Enhanced room details populated successfully!');
    }

    private function createSampleRooms($property)
    {
        $sampleRooms = [
            [
                'room_number' => '101',
                'room_type' => 'single',
                'price' => 8000,
                'size_sqm' => 12.5,
                'capacity' => 1,
                'occupied_count' => 0,
                'status' => 'available',
                'description' => 'Cozy single room perfect for students. Well-lit with natural lighting.',
            ],
            [
                'room_number' => '102',
                'room_type' => 'shared',
                'price' => 5500,
                'size_sqm' => 18.0,
                'capacity' => 2,
                'occupied_count' => 1,
                'status' => 'available',
                'description' => 'Spacious shared room with twin beds and study area.',
            ],
            [
                'room_number' => '201',
                'room_type' => 'studio',
                'price' => 12000,
                'size_sqm' => 25.0,
                'capacity' => 1,
                'occupied_count' => 1,
                'status' => 'occupied',
                'description' => 'Modern studio with kitchenette and private bathroom.',
            ],
        ];

        foreach ($sampleRooms as $roomData) {
            Room::create(array_merge($roomData, ['property_id' => $property->id]));
        }
    }

    private function populateEnhancedDetails($room)
    {
        // Randomize enhanced details to create realistic variety
        $updates = [
            // Physical Details
            'furnished_status' => $this->randomChoice(['furnished', 'semi_furnished', 'unfurnished'], [50, 30, 20]),
            'bathroom_type' => $this->randomChoice(['private', 'shared', 'communal'], [30, 60, 10]),
            'has_balcony' => $this->randomBool(25), // 25% chance
            'window_count' => rand(1, 3),
            'flooring_type' => $this->randomChoice(['tile', 'wood', 'concrete', 'vinyl'], [40, 20, 30, 10]),
            'ceiling_height' => round(rand(250, 350) / 100, 2), // 2.5m to 3.5m

            // Utilities & Features
            'ac_type' => $this->randomChoice(['central', 'window', 'split', 'ceiling_fan', 'none'], [10, 25, 35, 25, 5]),
            'internet_speed_mbps' => $this->randomChoice([25, 50, 100, 200], [20, 40, 30, 10]),
            'electrical_outlets' => rand(2, 6),
            'storage_space' => $this->randomChoice(['closet', 'wardrobe', 'built_in', 'none'], [30, 40, 20, 10]),
            'has_kitchenette' => $this->randomBool($room->room_type === 'studio' ? 80 : 15),
            'has_refrigerator' => $this->randomBool(60),
            'has_study_desk' => $this->randomBool(85),

            // Safety & Security
            'has_smoke_detector' => $this->randomBool(70),
            'has_security_camera' => $this->randomBool(30),
            'has_window_grills' => $this->randomBool(75),
            'emergency_exit_access' => $this->randomBool(90),

            // Accessibility
            'wheelchair_accessible' => $this->randomBool(15),
            'is_ground_floor' => $this->randomBool(30),
            'elevator_access' => $this->randomBool(60),
            'floor_level' => rand(1, 4),

            // Maintenance & Condition
            'last_renovated' => $this->randomDate('2020-01-01', '2024-12-31'),
            'condition_rating' => round(rand(35, 50) / 10, 1), // 3.5 to 5.0
            'maintenance_notes' => $this->randomChoice([
                'Recently renovated with new paint and fixtures',
                'Well-maintained with regular cleaning',
                'Minor wear but fully functional',
                null
            ], [25, 50, 20, 5]),
            'last_inspection' => $this->randomDate('2024-01-01', '2024-12-31'),

            // Booking & Policies
            'minimum_stay_months' => $this->randomChoice([1, 3, 6, 12], [40, 30, 20, 10]),
            'maximum_stay_months' => $this->randomChoice([null, 12, 24], [30, 50, 20]),
            'security_deposit' => $room->price * $this->randomChoice([1, 1.5, 2], [60, 30, 10]),
            'advance_payment_months' => $this->randomChoice([1, 2, 3], [60, 30, 10]),
            'pets_allowed' => $this->randomBool(20),
            'smoking_allowed' => $this->randomBool(10),
            'house_rules' => $this->randomChoice([
                'No loud music after 10 PM. Keep common areas clean.',
                'Guests allowed until 9 PM only. No overnight visitors.',
                'Quiet hours from 10 PM to 7 AM. No cooking in rooms.',
                null
            ], [30, 25, 25, 20]),

            // Additional Details
            'view_description' => $this->randomChoice(['city', 'garden', 'courtyard', 'street', 'parking'], [15, 25, 20, 30, 10]),
            'included_utilities' => $this->randomChoice([
                ['electricity', 'water'],
                ['electricity', 'water', 'internet'],
                ['electricity', 'water', 'internet', 'cable_tv'],
                ['water']
            ], [20, 50, 25, 5]),
            'special_features' => $this->randomChoice([
                'Large windows with excellent natural light',
                'Built-in study nook with shelving',
                'Recently renovated with modern amenities',
                'Corner room with extra space',
                null
            ], [20, 15, 20, 15, 30]),
            'room_orientation' => $this->randomChoice(['north', 'south', 'east', 'west', 'northeast', 'northwest', 'southeast', 'southwest'], [12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5, 12.5]),
        ];

        $room->update($updates);

        $this->command->info("Updated room {$room->room_number} with enhanced details");
    }

    private function randomChoice(array $options, array $weights = null)
    {
        if ($weights === null) {
            return $options[array_rand($options)];
        }

        $rand = rand(1, array_sum($weights));
        $currentWeight = 0;

        foreach ($options as $index => $option) {
            $currentWeight += $weights[$index];
            if ($rand <= $currentWeight) {
                return $option;
            }
        }

        return $options[0];
    }

    private function randomBool(int $truePercentage = 50): bool
    {
        return rand(1, 100) <= $truePercentage;
    }

    private function randomDate(string $start, string $end): string
    {
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);
        $randomTimestamp = rand($startTimestamp, $endTimestamp);

        return date('Y-m-d', $randomTimestamp);
    }
}
