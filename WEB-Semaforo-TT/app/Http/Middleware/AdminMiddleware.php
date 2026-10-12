<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->rol === 'Administrador') {
            return $next($request);
        }

        abort(403, 'Acceso denegado. Esta sección es exclusiva para Administradores.');
    }
}