<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_browse', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('sort'); // e.g., 'nearest', 'price_asc'
            $table->text('property_ids_json'); // JSON array of property IDs in result
            $table->string('campus_origin')->nullable(); // 'bacolor' or 'san_fernando'
            $table->timestamps();

            $table->index(['session_id']);
            $table->index(['sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_browse');
    }
};