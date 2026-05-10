<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsOng
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isOng()) {
            return redirect()->route('login')
                ->with('error', 'Acesso exclusivo para ONGs.');
        }

        return $next($request);
    }
}
