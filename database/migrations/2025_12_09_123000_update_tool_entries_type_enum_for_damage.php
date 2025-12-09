<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Permitir nuevos tipos damage y loss sin requerir doctrine/dbal
        DB::statement("
            ALTER TABLE tool_entries
            MODIFY COLUMN type ENUM('purchase','donation','transfer','repair','damage','loss') NOT NULL
        ");
    }

    public function down(): void
    {
        // Volver al enum original
        DB::statement("
            ALTER TABLE tool_entries
            MODIFY COLUMN type ENUM('purchase','donation','transfer','repair') NOT NULL
        ");
    }
};

