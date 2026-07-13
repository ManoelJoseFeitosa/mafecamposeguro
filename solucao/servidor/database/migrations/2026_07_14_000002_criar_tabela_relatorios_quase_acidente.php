<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relatorios_quase_acidente', function (Blueprint $tabela) {
            $tabela->id();
            $tabela->foreignId('missao_id')->constrained('missoes')->cascadeOnDelete();
            $tabela->text('descricao');
            $tabela->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relatorios_quase_acidente');
    }
};
