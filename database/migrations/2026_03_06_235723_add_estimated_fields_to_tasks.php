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
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('estimated_hours', 8, 2)->default(0);
            $table->decimal('estimated_total_payment', 12, 2)->default(0);
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn(['estimated_hours', 'estimated_total_payment', 'creator_id']);
        });
    }
};
