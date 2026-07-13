<?php

namespace App\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RQA — Relatório de Quase Acidente. Alimenta o indicador contratual "Número de
 * não conformidades" definido na Ficha Técnica do Desafio (Anexo II).
 */
class RelatorioQuaseAcidente extends Model
{
    protected $table = 'relatorios_quase_acidente';

    protected $fillable = ['missao_id', 'descricao'];

    public function missao(): BelongsTo
    {
        return $this->belongsTo(Missao::class);
    }
}
