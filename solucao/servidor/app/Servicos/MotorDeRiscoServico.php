<?php

namespace App\Servicos;

/**
 * Motor determinístico de análise de risco e recomendação de EPIs.
 *
 * Dado (atividade, ambiente, tempo de exposição em horas, previsão de tempo
 * severa?), calcula um nível de risco e a lista de EPIs/riscos aplicáveis,
 * combinando as entradas da matriz definida em CatalogoServico. É
 * intencionalmente determinístico (não usa IA generativa) para ser auditável —
 * decisão registrada em CLAUDE.md, seção 3.
 */
class MotorDeRiscoServico
{
    /**
     * @return array{
     *     atividade: string,
     *     ambiente: string,
     *     nivelRisco: int,
     *     classificacao: string,
     *     riscosIdentificados: string[],
     *     episRecomendados: array<int, array{codigo: string, nome: string, uso: string, manutencao: string}>,
     *     medidasAdministrativas: string[],
     * }
     */
    public function analisar(
        string $atividade,
        string $ambiente,
        float $tempoExposicaoHoras = 4.0,
        bool $climaSevero = false,
        int $historicoAcidentesLocal = 0,
    ): array {
        $regra = collect(CatalogoServico::MATRIZ_RISCO)
            ->first(fn (array $r) => $r['atividade'] === $atividade && $r['ambiente'] === $ambiente);

        if ($regra === null) {
            // Sem regra exata cadastrada: aplica um conjunto mínimo conservador em
            // vez de "inventar" um risco específico não catalogado.
            $riscos = ['Combinação atividade/ambiente não catalogada — aplicar avaliação manual do engenheiro de segurança.'];
            $codigosEpi = ['bota_seguranca', 'gps_comunicador_satelital'];
            $nivel = 5;
        } else {
            $riscos = $regra['riscos'];
            $codigosEpi = $regra['epis'];
            $nivel = $regra['nivel_base'];
        }

        if ($tempoExposicaoHoras > 6) {
            $nivel += 1;
        }
        if ($climaSevero) {
            $nivel += 2;
        }
        if ($historicoAcidentesLocal > 0) {
            $nivel += min($historicoAcidentesLocal, 2);
        }
        $nivel = max(1, min($nivel, 10));

        $epis = collect($codigosEpi)->map(function (string $codigo) {
            $info = CatalogoServico::INFORMACOES_EPI[$codigo];

            return [
                'codigo' => $codigo,
                'nome' => $info['nome'],
                'uso' => $info['uso'],
                'manutencao' => $info['manutencao'],
            ];
        })->values()->all();

        return [
            'atividade' => $atividade,
            'ambiente' => $ambiente,
            'nivelRisco' => $nivel,
            'classificacao' => $this->classificar($nivel),
            'riscosIdentificados' => $riscos,
            'episRecomendados' => $epis,
            'medidasAdministrativas' => $this->medidasAdministrativas($nivel, $climaSevero, $tempoExposicaoHoras),
        ];
    }

    private function classificar(int $nivel): string
    {
        return match (true) {
            $nivel <= 2 => 'Baixo',
            $nivel <= 4 => 'Moderado',
            $nivel <= 7 => 'Alto',
            default => 'Crítico',
        };
    }

    /** @return string[] */
    private function medidasAdministrativas(int $nivel, bool $climaSevero, float $tempoExposicaoHoras): array
    {
        $medidas = [
            'Registrar plano de risco no sistema antes da saída de campo.',
            'Comunicar horário previsto de retorno ao gestor responsável.',
        ];

        if ($nivel >= 5) {
            $medidas[] = 'Exigir acompanhamento de ao menos 2 colaboradores (proibida atividade solo).';
        }
        if ($climaSevero) {
            $medidas[] = 'Reavaliar viabilidade da saída devido a alerta meteorológico severo (INMET/Defesa Civil).';
        }
        if ($tempoExposicaoHoras > 6) {
            $medidas[] = 'Programar pausas de recuperação a cada 2h de exposição contínua.';
        }
        if ($nivel >= 8) {
            $medidas[] = 'Exigir aprovação expressa do gestor de segurança antes da saída.';
        }

        return $medidas;
    }
}
