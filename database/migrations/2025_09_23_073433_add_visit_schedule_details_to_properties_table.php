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
        Schema::table('properties', function (Blueprint $table) {
            $table->json('visit_days')->nullable()->after('visit_schedule_enabled');
            $table->time('visit_time_start')->nullable()->after('visit_days');
            $table->time('visit_time_end')->nullable()->after('visit_time_start');
            $table->integer('visit_duration')->nullable()->after('visit_time_end');
            $table->text('visit_instructions')->nullable()->after('visit_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'visit_days',
                'visit_time_start',
                'visit_time_end',
                'visit_duration',
                'visit_instructions'
            ]);
        });
    }
};
