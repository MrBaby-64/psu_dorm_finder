<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $schema = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $schema->listTableIndexes($table);
            return array_key_exists($indexName, $indexes);
        } catch (\Exception $e) {
            // If we can't check, assume it doesn't exist to be safe
            return false;
        }
    }

    /**
     * Run the migrations for PostgreSQL compatibility.
     */
    public function up(): void
    {
        // Enable UUID extension for PostgreSQL if needed
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');
        }

        // Update any tables that might have MySQL-specific issues
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Ensure proper constraints for PostgreSQL
                if (!$this->indexExists('users', 'users_email_index')) {
                    $table->index('email');
                }
                if (!$this->indexExists('users', 'users_role_index')) {
                    $table->index('role');
                }
            });
        }

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                // Ensure proper indexes
                if (!$this->indexExists('properties', 'properties_approval_status_index')) {
                    $table->index('approval_status');
                }
                if (!$this->indexExists('properties', 'properties_city_index')) {
                    $table->index('city');
                }
                if (!$this->indexExists('properties', 'properties_latitude_longitude_index')) {
                    $table->index(['latitude', 'longitude']);
                }
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                // Ensure proper indexes for performance
                if (!$this->indexExists('rooms', 'rooms_property_id_status_index')) {
                    $table->index(['property_id', 'status']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes if they exist
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if ($this->indexExists('users', 'users_email_index')) {
                    $table->dropIndex(['email']);
                }
                if ($this->indexExists('users', 'users_role_index')) {
                    $table->dropIndex(['role']);
                }
            });
        }

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                if ($this->indexExists('properties', 'properties_approval_status_index')) {
                    $table->dropIndex(['approval_status']);
                }
                if ($this->indexExists('properties', 'properties_city_index')) {
                    $table->dropIndex(['city']);
                }
                if ($this->indexExists('properties', 'properties_latitude_longitude_index')) {
                    $table->dropIndex(['latitude', 'longitude']);
                }
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                if ($this->indexExists('rooms', 'rooms_property_id_status_index')) {
                    $table->dropIndex(['property_id', 'status']);
                }
            });
        }
    }
};