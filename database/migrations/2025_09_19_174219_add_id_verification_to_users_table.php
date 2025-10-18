<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('valid_id_path')->nullable()->after('is_verified');
            $table->enum('id_verification_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('valid_id_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['valid_id_path', 'id_verification_status']);
        });
    }
};