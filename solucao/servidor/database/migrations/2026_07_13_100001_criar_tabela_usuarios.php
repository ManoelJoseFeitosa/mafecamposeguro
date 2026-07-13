<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $tabela) {
            $tabela->id();
            $tabela->string('nome');
            $tabela->string('email')->unique();
            $tabela->string('senha');
            $tabela->enum('perfil', ['gestor', 'colaborador'])->default('colaborador');
            $tabela->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
