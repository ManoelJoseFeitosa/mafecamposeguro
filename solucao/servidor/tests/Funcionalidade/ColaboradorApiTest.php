<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_busca_encontra_colaborador_ativo_por_nome_sem_login(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-1']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Beto Souza', 'ativo' => true, 'matricula' => 'SGB-2']);

        // Rota pública: o app de campo não envia token.
        $resposta = $this->getJson('/api/colaboradores?busca=ana');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ribeiro');
        $resposta->assertJsonPath('0.matricula', 'SGB-1');
    }

    public function test_busca_tambem_encontra_por_matricula(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-777']);

        $resposta = $this->getJson('/api/colaboradores?busca=SGB-777');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ribeiro');
    }

    public function test_sem_termo_de_busca_nao_despeja_o_diretorio(): void
    {
        Usuario::factory()->count(3)->create(['perfil' => 'colaborador', 'ativo' => true]);

        // Escala/LGPD: sem busca (ou com 1 caractere) a rota não lista todos.
        $this->getJson('/api/colaboradores')->assertOk()->assertJsonCount(0);
        $this->getJson('/api/colaboradores?busca=a')->assertOk()->assertJsonCount(0);
    }

    public function test_busca_ignora_inativos_e_nao_colaboradores(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Ativa', 'ativo' => true]);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Inativa', 'ativo' => false]);
        Usuario::factory()->create(['perfil' => 'gestor', 'nome' => 'Carla Gestora']);

        $resposta = $this->getJson('/api/colaboradores?busca=Carla');

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

        $primeiro = $this->getJson('/api/colaboradores?busca=ana')->json('0');

        $this->assertArrayNotHasKey('email', $primeiro);
        $this->assertArrayNotHasKey('telefone', $primeiro);
    }
}
