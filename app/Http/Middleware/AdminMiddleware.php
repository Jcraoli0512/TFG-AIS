<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AdminMiddleware: Verificando acceso');
        
        if (!auth()->check()) {
            Log::info('AdminMiddleware: Usuario no autenticado');
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta sección.');
        }

        $user = auth()->user();
        Log::info('AdminMiddleware: Usuario autenticado', [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active
        ]);

        if (!$user->isAdmin()) {
            Log::info('AdminMiddleware: Usuario no es admin');
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        Log::info('AdminMiddleware: Acceso permitido');
        return $next($request);
    }
} 