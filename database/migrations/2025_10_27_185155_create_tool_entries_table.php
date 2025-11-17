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
            $table->enum('type', ['purchase', 'donation', 'transfer', 'repair']); // Tipo de entrada
            $table->decimal('unit_cost', 10, 2)->nullable(); // Costo unitario
            $table->decimal('total_cost', 12, 2)->nullable(); // Costo total
            $table->date('entry_date'); // Fecha de entrada
            $table->string('supplier')->nullable(); // Proveedor
            $table->string('invoice_number')->nullable(); // Número de factura
            $table->text('notes')->nullable(); // Notas adicionales
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