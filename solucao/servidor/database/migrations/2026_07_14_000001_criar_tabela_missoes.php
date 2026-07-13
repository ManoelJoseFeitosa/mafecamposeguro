<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missoes', function (Blueprint $tabela) {
            $tabela->id();
            // Colaborador (usuário de campo) responsável pela missão. Nulo quando
            // a missão foi criada solta (ex.: no protótipo antigo, sem atribuição).
            // O gestor atribui um colaborador ao gerar a missão no painel web.
            $tabela->foreignId('colaborador_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $tabela->string('divisao');
            $tabela->string('atividade');
            $tabela->string('ambiente');
            $tabela->string('projeto');
            $tabela->double('latitude');
            $tabela->double('longitude');
            $tabela->float('tempo_exposicao_horas')->default(4);
            $tabela->boolean('clima_severo')->default(false);
            $tabela->boolean('planejamento_concluido')->default(false);
            $tabela->unsignedTinyInteger('nivel_risco')->nullable();
            $tabela->string('classificacao_risco')->nullable();
            $tabela->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missoes');
    }
};
