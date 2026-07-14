<?php

namespace App\Http\Controladores;

use App\Modelos\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * BUSCA de colaboradores (usuários de campo) ativos para a identificação no app.
 *
 * Rota PÚBLICA de propósito: o app de campo não tem login (decisão de escopo —
 * o offline-first não deve depender de sessão). Mas, para escalar até os 1.200+
 * usuários do edital SEM virar uma lista rolável gigante — e sem despejar o
 * diretório inteiro de nomes numa rota sem autenticação (fraco para a LGPD) —,
 * a identificação é por BUSCA: o colaborador digita parte do nome ou a matrícula
 * e recebe no máximo 25 correspondências. Exige ao menos 2 caracteres; sem termo
 * a rota devolve lista vazia. Expõe só nome, matrícula e cargo — nunca e-mail,
 * senha ou telefone.
 */
class ColaboradorControlador extends Controlador
{
    private const MIN_BUSCA = 2;
    private const LIMITE = 25;

    public function listar(Request $requisicao): JsonResponse
    {
        $busca = trim((string) $requisicao->query('busca', ''));

        // Sem termo suficiente não retorna nada — evita a lista aberta completa.
        if (mb_strlen($busca) < self::MIN_BUSCA) {
            return response()->json([]);
        }

        $colaboradores = Usuario::query()
            ->where('perfil', 'colaborador')
            ->where('ativo', true)
            ->where(function ($consulta) use ($busca) {
                $consulta->where('nome', 'like', "%{$busca}%")
                    ->orWhere('matricula', 'like', "%{$busca}%");
            })
            ->orderBy('nome')
            ->limit(self::LIMITE)
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
