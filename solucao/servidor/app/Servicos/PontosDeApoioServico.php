<?php

namespace App\Servicos;

/**
 * Localização de pontos de apoio (hospitais, delegacias, UBS, bombeiros).
 *
 * Protótipo usa dados mock fixos (CatalogoServico::PONTOS_DE_APOIO_MOCK). Em
 * produção, este serviço vira um adapter que consulta bases públicas
 * geolocalizadas (CNES/DATASUS para saúde, SSP estaduais para segurança) a
 * partir de lat/long — ver documentacao/02_escopo_tecnico_arquitetura.pdf.
 */
class PontosDeApoioServico
{
    /**
     * @return array<int, array{nome: string, tipo: string, distanciaKm: float, telefone: string}>
     */
    public function buscar(float $latitude, float $longitude, float $raioKm = 30.0): array
    {
        // Protótipo: retorna a lista mock ordenada por distância, filtrando pelo
        // raio. (lat/long ainda não entram no cálculo — placeholder documentado
        // para a integração real; não fingir precisão geográfica que não existe.)
        return collect(CatalogoServico::PONTOS_DE_APOIO_MOCK)
            ->filter(fn (array $ponto) => $ponto['distancia_km'] <= $raioKm)
            ->sortBy('distancia_km')
            ->map(fn (array $ponto) => [
                'nome' => $ponto['nome'],
                'tipo' => $ponto['tipo'],
                'distanciaKm' => $ponto['distancia_km'],
                'telefone' => $ponto['telefone'],
            ])
            ->values()
            ->all();
    }
}
