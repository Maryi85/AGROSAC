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
        Schema::table('supply_consumptions', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('supply_consumptions'))->pluck('name');
            if (!$indexes->contains('supply_consumptions_used_at_index')) {
                $table->index('used_at');
            }
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('ledger_entries'))->pluck('name');
            if (!$indexes->contains('ledger_entries_occurred_at_index')) {
                $table->index('occurred_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supply_consumptions', function (Blueprint $table) {
            $table->dropIndex(['used_at']);
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropIndex(['occurred_at']);
        });
    }
};
