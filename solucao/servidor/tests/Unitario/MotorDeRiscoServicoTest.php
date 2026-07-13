<?php

namespace Tests\Unitario;

use App\Servicos\MotorDeRiscoServico;
use PHPUnit\Framework\TestCase;

class MotorDeRiscoServicoTest extends TestCase
{
    public function test_regra_catalogada_retorna_epis_especificos(): void
    {
        $motor = new MotorDeRiscoServico();
        $resultado = $motor->analisar('hidrometria_rio', 'rio_lago');

        $codigos = array_column($resultado['episRecomendados'], 'codigo');

        $this->assertContains('colete_flutuacao', $codigos);
        $this->assertContains($resultado['classificacao'], ['Moderado', 'Alto', 'Crítico']);
    }

    public function test_clima_severo_aumenta_nivel_de_risco(): void
    {
        $motor = new MotorDeRiscoServico();

        $base = $motor->analisar('caminhamento_geologico', 'mata_fechada', climaSevero: false);
        $severo = $motor->analisar('caminhamento_geologico', 'mata_fechada', climaSevero: true);

        $this->assertGreaterThan($base['nivelRisco'], $severo['nivelRisco']);
        $this->assertStringContainsStringIgnoringCase(
            'alerta meteorológico',
            implode(' ', $severo['medidasAdministrativas'])
        );
    }

    public function test_combinacao_nao_catalogada_nao_inventa_risco_especifico(): void
    {
        $motor = new MotorDeRiscoServico();
        $resultado = $motor->analisar('sensoriamento_aereo_drone', 'montanha_alto_risco');

        $this->assertStringContainsString('não catalogada', $resultado['riscosIdentificados'][0]);
    }
}
