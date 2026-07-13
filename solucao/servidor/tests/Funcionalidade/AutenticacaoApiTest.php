<?php

namespace Tests\Funcionalidade;

use App\Modelos\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AutenticacaoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_com_credenciais_validas_retorna_token(): void
    {
        Usuario::factory()->create([
            'email' => 'gestor@teste.local',
            'senha' => Hash::make('senha-correta'),
            'perfil' => 'gestor',
        ]);

        $resposta = $this->postJson('/api/login', [
            'email' => 'gestor@teste.local',
            'senha' => 'senha-correta',
        ]);

        $resposta->assertOk();
        $resposta->assertJsonStructure(['token', 'usuario' => ['id', 'nome', 'email', 'perfil']]);
    }

    public function test_login_com_senha_incorreta_e_rejeitado(): void
    {
        Usuario::factory()->create([
            'email' => 'gestor@teste.local',
            'senha' => Hash::make('senha-correta'),
        ]);

        $resposta = $this->postJson('/api/login', [
            'email' => 'gestor@teste.local',
            'senha' => 'senha-errada',
        ]);

        $resposta->assertUnprocessable();
    }

    public function test_logout_revoga_o_token_atual(): void
    {
        $usuario = Usuario::factory()->create();
        $token = $usuario->createToken('teste')->plainTextToken;

        $resposta = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/logout');

        $resposta->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
