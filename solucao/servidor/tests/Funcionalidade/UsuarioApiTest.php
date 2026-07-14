<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_gestao_de_usuarios_exige_autenticacao(): void
    {
        $this->getJson('/api/usuarios')->assertUnauthorized();
        $this->postJson('/api/usuarios', [])->assertUnauthorized();
    }

    public function test_colaborador_nao_acessa_gestao_de_usuarios(): void
    {
        $colaborador = Usuario::factory()->create(['perfil' => 'colaborador']);

        $this->actingAs($colaborador, 'sanctum')
            ->getJson('/api/usuarios')
            ->assertForbidden();
    }

    public function test_superadmin_cria_colaborador_de_campo(): void
    {
        $admin = Usuario::factory()->create(['perfil' => 'superadmin']);

        $resposta = $this->actingAs($admin, 'sanctum')->postJson('/api/usuarios', [
            'nome' => 'Maria Souza',
            'email' => 'maria.souza@exemplo.local',
            'senha' => 'segredo123',
            'perfil' => 'colaborador',
            'matricula' => 'SGB-9001',
            'cargo' => 'Geóloga',
            'telefone' => '(86) 90000-0000',
        ]);

        $resposta->assertCreated();
        $resposta->assertJsonPath('perfil', 'colaborador');
        $resposta->assertJsonPath('matricula', 'SGB-9001');
        $this->assertDatabaseHas('usuarios', ['email' => 'maria.souza@exemplo.local', 'perfil' => 'colaborador']);
    }

    public function test_colaborador_sem_matricula_e_rejeitado(): void
    {
        $admin = Usuario::factory()->create(['perfil' => 'superadmin']);

        $this->actingAs($admin, 'sanctum')->postJson('/api/usuarios', [
            'nome' => 'Sem Matricula',
            'email' => 'sem.matricula@exemplo.local',
            'senha' => 'segredo123',
            'perfil' => 'colaborador',
            // matrícula ausente de propósito
        ])->assertUnprocessable()->assertJsonValidationErrors('matricula');
    }

    public function test_superadmin_pode_criar_gestor(): void
    {
        $admin = Usuario::factory()->create(['perfil' => 'superadmin']);

        $this->actingAs($admin, 'sanctum')->postJson('/api/usuarios', [
            'nome' => 'Novo Gestor',
            'email' => 'novo.gestor@exemplo.local',
            'senha' => 'segredo123',
            'perfil' => 'gestor',
        ])->assertCreated();
    }

    public function test_gestor_nao_pode_criar_outro_gestor(): void
    {
        $gestor = Usuario::factory()->create(['perfil' => 'gestor']);

        // Gestor só gerencia colaboradores; tentar criar 'gestor' é rejeitado
        // na validação (perfil fora da lista permitida para o solicitante).
        $this->actingAs($gestor, 'sanctum')->postJson('/api/usuarios', [
            'nome' => 'Gestor Proibido',
            'email' => 'proibido@exemplo.local',
            'senha' => 'segredo123',
            'perfil' => 'gestor',
        ])->assertUnprocessable();
    }

    public function test_gestor_so_enxerga_colaboradores_na_listagem(): void
    {
        $gestor = Usuario::factory()->create(['perfil' => 'gestor']);
        Usuario::factory()->create(['perfil' => 'superadmin']);
        Usuario::factory()->create(['perfil' => 'colaborador']);

        $resposta = $this->actingAs($gestor, 'sanctum')->getJson('/api/usuarios');

        $resposta->assertOk();
        foreach ($resposta->json() as $usuario) {
            $this->assertSame('colaborador', $usuario['perfil']);
        }
    }

    public function test_usuario_nao_pode_remover_a_si_mesmo(): void
    {
        $admin = Usuario::factory()->create(['perfil' => 'superadmin']);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/usuarios/{$admin->id}")
            ->assertUnprocessable();
    }
}
