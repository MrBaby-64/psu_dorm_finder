<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // tenant
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->date('preferred_date');
            $table->time('preferred_time');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed, no_show
            $table->text('notes')->nullable();
            $table->text('landlord_response')->nullable();
            $table->date('confirmed_date')->nullable();
            $table->time('confirmed_time')->nullable();
            $table->string('cancelled_by')->nullable(); // tenant, landlord
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['preferred_date', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_visits');
    }
};