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
            $connection = DB::connection();
            $driver = $connection->getDriverName();

            if ($driver === 'pgsql') {
                // PostgreSQL specific query
                $result = $connection->select("
                    SELECT 1 FROM pg_indexes
                    WHERE tablename = ? AND indexname = ?
                ", [$table, $indexName]);
                return !empty($result);
            } else {
                // MySQL/other databases
                $schema = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $schema->listTableIndexes($table);
                return array_key_exists($indexName, $indexes);
            }
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
                try {
                    if (!$this->indexExists('users', 'users_email_index')) {
                        $table->index('email');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
                }

                try {
                    if (!$this->indexExists('users', 'users_role_index')) {
                        $table->index('role');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
                }
            });
        }

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                // Ensure proper indexes
                try {
                    if (!$this->indexExists('properties', 'properties_approval_status_index')) {
                        $table->index('approval_status');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
                }

                try {
                    if (!$this->indexExists('properties', 'properties_city_index')) {
                        $table->index('city');
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
                }

                try {
                    if (!$this->indexExists('properties', 'properties_latitude_longitude_index')) {
                        $table->index(['latitude', 'longitude']);
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
                }
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                // Ensure proper indexes for performance
                try {
                    if (!$this->indexExists('rooms', 'rooms_property_id_status_index')) {
                        $table->index(['property_id', 'status']);
                    }
                } catch (\Exception $e) {
                    // Index might already exist, continue
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
                try {
                    if ($this->indexExists('users', 'users_email_index')) {
                        $table->dropIndex(['email']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }

                try {
                    if ($this->indexExists('users', 'users_role_index')) {
                        $table->dropIndex(['role']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
            });
        }

        if (Schema::hasTable('properties')) {
            Schema::table('properties', function (Blueprint $table) {
                try {
                    if ($this->indexExists('properties', 'properties_approval_status_index')) {
                        $table->dropIndex(['approval_status']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }

                try {
                    if ($this->indexExists('properties', 'properties_city_index')) {
                        $table->dropIndex(['city']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }

                try {
                    if ($this->indexExists('properties', 'properties_latitude_longitude_index')) {
                        $table->dropIndex(['latitude', 'longitude']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
            });
        }

        if (Schema::hasTable('rooms')) {
            Schema::table('rooms', function (Blueprint $table) {
                try {
                    if ($this->indexExists('rooms', 'rooms_property_id_status_index')) {
                        $table->dropIndex(['property_id', 'status']);
                    }
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
            });
        }
    }
};