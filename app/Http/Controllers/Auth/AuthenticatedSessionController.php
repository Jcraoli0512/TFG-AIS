<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista de inicio de sesión.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Maneja una solicitud de autenticación entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Verificar si hay una URL de redirección específica
        if ($request->has('redirect')) {
            $redirectUrl = $request->input('redirect');
            // Validar que la URL es interna y segura
            if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                $redirectHost = parse_url($redirectUrl, PHP_URL_HOST);
                $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                
                if ($redirectHost === $appHost || $redirectHost === '127.0.0.1' || $redirectHost === 'localhost') {
                    return redirect($redirectUrl);
                }
            }
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destruye una sesión autenticada.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
