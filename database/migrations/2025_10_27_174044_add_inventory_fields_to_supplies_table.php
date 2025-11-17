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
        Schema::table('supplies', function (Blueprint $table) {
            $table->decimal('current_stock', 12, 3)->default(0)->after('unit_cost');
            $table->decimal('min_stock', 12, 3)->default(0)->after('current_stock');
            $table->string('category')->nullable()->after('min_stock');
            $table->text('description')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplies', function (Blueprint $table) {
            $table->dropColumn(['current_stock', 'min_stock', 'category', 'description']);
        });
    }
};
