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
        Schema::create('suspensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('suspended_by')->constrained('users')->onDelete('cascade');
            $table->enum('duration_type', ['1_day', '3_days', 'permanent'])->default('1_day');
            $table->timestamp('suspended_at');
            $table->timestamp('expires_at')->nullable();
            $table->text('reason');
            $table->text('admin_notes')->nullable();
            $table->integer('warning_number')->default(1); // 1st, 2nd, 3rd warning
            $table->boolean('is_active')->default(true);
            $table->timestamp('lifted_at')->nullable();
            $table->foreignId('lifted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });

        // Add suspension fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('is_verified');
            $table->timestamp('suspended_until')->nullable()->after('is_suspended');
            $table->integer('suspension_count')->default(0)->after('suspended_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_suspended', 'suspended_until', 'suspension_count']);
        });

        Schema::dropIfExists('suspensions');
    }
};
