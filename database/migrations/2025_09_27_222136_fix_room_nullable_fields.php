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
        Schema::table('rooms', function (Blueprint $table) {
            // Make these boolean fields nullable so landlords don't have to specify them
            $table->boolean('pets_allowed')->nullable()->change();
            $table->boolean('smoking_allowed')->nullable()->change();
            $table->boolean('has_refrigerator')->nullable()->change();
            $table->boolean('has_study_desk')->nullable()->change();
            $table->boolean('has_kitchenette')->nullable()->change();
            $table->boolean('has_balcony')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Revert back to not nullable with defaults
            $table->boolean('pets_allowed')->default(false)->change();
            $table->boolean('smoking_allowed')->default(false)->change();
            $table->boolean('has_refrigerator')->default(false)->change();
            $table->boolean('has_study_desk')->default(false)->change();
            $table->boolean('has_kitchenette')->default(false)->change();
            $table->boolean('has_balcony')->default(false)->change();
        });
    }
};
