<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsOng
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->isOng()) {
            // Usuário autenticado, mas tipo errado — redireciona para o painel correto
            return redirect()->route('dashboard.voluntario')
                ->with('error', 'Área restrita para ONGs.');
        }

        return $next($request);
    }
}
