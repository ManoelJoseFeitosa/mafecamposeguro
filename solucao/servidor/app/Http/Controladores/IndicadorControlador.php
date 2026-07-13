<?php

namespace App\Http\Controladores;

use App\Modelos\Missao;
use App\Modelos\RelatorioQuaseAcidente;
use Illuminate\Http\JsonResponse;

/**
 * Indicadores contratuais definidos na Ficha Técnica do Desafio (Anexo II):
 * percentual de planejamento de risco concluído e número de não conformidades.
 */
class IndicadorControlador extends Controlador
{
    public function exibir(): JsonResponse
    {
        $total = Missao::count();
        $concluidas = Missao::where('planejamento_concluido', true)->count();
        $percentual = $total > 0 ? round(($concluidas / $total) * 100, 1) : 0.0;

        $totalRqa = RelatorioQuaseAcidente::count();

        $porClassificacao = Missao::query()
            ->selectRaw('COALESCE(classificacao_risco, "N/D") as classificacao, COUNT(*) as total')
            ->groupBy('classificacao')
            ->pluck('total', 'classificacao');

        $historicoAlertas = Missao::query()
            ->where(function ($consulta) {
                $consulta->whereIn('classificacao_risco', ['Alto', 'Crítico'])
                    ->orWhere('clima_severo', true);
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'projeto', 'atividade', 'ambiente', 'classificacao_risco', 'clima_severo', 'created_at'])
            ->map(fn (Missao $missao) => [
                'missaoId' => $missao->id,
                'projeto' => $missao->projeto,
                'atividade' => $missao->atividade,
                'ambiente' => $missao->ambiente,
                'classificacaoRisco' => $missao->classificacao_risco,
                'climaSevero' => $missao->clima_severo,
                'criadoEm' => $missao->created_at,
            ]);

        $rqasRecentes = RelatorioQuaseAcidente::query()
            ->with('missao:id,projeto')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (RelatorioQuaseAcidente $rqa) => [
                'id' => $rqa->id,
                'missaoId' => $rqa->missao_id,
                'projetoMissao' => $rqa->missao?->projeto,
                'descricao' => $rqa->descricao,
                'criadoEm' => $rqa->created_at,
            ]);

        $totalClimaSevero = Missao::where('clima_severo', true)->count();
        $percentualClimaSevero = $total > 0 ? round(($totalClimaSevero / $total) * 100, 1) : 0.0;

        return response()->json([
            'totalMissoes' => $total,
            'percentualPlanejamentoRiscoConcluido' => $percentual,
            'totalRqaRegistrados' => $totalRqa,
            'missoesPorClassificacaoRisco' => $porClassificacao,
            'percentualMissoesComAlertaClimatico' => $percentualClimaSevero,
            'historicoAlertas' => $historicoAlertas,
            'rqasRecentes' => $rqasRecentes,
        ]);
    }
}
