<?php

namespace App\Console\Commands;

use App\Modelos\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Cria (ou promove) um usuário superadmin sem deixar senha no código-fonte.
 *
 * Uso:
 *   php artisan app:criar-superadmin "Manoel Feitosa" manoelbd2012@gmail.com
 *   (a senha é pedida de forma oculta; ou passe --senha=... se preferir)
 */
class CriarSuperadmin extends Command
{
    protected $signature = 'app:criar-superadmin {nome} {email} {--senha=}';

    protected $description = 'Cria ou atualiza um usuário com perfil superadmin.';

    public function handle(): int
    {
        $nome = $this->argument('nome');
        $email = $this->argument('email');
        $senha = $this->option('senha') ?: $this->secret('Senha do superadmin');

        if (! $senha || Str::length($senha) < 6) {
            $this->error('A senha precisa ter ao menos 6 caracteres.');
            return self::FAILURE;
        }

        $usuario = Usuario::updateOrCreate(
            ['email' => $email],
            [
                'nome' => $nome,
                'senha' => Hash::make($senha),
                'perfil' => 'superadmin',
                'ativo' => true,
            ],
        );

        $this->info("Superadmin pronto: {$usuario->nome} <{$usuario->email}> (id {$usuario->id}).");

        return self::SUCCESS;
    }
}
