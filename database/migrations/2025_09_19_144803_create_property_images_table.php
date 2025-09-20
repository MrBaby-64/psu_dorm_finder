<?php
// database/migrations/xxxx_xx_xx_create_property_images_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('url'); // Image file path/URL
            $table->string('alt')->nullable(); // Alt text for accessibility
            $table->boolean('is_cover')->default(false); // Cover image for listings
            $table->integer('sort_order')->default(0); // Display order
            $table->timestamps();

            $table->index(['property_id']);
            $table->index(['is_cover']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};