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
        Schema::create('landlord_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade'); // Tenant who reports
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade'); // Landlord being reported
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null'); // Related property
            $table->string('reason'); // Report category
            $table->text('description'); // Detailed description
            $table->string('status')->default('pending'); // pending, reviewed, resolved, dismissed
            $table->text('admin_notes')->nullable(); // Admin's response/notes
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null'); // Admin who reviewed
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landlord_reports');
    }
};
