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
            // Três perfis, do mais para o menos poderoso:
            // superadmin > gestor > colaborador (usuário de campo). Ver CLAUDE.md.
            $tabela->enum('perfil', ['superadmin', 'gestor', 'colaborador'])->default('colaborador');
            // Campos usados sobretudo para o cadastro de usuários de campo
            // (colaboradores). Nulos para gestor/superadmin, que se identificam
            // por e-mail/senha e não aparecem no seletor do app.
            $tabela->string('matricula')->nullable()->unique();
            $tabela->string('cargo')->nullable();
            $tabela->string('telefone')->nullable();
            $tabela->boolean('ativo')->default(true);
            $tabela->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
