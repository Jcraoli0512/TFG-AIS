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
            Log::info('Iniciando actualización de perfil', ['user_id' => $user->id]);

            // Actualizar foto de perfil si se proporciona una nueva
            if ($request->hasFile('profile_photo')) {
                Log::info('Nueva foto de perfil detectada');
                
                // Eliminar foto anterior si existe
                if ($user->profile_photo) {
                    Log::info('Eliminando foto anterior', ['old_photo' => $user->profile_photo]);
                    Storage::disk('public')->delete($user->profile_photo);
                }

                // Guardar nueva foto
                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                Log::info('Nueva foto guardada', ['path' => $path]);
                $user->profile_photo = $path;
            }

            // Actualizar campos básicos
            $user->name = $request->name;
            $user->email = $request->email;
            $user->biography = $request->biography;

            // Actualizar redes sociales
            $user->instagram = $request->instagram;
            $user->twitter = $request->twitter;
            $user->tiktok = $request->tiktok;
            $user->youtube = $request->youtube;
            $user->pinterest = $request->pinterest;
            $user->linkedin = $request->linkedin;

            // Si el email cambió, resetear la verificación
            if ($user->isDirty('email')) {
                Log::info('Email cambiado, reseteando verificación');
                $user->email_verified_at = null;
            }

            $user->save();
            Log::info('Perfil actualizado exitosamente');

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil', [
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

    public function updatePanoramic(Request $request)
    {
        try {
            Log::info('Iniciando actualización de imagen panorámica');
            
            $request->validate([
                'panoramic_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            ]);

            $user = $request->user();
            Log::info('Usuario autenticado', ['user_id' => $user->id]);

            // Eliminar la imagen anterior si existe
            if ($user->panoramic_image) {
                Log::info('Eliminando imagen anterior', ['path' => $user->panoramic_image]);
                $oldPath = str_replace('/storage/', '', $user->panoramic_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Guardar la nueva imagen
            $path = $request->file('panoramic_image')->store('panoramic-images', 'public');
            Log::info('Nueva imagen guardada', ['path' => $path]);
            
            $user->update([
                'panoramic_image' => $path
            ]);
            Log::info('Base de datos actualizada con nueva ruta');

            return response()->json([
                'message' => 'Imagen panorámica actualizada correctamente',
                'image_url' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar imagen panorámica', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar la imagen panorámica: ' . $e->getMessage()
            ], 500);
        }
    }
}
