<?php
// database/migrations/xxxx_xx_xx_create_amenity_property_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenity_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['amenity_id', 'property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_property');
    }
};