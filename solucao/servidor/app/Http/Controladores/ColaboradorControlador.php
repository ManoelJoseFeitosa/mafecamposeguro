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
 * a identificação é por CORRESPONDÊNCIA EXATA: o colaborador digita o nome
 * completo OU a matrícula completa e recebe o(s) cadastro(s) correspondente(s).
 * Não faz busca por prefixo/trecho de propósito — evita que digitar 1-2
 * caracteres liste vários nomes (enumeração do diretório), reforçando a LGPD.
 * Exige ao menos 2 caracteres; sem termo devolve lista vazia. Expõe só nome,
 * matrícula e cargo — nunca e-mail, senha ou telefone.
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

        $termo = mb_strtolower($busca);

        $colaboradores = Usuario::query()
            ->where('perfil', 'colaborador')
            ->where('ativo', true)
            ->where(function ($consulta) use ($termo) {
                // Igualdade exata (case-insensitive) — nome completo OU matrícula.
                $consulta->whereRaw('LOWER(nome) = ?', [$termo])
                    ->orWhereRaw('LOWER(matricula) = ?', [$termo]);
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
