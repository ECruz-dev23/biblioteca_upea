<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = Auth::user();

        if (!$usuario || !in_array($usuario->rol, $roles)) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}