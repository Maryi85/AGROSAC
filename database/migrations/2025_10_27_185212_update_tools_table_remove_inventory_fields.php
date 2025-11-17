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
        Schema::table('tools', function (Blueprint $table) {
            // Remover campos de inventario que ahora se manejan en tool_entries
            $table->dropColumn([
                'total_entries',
                'available_qty', 
                'damaged_qty',
                'lost_qty',
                'purchase_price',
                'purchase_date',
                'supplier'
            ]);
            
            // Mantener solo los campos básicos de la herramienta
            // name, category, status, description, brand, model, serial_number ya existen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // Restaurar campos de inventario
            $table->integer('total_entries')->default(0);
            $table->integer('available_qty')->default(0);
            $table->integer('damaged_qty')->default(0);
            $table->integer('lost_qty')->default(0);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('supplier')->nullable();
        });
    }
};