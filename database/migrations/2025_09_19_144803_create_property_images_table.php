<?php

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
            $table->string('image_path'); // Path to the image file
            $table->string('alt_text')->nullable(); // Alt text for accessibility
            $table->boolean('is_cover')->default(false); // Is this the cover/main image
            $table->integer('sort_order')->default(0); // For ordering images
            $table->timestamps();

            // Indexes for performance
            $table->index(['property_id', 'is_cover']);
            $table->index(['property_id', 'sort_order']);
            $table->index('is_cover');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};