<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MissaoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_missao_e_retorna_analise_de_risco(): void
    {
        $resposta = $this->postJson('/api/missoes', [
            'divisao' => 'DIHIBA',
            'projeto' => 'Expedição de teste',
            'atividade' => 'hidrometria_rio',
            'ambiente' => 'rio_lago',
            'latitude' => -14.2,
            'longitude' => -42.7,
            'tempoExposicaoHoras' => 7,
            'climaSevero' => true,
            'historicoAcidentesLocal' => 1,
        ]);

        $resposta->assertCreated();
        $resposta->assertJsonPath('analise.classificacao', 'Crítico');
        $resposta->assertJsonPath('divisao', 'DIHIBA');
        $resposta->assertJsonCount(4, 'pontosDeApoio');
    }

    public function test_rejeita_atividade_invalida(): void
    {
        $resposta = $this->postJson('/api/missoes', [
            'divisao' => 'DIHIBA',
            'projeto' => 'Expedição de teste',
            'atividade' => 'atividade_inexistente',
            'ambiente' => 'rio_lago',
            'latitude' => -14.2,
            'longitude' => -42.7,
        ]);

        $resposta->assertUnprocessable();
    }

    public function test_lista_missoes_para_sincronizacao_do_app_movel(): void
    {
        $this->postJson('/api/missoes', [
            'divisao' => 'DIHIBA',
            'projeto' => 'Expedição para listagem',
            'atividade' => 'hidrometria_rio',
            'ambiente' => 'rio_lago',
            'latitude' => -14.2,
            'longitude' => -42.7,
        ])->assertCreated();

        $resposta = $this->getJson('/api/missoes');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.projeto', 'Expedição para listagem');
        $resposta->assertJsonStructure([
            ['id', 'projeto', 'divisao', 'latitude', 'longitude', 'analise', 'pontosDeApoio', 'atualizadoEm'],
        ]);
    }

    public function test_indicadores_refletem_missoes_criadas(): void
    {
        $this->postJson('/api/missoes', [
            'divisao' => 'DIHIBA',
            'projeto' => 'Expedição A',
            'atividade' => 'coleta_amostras_solo',
            'ambiente' => 'area_rural',
            'latitude' => -14.2,
            'longitude' => -42.7,
        ])->assertCreated();

        $gestor = Usuario::factory()->create(['perfil' => 'gestor']);

        $resposta = $this->actingAs($gestor, 'sanctum')->getJson('/api/indicadores');

        $resposta->assertOk();
        $resposta->assertJsonPath('totalMissoes', 1);
        $resposta->assertJsonPath('percentualPlanejamentoRiscoConcluido', 100);
    }

    public function test_indicadores_exige_autenticacao(): void
    {
        $resposta = $this->getJson('/api/indicadores');

        $resposta->assertUnauthorized();
    }
}
