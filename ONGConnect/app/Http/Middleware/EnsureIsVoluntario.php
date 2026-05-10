<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsVoluntario
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isVoluntario()) {
            return redirect()->route('login')
                ->with('error', 'Acesso exclusivo para voluntários.');
        }

        return $next($request);
    }
}
