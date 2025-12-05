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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do insumo
            $table->decimal('current_quantity', 10, 2)->default(0); // Quantidade atual
            $table->decimal('min_quantity', 10, 2)->default(0); // Quantidade mínima para alerta
            $table->string('unit', 50)->default('unidade'); // Unidade de medida (pacote, caixa, unidade, litro, etc)
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null'); // Usuário que fez a última atualização
            $table->text('notes')->nullable(); // Observações
            $table->boolean('active')->default(true); // Se está ativo
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
