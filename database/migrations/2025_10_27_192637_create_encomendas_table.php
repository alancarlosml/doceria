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
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pendente', 'em_producao', 'pronto', 'entregue', 'cancelado'])->default('pendente');
            $table->date('delivery_date');
            $table->time('delivery_time')->nullable();
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('custom_costs', 10, 2)->default(0); // Custos adicionais (coberturas, decorações, etc.)
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
