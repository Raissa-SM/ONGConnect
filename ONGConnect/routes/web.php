<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AvaliacaoController;
use App\Http\Controllers\Web\DemandaController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\InscricaoController;
use App\Http\Controllers\Web\MatchController;
use App\Http\Controllers\Web\ONGController;
use App\Http\Controllers\Web\PerfilController;
use App\Http\Controllers\Web\VoluntarioController;
use Illuminate\Support\Facades\Route;

// ── Públicas ───────────────────────────────────────────────────────────────
Route::get('/',              [HomeController::class,   'index'])->name('home');
Route::get('/demandas',      [DemandaController::class, 'index'])->name('demandas.index');
Route::get('/demandas/{id}', [DemandaController::class, 'show'])->name('demandas.show');
Route::get('/ongs',          [ONGController::class,    'index'])->name('ongs.index');
Route::get('/ongs/{id}',     [ONGController::class,    'show'])->name('ongs.show');
Route::get('/voluntarios/{id}', [VoluntarioController::class, 'show'])->name('voluntarios.show');

// ── Auth ───────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
    Route::post('/registro',[AuthController::class, 'registro']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Voluntário ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'voluntario'])->group(function () {
    Route::get('/dashboard',                        [DashboardController::class, 'voluntario'])->name('dashboard.voluntario');
    Route::get('/perfil',                           [PerfilController::class,    'voluntario'])->name('perfil.voluntario');
    Route::put('/perfil',                           [PerfilController::class,    'atualizarVoluntario'])->name('perfil.voluntario.update');
    Route::get('/inscricoes',                       [InscricaoController::class, 'minhas'])->name('inscricoes.minhas');
    Route::post('/demandas/{id}/inscrever',         [InscricaoController::class, 'store'])->name('inscricoes.store');
    Route::post('/inscricoes/{id}/cancelar',        [InscricaoController::class, 'cancelar'])->name('inscricoes.cancelar');
    Route::get('/match',                            [MatchController::class,     'sugestoes'])->name('match.sugestoes');
});

// ── ONG ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'ong'])->group(function () {
    Route::get('/dashboard/ong',                    [DashboardController::class, 'ong'])->name('dashboard.ong');
    Route::get('/perfil/ong',                       [PerfilController::class,    'ong'])->name('perfil.ong');
    Route::put('/perfil/ong',                       [PerfilController::class,    'atualizarOng'])->name('perfil.ong.update');
    Route::get('/minhas-demandas',                  [DemandaController::class,   'minhas'])->name('demandas.minhas');
    Route::get('/minhas-demandas/criar',            [DemandaController::class,   'criar'])->name('demandas.criar');
    Route::post('/minhas-demandas',                 [DemandaController::class,   'store'])->name('demandas.store');
    Route::get('/minhas-demandas/{id}/editar',      [DemandaController::class,   'editar'])->name('demandas.editar');
    Route::put('/minhas-demandas/{id}',             [DemandaController::class,   'update'])->name('demandas.update');
    Route::delete('/minhas-demandas/{id}',          [DemandaController::class,   'destroy'])->name('demandas.destroy');
    Route::post('/minhas-demandas/{id}/publicar',   [DemandaController::class,   'publicar'])->name('demandas.publicar');
    Route::post('/minhas-demandas/{id}/concluir',   [DemandaController::class,   'concluirTodas'])->name('demandas.concluir');
    Route::get('/minhas-demandas/{id}/inscricoes',  [InscricaoController::class, 'porDemanda'])->name('inscricoes.demanda');
    Route::post('/inscricoes/{id}/aceitar',         [InscricaoController::class, 'aceitar'])->name('inscricoes.aceitar');
    Route::post('/inscricoes/{id}/recusar',         [InscricaoController::class, 'recusar'])->name('inscricoes.recusar');
    Route::post('/inscricoes/{id}/concluir',        [InscricaoController::class, 'concluir'])->name('inscricoes.concluir');
});

// ── Avaliação (ambos autenticados) ─────────────────────────────────────────
Route::post('/inscricoes/{id}/avaliar', [AvaliacaoController::class, 'store'])
    ->name('avaliacoes.store')
    ->middleware('auth');
