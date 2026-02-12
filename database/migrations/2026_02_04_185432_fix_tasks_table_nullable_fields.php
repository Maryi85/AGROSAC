<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, actualizar todos los valores NULL existentes a 0
        DB::table('tasks')->whereNull('hours')->update(['hours' => 0]);
        DB::table('tasks')->whereNull('kilos')->update(['kilos' => 0]);
        DB::table('tasks')->whereNull('total_payment')->update(['total_payment' => 0]);
        
        // Ahora modificar las columnas para que NO permitan NULL y tengan default 0
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('hours', 8, 2)->default(0)->nullable(false)->change();
            $table->decimal('kilos', 12, 3)->default(0)->nullable(false)->change();
            $table->decimal('total_payment', 12, 2)->default(0)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('hours', 8, 2)->nullable()->change();
            $table->decimal('kilos', 12, 3)->nullable()->change();
            $table->decimal('total_payment', 12, 2)->nullable()->change();
        });
    }
};
