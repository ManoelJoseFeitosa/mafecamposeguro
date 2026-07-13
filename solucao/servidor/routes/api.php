<?php

use App\Http\Controladores\AutenticacaoControlador;
use App\Http\Controladores\CatalogoControlador;
use App\Http\Controladores\IndicadorControlador;
use App\Http\Controladores\MissaoControlador;
use App\Http\Controladores\RqaControlador;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas da API — CPSI 2026 (Segurança nas Saídas de Campo)
|--------------------------------------------------------------------------
| Prefixo /api já é aplicado automaticamente pelo Laravel para este arquivo.
| Nomes de rota e caminhos em português, conforme convenção do projeto
| (ver CLAUDE.md, seção "Convenções de nomenclatura").
*/

Route::get('/catalogo/divisoes', [CatalogoControlador::class, 'divisoes'])->name('catalogo.divisoes');
Route::get('/catalogo/atividades', [CatalogoControlador::class, 'atividades'])->name('catalogo.atividades');
Route::get('/catalogo/ambientes', [CatalogoControlador::class, 'ambientes'])->name('catalogo.ambientes');

Route::get('/missoes', [MissaoControlador::class, 'listar'])->name('missoes.listar');
Route::post('/missoes', [MissaoControlador::class, 'armazenar'])->name('missoes.armazenar');
Route::get('/missoes/{missao}/relatorio.pdf', [MissaoControlador::class, 'relatorioPdf'])->name('missoes.relatorio-pdf');
Route::get('/missoes/{missao}/relatorio.docx', [MissaoControlador::class, 'relatorioDocx'])->name('missoes.relatorio-docx');

Route::post('/rqa', [RqaControlador::class, 'armazenar'])->name('rqa.armazenar');

// Login público; demais rotas de autenticação e o painel de indicadores (uso
// exclusivo do gestor) exigem token Sanctum. Criação/sincronização de missões
// e RQA permanecem públicas de propósito: são o caminho usado pelo app móvel
// offline-first (colaborador em campo, sem sessão de gestor) — ver CLAUDE.md.
Route::post('/login', [AutenticacaoControlador::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AutenticacaoControlador::class, 'logout'])->name('logout');
    Route::get('/usuario-autenticado', [AutenticacaoControlador::class, 'usuarioAutenticado'])->name('usuario-autenticado');
    Route::get('/indicadores', [IndicadorControlador::class, 'exibir'])->name('indicadores.exibir');
});
