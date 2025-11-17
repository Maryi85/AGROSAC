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
        Schema::table('tool_entries', function (Blueprint $table) {
            // Agregar campos para daño y pérdida
            $table->integer('damaged_qty')->default(0)->after('quantity');
            $table->integer('lost_qty')->default(0)->after('damaged_qty');
            $table->integer('available_qty')->default(0)->after('lost_qty');
            $table->text('damage_notes')->nullable()->after('notes');
            $table->text('loss_notes')->nullable()->after('damage_notes');
            $table->date('damage_date')->nullable()->after('loss_notes');
            $table->date('loss_date')->nullable()->after('damage_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tool_entries', function (Blueprint $table) {
            $table->dropColumn([
                'damaged_qty',
                'lost_qty', 
                'available_qty',
                'damage_notes',
                'loss_notes',
                'damage_date',
                'loss_date'
            ]);
        });
    }
};