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
        Schema::create('supply_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('restrict');
            $table->enum('type', ['entry', 'exit']);
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->onDelete('restrict');
            $table->foreignId('plot_id')->nullable()->constrained('plots')->onDelete('restrict');
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('restrict');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('movement_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_movements');
    }
};