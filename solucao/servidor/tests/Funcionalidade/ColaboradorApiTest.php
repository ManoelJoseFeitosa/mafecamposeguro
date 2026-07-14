<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColaboradorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_matricula_completa_e_exata_identifica_um_unico_colaborador_sem_login(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0001']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carlos Menezes', 'ativo' => true, 'matricula' => 'SGB-0002']);

        // Rota pública (sem token) + matrícula completa (case-insensitive).
        $resposta = $this->getJson('/api/colaboradores?matricula=' . urlencode('sgb-0001'));

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Ana Ribeiro');
        $resposta->assertJsonMissingPath('1');
    }

    public function test_matricula_parcial_nunca_revela_nome_de_outro_colaborador(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0001']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carlos Menezes', 'ativo' => true, 'matricula' => 'SGB-0002']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'João Pereira', 'ativo' => true, 'matricula' => 'SGB-0003']);

        // Digitar um prefixo comum a vários (ex.: "SGB-") não deve revelar
        // NENHUM nome — é justamente o comportamento reportado como bug.
        $resposta = $this->getJson('/api/colaboradores?matricula=SGB-');

        $resposta->assertOk();
        $resposta->assertJsonCount(0);
    }

    public function test_busca_por_nome_nao_e_mais_aceita(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Ana Ribeiro', 'ativo' => true, 'matricula' => 'SGB-0001']);

        // Digitar o nome completo não retorna nada — só matrícula identifica.
        $resposta = $this->getJson('/api/colaboradores?matricula=' . urlencode('Ana Ribeiro'));

        $resposta->assertOk();
        $resposta->assertJsonCount(0);
    }

    public function test_sem_matricula_nao_despeja_o_diretorio(): void
    {
        Usuario::factory()->count(3)->create(['perfil' => 'colaborador', 'ativo' => true]);

        $this->getJson('/api/colaboradores')->assertOk()->assertJsonCount(0);
    }

    public function test_ignora_inativos_e_nao_colaboradores(): void
    {
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Ativa', 'ativo' => true, 'matricula' => 'SGB-9001']);
        Usuario::factory()->create(['perfil' => 'colaborador', 'nome' => 'Carla Inativa', 'ativo' => false, 'matricula' => 'SGB-9002']);
        Usuario::factory()->create(['perfil' => 'gestor', 'nome' => 'Carla Gestora', 'matricula' => 'SGB-9003']);

        $this->getJson('/api/colaboradores?matricula=SGB-9002')->assertOk()->assertJsonCount(0);
        $this->getJson('/api/colaboradores?matricula=SGB-9003')->assertOk()->assertJsonCount(0);

        $resposta = $this->getJson('/api/colaboradores?matricula=SGB-9001');
        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.nome', 'Carla Ativa');
    }

    public function test_nao_expoe_email_nem_telefone(): void
    {
        Usuario::factory()->create([
            'perfil' => 'colaborador',
            'nome' => 'Ana Ribeiro',
            'matricula' => 'SGB-0001',
            'telefone' => '(86) 90000-0000',
        ]);

        $primeiro = $this->getJson('/api/colaboradores?matricula=SGB-0001')->json('0');

        $this->assertArrayNotHasKey('email', $primeiro);
        $this->assertArrayNotHasKey('telefone', $primeiro);
    }
}
