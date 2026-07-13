<?php

namespace Database\Seeders;

use App\Modelos\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco de dados de demonstração com um usuário gestor padrão
     * (credenciais só para o protótipo local — trocar antes de qualquer uso
     * real, ver solucao/servidor/README.md).
     */
    public function run(): void
    {
        Usuario::firstOrCreate(
            ['email' => 'gestor@cpsi2026.local'],
            [
                'nome' => 'Gestor de Demonstração',
                'senha' => Hash::make('cpsi2026-demo'),
                'perfil' => 'gestor',
            ]
        );
    }
}
