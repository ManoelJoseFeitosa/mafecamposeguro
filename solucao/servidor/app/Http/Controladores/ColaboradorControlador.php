<?php

namespace App\Http\Controladores;

use App\Modelos\Usuario;
use Illuminate\Http\JsonResponse;

/**
 * Lista de colaboradores (usuários de campo) ativos para o SELETOR do app móvel.
 *
 * Rota PÚBLICA de propósito: o app de campo não tem login (decisão de escopo
 * registrada no CLAUDE.md — o offline-first não deve depender de sessão). O
 * colaborador apenas escolhe seu nome nesta lista ao abrir o app. Por isso
 * expõe só dados mínimos de identificação (nome, matrícula, cargo) — nunca
 * e-mail, senha ou telefone.
 */
class ColaboradorControlador extends Controlador
{
    public function listar(): JsonResponse
    {
        $colaboradores = Usuario::query()
            ->where('perfil', 'colaborador')
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return response()->json(
            $colaboradores->map(fn (Usuario $c) => [
                'id' => $c->id,
                'nome' => $c->nome,
                'matricula' => $c->matricula,
                'cargo' => $c->cargo,
            ])->all(),
        );
    }
}
