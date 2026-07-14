<?php

namespace Database\Seeders;

use App\Modelos\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco de demonstração com os três perfis do sistema
     * (superadmin > gestor > colaborador). Credenciais só para o protótipo
     * local — trocar antes de qualquer uso real (ver solucao/servidor/README.md).
     */
    public function run(): void
    {
        Usuario::firstOrCreate(
            ['email' => 'manoelbd2012@gmail.com'],
            [
                'nome' => 'Administrador Geral',
                'senha' => Hash::make('Mf@871277'),
                'perfil' => 'superadmin',
                'ativo' => true,
            ],
        );

        Usuario::firstOrCreate(
            ['email' => 'gestor@cpsi2026.local'],
            [
                'nome' => 'Gestor de Demonstração',
                'senha' => Hash::make('cpsi2026-demo'),
                'perfil' => 'gestor',
                'ativo' => true,
            ],
        );

        // Colaboradores (usuários de campo) de demonstração — aparecem no seletor
        // do app móvel e podem receber missões atribuídas pelo gestor. A senha
        // existe só para satisfazer o schema; o app de campo não faz login.
        $colaboradores = [
            ['nome' => 'Ana Ribeiro', 'email' => 'ana.ribeiro@cpsi2026.local', 'matricula' => 'SGB-0001', 'cargo' => 'Geóloga'],
            ['nome' => 'Carlos Menezes', 'email' => 'carlos.menezes@cpsi2026.local', 'matricula' => 'SGB-0002', 'cargo' => 'Técnico de Campo'],
            ['nome' => 'João Pereira', 'email' => 'joao.pereira@cpsi2026.local', 'matricula' => 'SGB-0003', 'cargo' => 'Hidrólogo'],
        ];

        foreach ($colaboradores as $dados) {
            Usuario::firstOrCreate(
                ['email' => $dados['email']],
                [
                    'nome' => $dados['nome'],
                    'senha' => Hash::make('colaborador-demo'),
                    'perfil' => 'colaborador',
                    'matricula' => $dados['matricula'],
                    'cargo' => $dados['cargo'],
                    'ativo' => true,
                ],
            );
        }
    }
}
