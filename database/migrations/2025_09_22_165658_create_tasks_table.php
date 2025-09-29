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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // daily, harvest, etc.
            $table->string('description')->nullable();
            $table->foreignId('plot_id')->nullable()->constrained('plots')->nullOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->date('scheduled_for')->nullable()->index();
            $table->string('status')->default('pending')->index(); // pending, in_progress, completed, approved, rejected, invalid
            $table->decimal('hours', 8, 2)->default(0);
            $table->decimal('kilos', 12, 3)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
