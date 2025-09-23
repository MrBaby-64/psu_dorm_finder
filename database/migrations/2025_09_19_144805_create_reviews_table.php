<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('rating')->default(5); // Overall rating 1-5
            $table->text('comment')->nullable();
            
            // Detailed ratings
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('location_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->integer('communication_rating')->nullable();
            
            $table->boolean('is_verified')->default(false);
            $table->text('landlord_reply')->nullable();
            $table->timestamp('landlord_reply_at')->nullable();
            $table->timestamps();

            // Prevent duplicate reviews from same user for same property
            $table->unique(['user_id', 'property_id']);
            
            // Indexes for performance
            $table->index(['property_id', 'rating']);
            $table->index(['property_id', 'created_at']);
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};