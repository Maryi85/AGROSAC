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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense'])->index();
            $table->string('category');
            $table->decimal('amount', 14, 2);
            $table->date('occurred_at')->index();
            $table->foreignId('crop_id')->nullable()->constrained('crops')->nullOnDelete();
            $table->foreignId('plot_id')->nullable()->constrained('plots')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
