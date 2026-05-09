<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AvaliacaoController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DemandaController;
use App\Http\Controllers\Api\InscricaoController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\ONGController;
use App\Http\Controllers\Api\VoluntarioController;
use Illuminate\Support\Facades\Route;

// ─── Autenticação ────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('registro', [AuthController::class, 'registro']);
    Route::post('login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('eu',      [AuthController::class, 'eu']);
    });
});

// ─── Rotas públicas ───────────────────────────────────────────────────────────
Route::get('categorias',                    [CategoriaController::class, 'index']);
Route::get('categorias/{id}',               [CategoriaController::class, 'show']);
Route::get('ongs',                          [ONGController::class, 'index']);
Route::get('ongs/{id}',                     [ONGController::class, 'show']);
Route::get('ongs/{id}/avaliacoes',          [AvaliacaoController::class, 'porOng']);
Route::get('voluntarios/{id}',              [VoluntarioController::class, 'show']);
Route::get('voluntarios/{id}/avaliacoes',   [AvaliacaoController::class, 'porVoluntario']);
Route::get('demandas',                      [DemandaController::class, 'index']);
Route::get('demandas/{id}',                 [DemandaController::class, 'show']);

// ─── Rotas protegidas (Bearer token) ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Categorias (write)
    Route::post('categorias',        [CategoriaController::class, 'store']);
    Route::put('categorias/{id}',    [CategoriaController::class, 'update']);
    Route::delete('categorias/{id}', [CategoriaController::class, 'destroy']);

    // ONG — editar próprio perfil
    Route::put('ongs/{id}', [ONGController::class, 'update']);

    // Voluntário — editar perfil + categorias
    Route::put('voluntarios/{id}',             [VoluntarioController::class, 'update']);
    Route::post('voluntarios/{id}/categorias', [VoluntarioController::class, 'syncCategorias']);

    // Demandas (write)
    Route::post('demandas',               [DemandaController::class, 'store']);
    Route::put('demandas/{id}',           [DemandaController::class, 'update']);
    Route::delete('demandas/{id}',        [DemandaController::class, 'destroy']);
    Route::post('demandas/{id}/publicar', [DemandaController::class, 'publicar']);
    Route::post('demandas/{id}/encerrar', [DemandaController::class, 'encerrar']);

    // Inscrições
    Route::get('inscricoes/minhas',              [InscricaoController::class, 'minhas']);
    Route::get('demandas/{id}/inscricoes',       [InscricaoController::class, 'porDemanda']);
    Route::post('demandas/{id}/inscricoes',      [InscricaoController::class, 'store']);
    Route::post('inscricoes/{id}/aceitar',       [InscricaoController::class, 'aceitar']);
    Route::post('inscricoes/{id}/recusar',       [InscricaoController::class, 'recusar']);
    Route::post('inscricoes/{id}/concluir',      [InscricaoController::class, 'concluir']);
    Route::post('inscricoes/{id}/cancelar',      [InscricaoController::class, 'cancelar']);

    // Avaliações (write)
    Route::post('inscricoes/{id}/avaliacoes', [AvaliacaoController::class, 'store']);

    // Match
    Route::get('match/sugestoes', [MatchController::class, 'sugestoes']);
    Route::get('match/score',     [MatchController::class, 'score']);

    // Dashboard
    Route::get('dashboard/voluntario', [DashboardController::class, 'voluntario']);
    Route::get('dashboard/ong',        [DashboardController::class, 'ong']);
});
