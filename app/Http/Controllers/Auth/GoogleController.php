<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        // Guardar la URL de redirección en la sesión si está presente
        if ($request->has('redirect')) {
            session(['redirect_after_login' => $request->input('redirect')]);
        }
        
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => bcrypt(Str::random(24))
                ]
            );

            Auth::login($user);

            // Verificar si hay una URL de redirección guardada en la sesión
            if (session()->has('redirect_after_login')) {
                $redirectUrl = session('redirect_after_login');
                session()->forget('redirect_after_login');
                
                // Validar que la URL es interna y segura
                if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                    $redirectHost = parse_url($redirectUrl, PHP_URL_HOST);
                    $appHost = parse_url(config('app.url'), PHP_URL_HOST);
                    
                    if ($redirectHost === $appHost || $redirectHost === '127.0.0.1' || $redirectHost === 'localhost') {
                        return redirect($redirectUrl);
                    }
                }
            }

            return redirect()->intended(route('home'));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con Google');
        }
    }
} 