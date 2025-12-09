<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Rellenar available_qty para entradas existentes si está en cero
        DB::statement("
            UPDATE tool_entries
            SET available_qty = GREATEST(quantity - IFNULL(damaged_qty,0) - IFNULL(lost_qty,0), 0)
            WHERE available_qty = 0 AND quantity IS NOT NULL
        ");
    }

    public function down(): void
    {
        // No revertimos para evitar perder datos; dejar available_qty como está
    }
};

