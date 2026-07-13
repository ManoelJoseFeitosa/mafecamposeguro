<?php

namespace App\Modelos;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'perfil',
        'matricula',
        'cargo',
        'telefone',
        'ativo',
    ];

    protected $hidden = [
        'senha',
    ];

    protected $casts = [
        'senha' => 'hashed',
        'ativo' => 'boolean',
    ];

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    /** Missões atribuídas a este colaborador (usuário de campo). */
    public function missoes(): HasMany
    {
        return $this->hasMany(Missao::class, 'colaborador_id');
    }

    public function ehSuperadmin(): bool
    {
        return $this->perfil === 'superadmin';
    }

    public function ehGestor(): bool
    {
        return $this->perfil === 'gestor';
    }

    protected static function newFactory(): UsuarioFactory
    {
        return UsuarioFactory::new();
    }
}
