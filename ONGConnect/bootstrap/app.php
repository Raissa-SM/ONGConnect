<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Não adicionamos EnsureFrontendRequestsAreStateful aqui.
        // A API usa exclusivamente Bearer tokens (Sanctum),
        // sem autenticação baseada em cookie/sessão.
        // Adicionar esse middleware causaria erro 419 (CSRF) no Swagger e Postman.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn ($request, $e) => $request->is('api/*'));
    })->create();
