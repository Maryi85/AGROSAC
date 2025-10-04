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
        Schema::table('crops', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('variety')->nullable()->after('description');
            $table->string('planting_season')->nullable()->after('variety');
            $table->integer('harvest_time')->nullable()->after('planting_season'); // días
            $table->decimal('yield_per_hectare', 10, 2)->nullable()->after('harvest_time'); // kg/ha
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crops', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'variety',
                'planting_season',
                'harvest_time',
                'yield_per_hectare'
            ]);
        });
    }
};