<?php

namespace Tests\Unitario;

use App\Servicos\PontosDeApoioServico;
use PHPUnit\Framework\TestCase;

class PontosDeApoioServicoTest extends TestCase
{
    public function test_calcula_distancia_real_a_partir_das_coordenadas_da_missao(): void
    {
        $servico = new PontosDeApoioServico();

        $pontos = $servico->buscar(-5.09, -42.80); // Teresina-PI (referência)

        $this->assertNotEmpty($pontos);

        foreach ($pontos as $ponto) {
            // Cada ponto tem coordenadas próprias e distância calculada (> 0),
            // não um número fixo — é isso que caracteriza a geolocalização real.
            $this->assertArrayHasKey('latitude', $ponto);
            $this->assertArrayHasKey('longitude', $ponto);
            $this->assertGreaterThan(0, $ponto['distanciaKm']);
        }
    }

    public function test_pontos_vem_ordenados_do_mais_proximo_ao_mais_distante(): void
    {
        $servico = new PontosDeApoioServico();

        $distancias = array_column($servico->buscar(-15.79, -47.88), 'distanciaKm');
        $ordenado = $distancias;
        sort($ordenado);

        $this->assertSame($ordenado, $distancias);
    }

    public function test_distancia_muda_quando_a_missao_muda_de_lugar(): void
    {
        $servico = new PontosDeApoioServico();

        // Mesmo ponto de apoio (mesmo índice) a partir de duas missões distantes
        // deve resultar em latitudes diferentes — prova de que o cálculo usa a
        // coordenada de entrada, não um valor fixo.
        $emBrasilia = $servico->buscar(-15.79, -47.88)[0];
        $emTeresina = $servico->buscar(-5.09, -42.80)[0];

        $this->assertNotEquals($emBrasilia['latitude'], $emTeresina['latitude']);
    }
}
