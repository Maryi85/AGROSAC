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
        Schema::create('crop_tracking', function (Blueprint $table) {
            $table->id();
            $table->date('tracking_date');
            $table->foreignId('plot_id')->constrained()->onDelete('cascade');
            $table->foreignId('crop_id')->constrained()->onDelete('cascade');
            $table->string('process_type');
            $table->text('description')->nullable();
            $table->text('observations')->nullable();
            $table->date('harvest_date')->nullable();
            $table->date('final_date')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit', 20)->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['crop_id', 'tracking_date']);
            $table->index(['plot_id', 'tracking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_tracking');
    }
};
