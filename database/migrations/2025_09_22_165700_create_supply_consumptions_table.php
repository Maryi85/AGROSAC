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
        Schema::create('supply_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->cascadeOnDelete();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained('plots')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->decimal('qty', 12, 3);
            $table->decimal('total_cost', 14, 2);
            $table->date('used_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_consumptions');
    }
};
