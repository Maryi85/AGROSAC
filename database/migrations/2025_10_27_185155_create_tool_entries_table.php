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
        Schema::create('tool_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->onDelete('cascade');
            $table->integer('quantity'); // Cantidad de la entrada
            $table->enum('type', ['purchase', 'donation', 'transfer', 'repair', 'damage', 'loss']); // Tipo de entrada
            $table->decimal('unit_cost', 10, 2)->nullable(); // Costo unitario
            $table->decimal('total_cost', 12, 2)->nullable(); // Costo total
            $table->date('entry_date'); // Fecha de entrada
            $table->string('supplier')->nullable(); // Proveedor
            $table->string('invoice_number')->nullable(); // Número de factura
            $table->text('notes')->nullable(); // Notas adicionales
            
            // Campos de daño y pérdida
            $table->integer('damaged_qty')->default(0);
            $table->integer('lost_qty')->default(0);
            $table->integer('available_qty')->default(0);
            $table->text('damage_notes')->nullable();
            $table->text('loss_notes')->nullable();
            $table->date('damage_date')->nullable();
            $table->date('loss_date')->nullable();
            $table->string('damage_photo')->nullable();

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['tool_id', 'entry_date']);
            $table->index(['type', 'entry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_entries');
    }
};