<?php
// database/migrations/xxxx_xx_xx_create_properties_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // landlord
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('location_text'); // Human readable location
            $table->string('address_line');
            $table->string('barangay');
            $table->enum('city', ['Bacolor', 'San Fernando']);
            $table->string('province')->default('Pampanga');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('price', 10, 2); // Monthly rent
            $table->integer('room_count')->default(1);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_verified')->default(false); // PSU verified
            $table->boolean('is_featured')->default(false); // Featured listing
            $table->integer('display_priority')->default(0); // Higher = more prominent
            $table->decimal('rating_avg', 3, 2)->default(0); // Cached average rating
            $table->integer('rating_count')->default(0); // Cached review count
            $table->timestamps();

            // Indexes for performance
            $table->index(['city']);
            $table->index(['approval_status']);
            $table->index(['is_featured', 'display_priority']);
            $table->index(['latitude', 'longitude']);
            $table->index(['user_id']);
            $table->index(['is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};