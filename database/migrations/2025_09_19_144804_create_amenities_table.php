<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // WiFi, Pool, Gym, etc.
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('category')->default('basic'); // basic, comfort, entertainment, security, facilities
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['category', 'is_active']);
            $table->index('is_active');
            
            // Ensure unique amenity names
            $table->unique('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};