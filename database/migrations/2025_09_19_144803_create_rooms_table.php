<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('room_number'); // 101, A-1, Room 1, etc.
            $table->string('room_type'); // single, shared, studio, one_bedroom, bedspace
            $table->decimal('price', 10, 2); // Monthly rent for this room
            $table->decimal('size_sqm', 8, 1)->nullable(); // Room size in square meters
            $table->integer('capacity')->default(1); // How many people can stay
            $table->string('status')->default('available'); // available, occupied, maintenance, reserved
            $table->text('description')->nullable();
            $table->json('amenities')->nullable(); // Room-specific amenities
            $table->timestamps();

            // Indexes for performance
            $table->index(['property_id', 'status']);
            $table->index(['room_type', 'status']);
            $table->index('status');
            
            // Unique constraint for room number within a property
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};