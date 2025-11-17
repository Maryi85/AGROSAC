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
            // Renombrar columnas existentes para mayor claridad
            $table->renameColumn('total_qty', 'total_entries'); // Total de entradas al sistema
            $table->renameColumn('available_qty', 'available_qty'); // Disponibles para préstamo
            
            // Agregar nuevas columnas para el control de inventario
            $table->integer('damaged_qty')->default(0)->after('available_qty'); // Dañadas
            $table->integer('lost_qty')->default(0)->after('damaged_qty'); // Perdidas
            
            // Agregar campos adicionales
            $table->text('description')->nullable()->after('lost_qty');
            $table->string('brand')->nullable()->after('description');
            $table->string('model')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model');
            $table->decimal('purchase_price', 10, 2)->nullable()->after('serial_number');
            $table->date('purchase_date')->nullable()->after('purchase_price');
            $table->string('supplier')->nullable()->after('purchase_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->renameColumn('total_entries', 'total_qty');
            $table->dropColumn([
                'damaged_qty', 
                'lost_qty', 
                'description', 
                'brand', 
                'model', 
                'serial_number', 
                'purchase_price', 
                'purchase_date', 
                'supplier'
            ]);
        });
    }
};