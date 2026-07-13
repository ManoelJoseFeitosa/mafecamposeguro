<?php

namespace App\Http\Controladores;

use App\Modelos\Missao;
use App\Modelos\RelatorioQuaseAcidente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as RespostaHttp;

class RqaControlador extends Controlador
{
    public function armazenar(Request $requisicao): JsonResponse
    {
        $dados = $requisicao->validate([
            'missaoId' => ['required', 'integer', 'exists:missoes,id'],
            'descricao' => ['required', 'string', 'max:2000'],
        ]);

        $rqa = RelatorioQuaseAcidente::create([
            'missao_id' => $dados['missaoId'],
            'descricao' => $dados['descricao'],
        ]);

        return response()->json([
            'id' => $rqa->id,
            'registradoEm' => $rqa->created_at?->toIso8601String(),
        ], RespostaHttp::HTTP_CREATED);
    }
}
