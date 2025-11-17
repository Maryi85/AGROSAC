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
        Schema::table('crop_tracking', function (Blueprint $table) {
            // Eliminar campos innecesarios
            $table->dropColumn([
                'process_type',
                'description',
                'observations',
                'final_date',
                'quantity',
                'unit'
            ]);
            
            // Renombrar harvest_date a cut_date
            $table->renameColumn('harvest_date', 'cut_date');
            
            // Agregar nuevos campos
            $table->string('phase')->nullable()->after('crop_id');
            $table->text('novelties')->nullable()->after('cut_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crop_tracking', function (Blueprint $table) {
            // Restaurar campos eliminados
            $table->string('process_type')->after('crop_id');
            $table->text('description')->nullable()->after('process_type');
            $table->text('observations')->nullable()->after('description');
            $table->date('final_date')->nullable()->after('cut_date');
            $table->decimal('quantity', 10, 2)->nullable()->after('final_date');
            $table->string('unit', 20)->nullable()->after('quantity');
            
            // Renombrar cut_date a harvest_date
            $table->renameColumn('cut_date', 'harvest_date');
            
            // Eliminar nuevos campos
            $table->dropColumn(['phase', 'novelties']);
        });
    }
};
