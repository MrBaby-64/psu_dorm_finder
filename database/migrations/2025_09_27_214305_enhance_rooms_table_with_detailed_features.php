<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Physical Details
            $table->string('furnished_status')->default('unfurnished'); // furnished, semi_furnished, unfurnished
            $table->string('bathroom_type')->default('shared'); // private, shared, communal
            $table->boolean('has_balcony')->default(false);
            $table->integer('window_count')->default(1);
            $table->string('flooring_type')->nullable(); // tile, wood, concrete, carpet
            $table->decimal('ceiling_height', 3, 2)->nullable(); // meters

            // Utilities & Features
            $table->string('ac_type')->nullable(); // central, window, split, ceiling_fan, none
            $table->integer('internet_speed_mbps')->nullable();
            $table->integer('electrical_outlets')->default(2);
            $table->string('storage_space')->nullable(); // closet, wardrobe, built_in, none
            $table->boolean('has_kitchenette')->default(false);
            $table->boolean('has_refrigerator')->default(false);
            $table->boolean('has_study_desk')->default(false);

            // Safety & Security
            $table->boolean('has_smoke_detector')->default(false);
            $table->boolean('has_security_camera')->default(false);
            $table->boolean('has_window_grills')->default(false);
            $table->boolean('emergency_exit_access')->default(true);

            // Accessibility
            $table->boolean('wheelchair_accessible')->default(false);
            $table->boolean('is_ground_floor')->default(false);
            $table->boolean('elevator_access')->default(false);
            $table->integer('floor_level')->default(1);

            // Maintenance & Condition
            $table->date('last_renovated')->nullable();
            $table->decimal('condition_rating', 2, 1)->default(5.0); // 1.0 to 5.0
            $table->text('maintenance_notes')->nullable();
            $table->date('last_inspection')->nullable();

            // Booking & Policies
            $table->integer('minimum_stay_months')->default(1);
            $table->integer('maximum_stay_months')->nullable();
            $table->decimal('security_deposit', 10, 2)->nullable();
            $table->decimal('advance_payment_months', 2, 1)->default(1.0);
            $table->boolean('pets_allowed')->default(false);
            $table->boolean('smoking_allowed')->default(false);
            $table->text('house_rules')->nullable();

            // Additional Details
            $table->string('view_description')->nullable(); // city, garden, courtyard, street
            $table->json('included_utilities')->nullable(); // electricity, water, internet, etc.
            $table->text('special_features')->nullable();
            $table->string('room_orientation')->nullable(); // north, south, east, west

            // Add indexes for performance
            $table->index(['furnished_status', 'status']);
            $table->index(['bathroom_type', 'status']);
            $table->index(['price', 'status']);
            $table->index(['floor_level', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Physical Details
            $table->dropColumn([
                'furnished_status', 'bathroom_type', 'has_balcony', 'window_count',
                'flooring_type', 'ceiling_height'
            ]);

            // Utilities & Features
            $table->dropColumn([
                'ac_type', 'internet_speed_mbps', 'electrical_outlets', 'storage_space',
                'has_kitchenette', 'has_refrigerator', 'has_study_desk'
            ]);

            // Safety & Security
            $table->dropColumn([
                'has_smoke_detector', 'has_security_camera', 'has_window_grills',
                'emergency_exit_access'
            ]);

            // Accessibility
            $table->dropColumn([
                'wheelchair_accessible', 'is_ground_floor', 'elevator_access',
                'floor_level'
            ]);

            // Maintenance & Condition
            $table->dropColumn([
                'last_renovated', 'condition_rating', 'maintenance_notes',
                'last_inspection'
            ]);

            // Booking & Policies
            $table->dropColumn([
                'minimum_stay_months', 'maximum_stay_months', 'security_deposit',
                'advance_payment_months', 'pets_allowed', 'smoking_allowed',
                'house_rules'
            ]);

            // Additional Details
            $table->dropColumn([
                'view_description', 'included_utilities', 'special_features',
                'room_orientation'
            ]);
        });
    }
};
