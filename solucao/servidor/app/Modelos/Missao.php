<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Missao extends Model
{
    use HasFactory;

    protected $table = 'missoes';

    protected $fillable = [
        'colaborador_id',
        'divisao',
        'atividade',
        'ambiente',
        'projeto',
        'latitude',
        'longitude',
        'tempo_exposicao_horas',
        'clima_severo',
        'planejamento_concluido',
        'nivel_risco',
        'classificacao_risco',
    ];

    protected $casts = [
        'clima_severo' => 'boolean',
        'planejamento_concluido' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'tempo_exposicao_horas' => 'float',
        'nivel_risco' => 'integer',
    ];

    public function relatoriosQuaseAcidente(): HasMany
    {
        return $this->hasMany(RelatorioQuaseAcidente::class);
    }

    /** Colaborador (usuário de campo) responsável pela missão. */
    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'colaborador_id');
    }
}
