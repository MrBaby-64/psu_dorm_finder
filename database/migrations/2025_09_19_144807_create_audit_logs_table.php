<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // e.g., 'approve_property', 'verify_landlord'
            $table->string('subject_type'); // e.g., 'App\Models\Property'
            $table->bigInteger('subject_id'); // ID of the affected record
            $table->text('meta_json')->nullable(); // Additional context data
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};