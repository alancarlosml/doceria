<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Corrige o comportamento do campo opened_at para não ser atualizado automaticamente
     * quando o registro é modificado (remove ON UPDATE CURRENT_TIMESTAMP se existir).
     */
    public function up(): void
    {
        // Para MySQL, modifica o campo para remover o ON UPDATE CURRENT_TIMESTAMP
        if (config('database.default') === 'mysql') {
            DB::statement('ALTER TABLE cash_registers MODIFY opened_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nada a reverter, pois a mudança é apenas para garantir comportamento correto
    }
};

