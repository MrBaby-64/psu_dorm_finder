<?php
// database/migrations/xxxx_xx_xx_update_users_table_for_psu_dorm.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'landlord', 'tenant'])->default('tenant')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->boolean('is_verified')->default(false)->after('phone');
            
            // Add indexes for performance
            $table->index('role');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_verified']);
            $table->dropColumn(['role', 'phone', 'is_verified']);
        });
    }
};