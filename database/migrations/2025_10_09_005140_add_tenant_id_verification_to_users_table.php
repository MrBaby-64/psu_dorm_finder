<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns for tenant ID verification
        if (!Schema::hasColumn('users', 'tenant_id_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('tenant_id_path')->nullable()->after('property_documents_path');
            });
        }

        if (!Schema::hasColumn('users', 'tenant_id_verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('tenant_id_verification_status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('tenant_id_path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tenant_id_path', 'tenant_id_verification_status']);
        });
    }
};
