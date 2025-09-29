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
        if (Schema::hasTable('loans')) {
            Schema::drop('loans');
        }

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestampTz('out_at')->useCurrent();
            $table->timestampTz('due_at')->nullable();
            $table->timestampTz('returned_at')->nullable();
            $table->string('condition_return')->nullable();
            $table->string('status')->default('out')->index(); // out, returned, lost, damaged
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
