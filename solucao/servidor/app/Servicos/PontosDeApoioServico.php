<?php

namespace App\Servicos;

/**
 * Localização de pontos de apoio (hospitais, delegacias, UBS, bombeiros)
 * próximos a uma missão de campo.
 *
 * A distância é calculada de verdade a partir das coordenadas da missão pela
 * fórmula de Haversine (distância entre dois pontos na superfície da Terra).
 * No protótipo, os pontos de apoio são posicionados por deslocamentos de
 * referência (ver CatalogoServico::PONTOS_DE_APOIO_MOCK); em produção, este
 * serviço vira um adapter que consulta bases públicas geolocalizadas
 * (CNES/DATASUS para saúde, SSP estaduais para segurança) buscando por
 * coordenada — o contrato de saída (nome, tipo, distânciaKm, coordenadas,
 * telefone) permanece o mesmo, então nada acima muda.
 */
class PontosDeApoioServico
{
    private const RAIO_TERRA_KM = 6371.0;

    /**
     * @return array<int, array{nome: string, tipo: string, distanciaKm: float, latitude: float, longitude: float, telefone: string}>
     */
    public function buscar(float $latitude, float $longitude, float $raioKm = 60.0): array
    {
        return collect(CatalogoServico::PONTOS_DE_APOIO_MOCK)
            ->map(function (array $ponto) use ($latitude, $longitude) {
                $pontoLat = $latitude + $ponto['delta_lat'];
                $pontoLng = $longitude + $ponto['delta_lng'];

                return [
                    'nome' => $ponto['nome'],
                    'tipo' => $ponto['tipo'],
                    'distanciaKm' => round($this->distanciaHaversineKm($latitude, $longitude, $pontoLat, $pontoLng), 1),
                    'latitude' => round($pontoLat, 6),
                    'longitude' => round($pontoLng, 6),
                    'telefone' => $ponto['telefone'],
                ];
            })
            ->filter(fn (array $ponto) => $ponto['distanciaKm'] <= $raioKm)
            ->sortBy('distanciaKm')
            ->values()
            ->all();
    }

    /** Distância em km entre duas coordenadas (fórmula de Haversine). */
    private function distanciaHaversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return self::RAIO_TERRA_KM * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
