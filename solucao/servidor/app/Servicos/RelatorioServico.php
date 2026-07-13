<?php

namespace App\Servicos;

use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;

/**
 * Geração do relatório de gestão de risco em .pdf e .docx, prontos para
 * assinatura digital (requisito da Ficha Técnica do Desafio, item 8.3.1).
 */
class RelatorioServico
{
    public function gerarPdf(array $analise, string $projeto): string
    {
        $pdf = Pdf::loadView('relatorios.gestao-risco', [
            'analise' => $analise,
            'projeto' => $projeto,
        ])->setPaper('a4');

        return $pdf->output();
    }

    public function gerarDocx(array $analise, string $projeto): string
    {
        $documento = new PhpWord();
        $secao = $documento->addSection();

        $secao->addTitle('Relatório de Gestão de Risco de Campo', 1);
        $secao->addText("Projeto/Missão: {$projeto}");
        $secao->addText("Atividade: {$analise['atividade']} | Ambiente: {$analise['ambiente']}");
        $secao->addText("Nível de risco: {$analise['nivelRisco']}/10 ({$analise['classificacao']})");

        $secao->addTitle('Riscos identificados', 2);
        foreach ($analise['riscosIdentificados'] as $risco) {
            $secao->addListItem($risco);
        }

        $secao->addTitle('EPIs recomendados', 2);
        foreach ($analise['episRecomendados'] as $epi) {
            $secao->addListItem("{$epi['nome']} — {$epi['uso']}");
        }

        $secao->addTitle('Medidas administrativas', 2);
        foreach ($analise['medidasAdministrativas'] as $medida) {
            $secao->addListItem($medida);
        }

        $secao->addTextBreak(1);
        $secao->addText('Assinatura do responsável técnico: ____________________________');

        $caminhoTemporario = tempnam(sys_get_temp_dir(), 'relatorio_') . '.docx';
        $documento->save($caminhoTemporario, 'Word2007');
        $conteudo = file_get_contents($caminhoTemporario);
        unlink($caminhoTemporario);

        return $conteudo;
    }
}
