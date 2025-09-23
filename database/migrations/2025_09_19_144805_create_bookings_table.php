<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // tenant
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            
            // Dates
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            
            // Pricing
            $table->decimal('total_amount', 10, 2);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('monthly_rent', 10, 2);
            
            // Status
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled, active, completed
            $table->string('payment_status')->default('pending'); // pending, partial, paid, overdue, refunded
            
            // Notes
            $table->text('notes')->nullable(); // Tenant notes/requests
            $table->text('landlord_notes')->nullable(); // Landlord notes/response
            
            // Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Cancellation tracking
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['property_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
            $table->index('check_in_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};