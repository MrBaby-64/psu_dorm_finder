<?php
// database/migrations/xxxx_xx_xx_create_rooms_table.php

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
            $table->string('name'); // e.g., "Room A", "Master Bedroom"
            $table->integer('capacity')->default(1); // How many people can stay
            $table->decimal('price', 10, 2); // Room-specific pricing if different from property
            $table->boolean('is_available')->default(true);
            $table->text('notes')->nullable(); // Room-specific notes
            $table->timestamps();

            $table->index(['property_id']);
            $table->index(['is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};