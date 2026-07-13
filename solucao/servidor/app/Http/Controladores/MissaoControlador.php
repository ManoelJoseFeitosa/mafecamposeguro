<?php

namespace App\Http\Controladores;

use App\Modelos\Missao;
use App\Servicos\CatalogoServico;
use App\Servicos\MotorDeRiscoServico;
use App\Servicos\PontosDeApoioServico;
use App\Servicos\RelatorioServico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as RespostaHttp;

class MissaoControlador extends Controlador
{
    public function __construct(
        private readonly MotorDeRiscoServico $motorDeRisco,
        private readonly PontosDeApoioServico $pontosDeApoio,
        private readonly RelatorioServico $relatorios,
    ) {
    }

    /**
     * Lista todas as missões com a análise recalculada. Usado pelo app móvel
     * (solucao/app-movel) para popular o cache local (SQLite) durante a
     * sincronização "pull" quando há conectividade — ver
     * codigo-fonte/servicos/sincronizacaoServico.ts.
     */
    public function listar(Request $requisicao): JsonResponse
    {
        $consulta = Missao::with('colaborador')->orderByDesc('created_at');

        // Filtro opcional por colaborador. O app móvel offline-first, na prática,
        // baixa todas as missões e filtra "as minhas" localmente (para funcionar
        // sem rede depois); este parâmetro existe para consultas pontuais online.
        if ($requisicao->filled('colaboradorId')) {
            $consulta->where('colaborador_id', (int) $requisicao->query('colaboradorId'));
        }

        $resultado = $consulta->get()->map(function (Missao $missao) {
            $analise = $this->reanalisar($missao);
            $pontos = $this->pontosDeApoio->buscar($missao->latitude, $missao->longitude);

            return [
                'id' => $missao->id,
                'projeto' => $missao->projeto,
                'divisao' => $missao->divisao,
                'latitude' => $missao->latitude,
                'longitude' => $missao->longitude,
                'colaboradorId' => $missao->colaborador_id,
                'colaboradorNome' => $missao->colaborador?->nome,
                'analise' => $analise,
                'pontosDeApoio' => $pontos,
                'atualizadoEm' => $missao->updated_at?->toIso8601String(),
            ];
        });

        return response()->json($resultado);
    }

    public function armazenar(Request $requisicao): JsonResponse
    {
        $dados = $requisicao->validate([
            'divisao' => ['required', 'string', 'in:' . implode(',', array_keys(CatalogoServico::DIVISOES))],
            'projeto' => ['required', 'string', 'max:255'],
            'atividade' => ['required', 'string', 'in:' . implode(',', CatalogoServico::ATIVIDADES)],
            'ambiente' => ['required', 'string', 'in:' . implode(',', CatalogoServico::AMBIENTES)],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'tempoExposicaoHoras' => ['nullable', 'numeric', 'min:1', 'max:24'],
            'climaSevero' => ['nullable', 'boolean'],
            'historicoAcidentesLocal' => ['nullable', 'integer', 'min:0'],
            // Colaborador atribuído. Opcional: o gestor informa ao gerar a missão
            // no painel; missões criadas direto no app (sem atribuição) ficam nulas.
            'colaboradorId' => ['nullable', 'integer', 'exists:usuarios,id'],
        ]);

        $tempoExposicao = (float) ($dados['tempoExposicaoHoras'] ?? 4.0);
        $climaSevero = (bool) ($dados['climaSevero'] ?? false);
        $historico = (int) ($dados['historicoAcidentesLocal'] ?? 0);

        $analise = $this->motorDeRisco->analisar(
            atividade: $dados['atividade'],
            ambiente: $dados['ambiente'],
            tempoExposicaoHoras: $tempoExposicao,
            climaSevero: $climaSevero,
            historicoAcidentesLocal: $historico,
        );

        $missao = Missao::create([
            'colaborador_id' => $dados['colaboradorId'] ?? null,
            'divisao' => $dados['divisao'],
            'atividade' => $dados['atividade'],
            'ambiente' => $dados['ambiente'],
            'projeto' => $dados['projeto'],
            'latitude' => $dados['latitude'],
            'longitude' => $dados['longitude'],
            'tempo_exposicao_horas' => $tempoExposicao,
            'clima_severo' => $climaSevero,
            'planejamento_concluido' => true,
            'nivel_risco' => $analise['nivelRisco'],
            'classificacao_risco' => $analise['classificacao'],
        ]);

        $pontos = $this->pontosDeApoio->buscar($dados['latitude'], $dados['longitude']);

        return response()->json([
            'id' => $missao->id,
            'projeto' => $missao->projeto,
            'divisao' => $missao->divisao,
            'colaboradorId' => $missao->colaborador_id,
            'colaboradorNome' => $missao->colaborador?->nome,
            'analise' => $analise,
            'pontosDeApoio' => $pontos,
        ], RespostaHttp::HTTP_CREATED);
    }

    public function relatorioPdf(Missao $missao): Response
    {
        $analise = $this->reanalisar($missao);
        $conteudo = $this->relatorios->gerarPdf($analise, $missao->projeto);

        return response($conteudo, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="relatorio-missao-' . $missao->id . '.pdf"',
        ]);
    }

    public function relatorioDocx(Missao $missao): Response
    {
        $analise = $this->reanalisar($missao);
        $conteudo = $this->relatorios->gerarDocx($analise, $missao->projeto);

        return response($conteudo, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="relatorio-missao-' . $missao->id . '.docx"',
        ]);
    }

    private function reanalisar(Missao $missao): array
    {
        return $this->motorDeRisco->analisar(
            atividade: $missao->atividade,
            ambiente: $missao->ambiente,
            tempoExposicaoHoras: $missao->tempo_exposicao_horas,
            climaSevero: $missao->clima_severo,
        );
    }
}
