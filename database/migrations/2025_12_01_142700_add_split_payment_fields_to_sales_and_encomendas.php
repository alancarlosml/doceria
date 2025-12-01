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
        // Adicionar campos na tabela sales
        Schema::table('sales', function (Blueprint $table) {
            // Campos para pagamento dividido (split payment)
            $table->json('payment_methods_split')->nullable()->after('payment_method'); // JSON com múltiplas formas de pagamento
            
            // Campos para valor recebido e troco (pagamento em dinheiro)
            $table->decimal('amount_received', 10, 2)->nullable()->after('payment_methods_split'); // Valor recebido do cliente
            $table->decimal('change_amount', 10, 2)->nullable()->after('amount_received'); // Troco
        });

        // Adicionar campos na tabela encomendas
        Schema::table('encomendas', function (Blueprint $table) {
            // Método de pagamento (que não existia antes)
            $table->enum('payment_method', ['dinheiro', 'cartao_credito', 'cartao_debito', 'pix', 'transferencia'])->nullable()->after('total');
            
            // Campos para pagamento dividido (split payment)
            $table->json('payment_methods_split')->nullable()->after('payment_method'); // JSON com múltiplas formas de pagamento
            
            // Campos para valor recebido e troco (pagamento em dinheiro)
            $table->decimal('amount_received', 10, 2)->nullable()->after('payment_methods_split'); // Valor recebido do cliente
            $table->decimal('change_amount', 10, 2)->nullable()->after('amount_received'); // Troco
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_methods_split', 'amount_received', 'change_amount']);
        });

        Schema::table('encomendas', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_methods_split', 'amount_received', 'change_amount']);
        });
    }
};
