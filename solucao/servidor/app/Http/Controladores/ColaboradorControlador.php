<?php

namespace App\Http\Controladores;

use App\Modelos\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Identificação de UM colaborador (usuário de campo) pela MATRÍCULA EXATA,
 * para o app de campo (sem login).
 *
 * Rota PÚBLICA de propósito: o app não tem login (decisão de escopo — o
 * offline-first não deve depender de sessão). A identificação é só por
 * matrícula (campo obrigatório e único no cadastro) — nunca por nome — porque
 * isso torna o resultado sempre 0 ou 1 registro: é IMPOSSÍVEL o colaborador
 * ver o nome de outra pessoa, mesmo digitando parcialmente, pois nada é
 * retornado até a matrícula completa bater exatamente. Expõe só nome,
 * matrícula e cargo — nunca e-mail, senha ou telefone.
 */
class ColaboradorControlador extends Controlador
{
    private const MIN_MATRICULA = 2;

    public function listar(Request $requisicao): JsonResponse
    {
        $matricula = trim((string) $requisicao->query('matricula', ''));

        // Sem matrícula completa suficiente não retorna nada.
        if (mb_strlen($matricula) < self::MIN_MATRICULA) {
            return response()->json([]);
        }

        $colaborador = Usuario::query()
            ->where('perfil', 'colaborador')
            ->where('ativo', true)
            ->whereRaw('LOWER(matricula) = ?', [mb_strtolower($matricula)])
            ->first();

        if (! $colaborador) {
            return response()->json([]);
        }

        return response()->json([[
            'id' => $colaborador->id,
            'nome' => $colaborador->nome,
            'matricula' => $colaborador->matricula,
            'cargo' => $colaborador->cargo,
        ]]);
    }
}
