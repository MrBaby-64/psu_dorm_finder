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
        // Add new columns if they don't exist
        if (!Schema::hasColumn('users', 'property_documents_path')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('property_documents_path')->nullable()->after('is_verified');
            });
        }

        if (!Schema::hasColumn('users', 'document_verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('document_verification_status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('property_documents_path');
            });
        }

        // Copy data from old columns to new columns if old columns exist
        if (Schema::hasColumn('users', 'valid_id_path') && Schema::hasColumn('users', 'id_verification_status')) {
            DB::table('users')->whereNotNull('valid_id_path')->update([
                'property_documents_path' => DB::raw('valid_id_path')
            ]);

            DB::table('users')->update([
                'document_verification_status' => DB::raw('id_verification_status')
            ]);

            // Drop old columns
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['valid_id_path', 'id_verification_status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add old columns back
        Schema::table('users', function (Blueprint $table) {
            $table->string('valid_id_path')->nullable()->after('is_verified');
            $table->enum('id_verification_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('valid_id_path');
        });

        // Copy data back
        DB::table('users')->update([
            'valid_id_path' => DB::raw('property_documents_path'),
            'id_verification_status' => DB::raw('document_verification_status')
        ]);

        // Drop new columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['property_documents_path', 'document_verification_status']);
        });
    }
};
