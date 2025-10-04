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
        Schema::table('property_images', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('image_path');
        });

        Schema::table('room_images', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->after('image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_images', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });

        Schema::table('room_images', function (Blueprint $table) {
            $table->dropColumn('cloudinary_public_id');
        });
    }
};
