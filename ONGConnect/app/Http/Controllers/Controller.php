<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'ONGConnect — Match Voluntários e ONGs',
    description: 'API REST para conexão entre ONGs do Alto Vale do Itajaí e voluntários da comunidade. Projeto de Extensão — Unidavi 2026.'
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: 'Servidor local'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token',
    description: 'Token Sanctum — obtenha via POST /api/auth/login'
)]
#[OA\Tag(name: 'Auth',        description: 'Registro, login e logout')]
#[OA\Tag(name: 'Categorias',  description: 'Gerenciamento de categorias')]
#[OA\Tag(name: 'ONGs',        description: 'Perfis de ONGs')]
#[OA\Tag(name: 'Voluntarios', description: 'Perfis de voluntários')]
#[OA\Tag(name: 'Demandas',    description: 'Publicação e consulta de demandas')]
#[OA\Tag(name: 'Inscricoes',  description: 'Inscrição e workflow de participação')]
#[OA\Tag(name: 'Avaliacoes',  description: 'Avaliação mútua após conclusão')]
#[OA\Tag(name: 'Match',       description: 'Sugestões automáticas por score')]
#[OA\Tag(name: 'Dashboard',   description: 'Estatísticas e histórico do usuário')]
abstract class Controller extends BaseController
{
    use AuthorizesRequests;
}