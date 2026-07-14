<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_busca_encontra_por_nome_completo_sem_login(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0001']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Beto Souza', 'ativo' => true, 'matricula' => 'SGB-0002']);

        // Rota pública (sem token) + nome completo (case-insensitive).
        $resposta = $this->getJson('/api/colaboradores?busca=' . urlencode('ana ribeiro'));

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ribeiro');
    }

    public function test_busca_encontra_por_matricula_completa(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0777']);

        $resposta = $this->getJson('/api/colaboradores?busca=SGB-0777');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ribeiro');
    }

    public function test_termo_parcial_nao_retorna_ninguem(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0001']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carlos Menezes', 'ativo' => true, 'matricula' => 'SGB-0002']);

        // Digitar só um trecho (nome parcial ou prefixo de matrícula) não lista
        // ninguém — exige nome/matrícula completos (evita enumerar o diretório).
        $this->getJson('/api/colaboradores?busca=ana')->assertOk()->assertJsonCount(0);
        $this->getJson('/api/colaboradores?busca=SGB')->assertOk()->assertJsonCount(0);
    }

    public function test_sem_termo_de_busca_nao_despeja_o_diretorio(): void
    {
        Usuario::factory()->count(3)->create(['perfil' => 'colaborador', 'ativo' => true]);

        $this->getJson('/api/colaboradores')->assertOk()->assertJsonCount(0);
    }

    public function test_busca_ignora_inativos_e_nao_colaboradores(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Ativa', 'ativo' => true]);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Inativa', 'ativo' => false]);
        Usuario::factory()->create(['perfil' => 'gestor', 'nome' => 'Carla Ativa']); // mesmo nome, outro perfil

        $resposta = $this->getJson('/api/colaboradores?busca=' . urlencode('Carla Ativa'));

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Carla Ativa');
    }

    public function test_busca_nao_expoe_email_nem_telefone(): void
    {
        Usuario::factory()->create([
            'perfil' => 'colaborador',
            'nome' => 'Ana Ribeiro',
            'telefone' => '(86) 90000-0000',
        ]);

        $primeiro = $this->getJson('/api/colaboradores?busca=' . urlencode('Ana Ribeiro'))->json('0');

        $this->assertArrayNotHasKey('email', $primeiro);
        $this->assertArrayNotHasKey('telefone', $primeiro);
    }
}
