<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsVoluntario
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!$request->user()->isVoluntario()) {
            // Usuário autenticado, mas tipo errado — redireciona para o painel correto
            return redirect()->route('dashboard.ong')
                ->with('error', 'Área restrita para voluntários.');
        }

        return $next($request);
    }
}
