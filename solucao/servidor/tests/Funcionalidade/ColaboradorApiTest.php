<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_seletor_do_app_lista_apenas_colaboradores_ativos_sem_login(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ativa', 'ativo' => true, 'matricula' => 'SGB-1']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Beto Inativo', 'ativo' => false, 'matricula' => 'SGB-2']);
        Usuario::factory()->create(['perfil' => 'gestor', 'nome' => 'Gestor Qualquer']);

        // Rota pública: o app de campo não envia token.
        $resposta = $this->getJson('/api/colaboradores');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ativa');
        $resposta->assertJsonPath('0.matricula', 'SGB-1');
    }

    public function test_seletor_nao_expoe_email_nem_telefone(): void
    {
        Usuario::factory()->create([
            'perfil' => 'colaborador',
            'nome' => 'Ana Ativa',
            'telefone' => '(86) 90000-0000',
        ]);

        $resposta = $this->getJson('/api/colaboradores');

        $resposta->assertOk();
        $primeiro = $resposta->json('0');
        $this->assertArrayNotHasKey('email', $primeiro);
        $this->assertArrayNotHasKey('telefone', $primeiro);
    }
}
