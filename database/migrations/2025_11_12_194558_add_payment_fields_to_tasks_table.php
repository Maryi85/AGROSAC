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
        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('price_per_hour', 10, 2)->nullable()->after('kilos');
            $table->decimal('price_per_day', 10, 2)->nullable()->after('price_per_hour');
            $table->decimal('price_per_kg', 10, 2)->nullable()->after('price_per_day');
            $table->decimal('total_payment', 12, 2)->default(0)->after('price_per_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['price_per_hour', 'price_per_day', 'price_per_kg', 'total_payment']);
        });
    }
};
