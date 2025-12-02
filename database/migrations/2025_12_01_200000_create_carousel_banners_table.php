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
        Schema::create('carousel_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image'); // Caminho da imagem
            $table->string('title')->nullable(); // Título opcional
            $table->text('description')->nullable(); // Descrição opcional
            $table->string('link')->nullable(); // Link opcional ao clicar
            $table->integer('order')->default(0); // Ordem de exibição
            $table->boolean('active')->default(true); // Se está ativo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousel_banners');
    }
};

