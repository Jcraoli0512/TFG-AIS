<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        Log::info('ProfileController@edit called', [
            'isAjax' => $request->ajax(),
            'url' => $request->url(),
            'method' => $request->method(),
            'user' => $request->user() ? $request->user()->id : 'not authenticated'
        ]);

        return view('profile._edit_form_partial', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            \Log::info('Iniciando actualización de perfil', ['user_id' => $user->id]);

            // Actualizar foto de perfil si se proporciona una nueva
            if ($request->hasFile('profile_photo')) {
                \Log::info('Nueva foto de perfil detectada');
                
                // Eliminar foto anterior si existe
                if ($user->profile_photo) {
                    \Log::info('Eliminando foto anterior', ['old_photo' => $user->profile_photo]);
                    Storage::disk('public')->delete($user->profile_photo);
                }

                // Guardar nueva foto
                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                \Log::info('Nueva foto guardada', ['path' => $path]);
                $user->profile_photo = $path;
            }

            // Actualizar otros campos
            $user->name = $request->name;
            $user->email = $request->email;
            $user->biography = $request->biography;

            // Si el email cambió, resetear la verificación
            if ($user->isDirty('email')) {
                \Log::info('Email cambiado, reseteando verificación');
                $user->email_verified_at = null;
            }

            $user->save();
            \Log::info('Perfil actualizado exitosamente');

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar perfil', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar el perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
