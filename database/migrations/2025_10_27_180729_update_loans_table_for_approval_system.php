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
        Schema::table('loans', function (Blueprint $table) {
            // Agregar campos para el sistema de aprobación
            $table->string('status')->default('pending')->change(); // pending, approved, rejected, out, returned, lost, damaged
            $table->text('request_notes')->nullable()->after('status'); // Notas del trabajador al solicitar
            $table->text('admin_notes')->nullable()->after('request_notes'); // Notas del administrador
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('admin_notes'); // Quien aprobó
            $table->timestamp('approved_at')->nullable()->after('approved_by'); // Cuando se aprobó
            $table->foreignId('returned_by')->nullable()->constrained('users')->after('approved_at'); // Quien devolvió
            $table->timestamp('returned_at')->nullable()->change(); // Cambiar a nullable
            $table->timestamp('due_at')->nullable()->change(); // Cambiar a nullable
            $table->timestamp('out_at')->nullable()->change(); // Cambiar a nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['request_notes', 'admin_notes', 'approved_by', 'approved_at', 'returned_by']);
            $table->string('status')->default('out')->change();
            $table->timestamp('out_at')->useCurrent()->change();
            $table->timestamp('due_at')->nullable(false)->change();
            $table->timestamp('returned_at')->nullable(false)->change();
        });
    }
};