<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\ArtworkDisplayDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::count();
        $totalArtists = User::where('role', 'artist')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalArtists',
            'totalAdmins',
            'recentUsers'
        ));
    }

    /**
     * Muestra un listado de los usuarios.
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Buscar por nombre o email
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrar por rol
        if ($request->has('role')) {
             if (!empty($request->input('role'))) {
                 $query->where('role', $request->input('role'));
             }
        }

        // Filtrar por estado
        if ($request->has('status') && $request->input('status') !== null) {
            if (!empty($request->input('status')) || $request->input('status') === '0') { // Permitir '0' para inactivos
                 $query->where('is_active', $request->input('status'));
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.users._users_table', compact('users'))->render()
            ]);
        }

        // Devolver la vista completa para solicitudes no AJAX
        return view('admin.users.index', compact('users'));
    }

    /**
     * Muestra el formulario para editar el usuario especificado.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function editUser(User $user, Request $request)
    {
        try {
            // Cargar las obras del usuario
            $user->load('artworks');

            // Actualizar last_active_at si es el usuario actual
            if ($user->id === auth()->id()) {
                $user->last_active_at = now();
                $user->save();
            }

            if ($request->ajax()) {
                // Si es una solicitud AJAX, devuelve solo la parte del formulario
                return response()->json([
                    'html' => view('admin.users._edit_form', compact('user'))->render()
                ]);
            }

            // Si es una solicitud normal, devuelve la vista de página completa
            return view('admin.users.edit', compact('user'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error al cargar el formulario: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.users.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'role' => 'required|in:admin,artist',
                'is_active' => 'boolean',
                'biography' => 'nullable|string|max:1000',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'instagram' => 'nullable|url|max:255',
                'twitter' => 'nullable|url|max:255',
                'tiktok' => 'nullable|url|max:255',
                'youtube' => 'nullable|url|max:255',
                'pinterest' => 'nullable|url|max:255',
                'linkedin' => 'nullable|url|max:255'
            ]);

            // Manejar la imagen de perfil si se subió una nueva
            if ($request->hasFile('profile_photo')) {
                // Eliminar la imagen anterior si existe
                if ($user->profile_photo) {
                    Storage::disk('public')->delete($user->profile_photo);
                }
                
                // Guardar la nueva imagen
                $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
                $validated['profile_photo'] = $profilePhotoPath;
            }

            $user->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Usuario actualizado correctamente',
                    'user' => $user->fresh()
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado correctamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.users.index')
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function deleteUser(User $user, Request $request)
    {
        try {
            if ($user->id === auth()->id()) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'No puedes eliminar tu propia cuenta'
                    ], 403);
                }
                return redirect()->route('admin.users.index')
                    ->with('error', 'No puedes eliminar tu propia cuenta');
            }

            $user->delete();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Usuario eliminado correctamente'
                ]);
            }

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.users.index')
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una obra de arte del perfil de un usuario.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function deleteUserArtwork(User $user, Artwork $artwork, Request $request)
    {
        try {
            // Verificar que la obra pertenece al usuario
            if ($artwork->user_id !== $user->id) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'La obra no pertenece a este usuario'
                    ], 403);
                }
                return redirect()->back()
                    ->with('error', 'La obra no pertenece a este usuario');
            }

            // Eliminar la imagen si existe
            if ($artwork->image_path) {
                $path = str_replace('/storage/', '', $artwork->image_path);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            $artwork->delete();

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Obra eliminada correctamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Obra eliminada correctamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error al eliminar la obra: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar la obra: ' . $e->getMessage());
        }
    }

    /**
     * Elimina la imagen panorámica de un usuario.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function deletePanoramicImage(User $user, Request $request)
    {
        try {
            if (!$user->panoramic_image) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'El usuario no tiene imagen panorámica'
                    ], 404);
                }
                return redirect()->back()
                    ->with('error', 'El usuario no tiene imagen panorámica');
            }

            // Eliminar la imagen del almacenamiento
            $path = str_replace('/storage/', '', $user->panoramic_image);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $user->update(['panoramic_image' => null]);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Imagen panorámica eliminada correctamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Imagen panorámica eliminada correctamente');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Error al eliminar la imagen panorámica: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al eliminar la imagen panorámica: ' . $e->getMessage());
        }
    }

    /**
     * Muestra las solicitudes de exhibición.
     *
     * @return \Illuminate\View\View
     */
    public function exhibitionRequests(): View
    {
        // Agrupar las solicitudes por usuario y fecha de exhibición
        $requestsGrouped = ArtworkDisplayDate::with('artwork.user')
            ->where('is_approved', false)
            ->get()
            ->groupBy(function ($date) {
                return $date->user_id . '-' . $date->display_date->format('Y-m-d');
            });

        // Log para depuración
        Log::info('Solicitudes de exhibición agrupadas', ['count' => $requestsGrouped->count(), 'keys' => $requestsGrouped->keys()->all()]);

        return view('admin.exhibition-requests.index', compact('requestsGrouped'));
    }

    /**
     * Aprueba una solicitud de exhibición específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRequest($id)
    {
        try {
            $displayDate = ArtworkDisplayDate::findOrFail($id);
            $displayDate->update(['is_approved' => true]);
            return response()->json(['message' => 'Solicitud aprobada correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al aprobar la solicitud:', [
                'request_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al aprobar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rechaza una solicitud de exhibición específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequest($id)
    {
        try {
            $displayDate = ArtworkDisplayDate::findOrFail($id);
            $displayDate->delete(); // Eliminar la solicitud al rechazarla
            return response()->json(['message' => 'Solicitud rechazada correctamente.']);
        } catch (\Exception $e) {
            Log::error('Error al rechazar la solicitud:', [
                'request_id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al rechazar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Aprueba un lote de solicitudes de exhibición para un usuario y fecha específicos.
     *
     * @param  int  $userId
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveBatch($userId, $date)
    {
        try {
            $parsedDate = Carbon::parse($date)->format('Y-m-d');

            // Asegurarse de que no hay exhibiciones aprobadas para esta fecha
            $existingApproved = ArtworkDisplayDate::where('display_date', $parsedDate)
                ->where('is_approved', true)
                ->exists();

            // Primero, verificar si existen las solicitudes
            $existingRequests = ArtworkDisplayDate::where('user_id', $userId)
                ->where('is_approved', false)
                ->get();

            \Log::info('Solicitudes existentes:', [
                'count' => $existingRequests->count(),
                'requests' => $existingRequests->map(function($req) {
                    return [
                        'id' => $req->id,
                        'user_id' => $req->user_id,
                        'display_date' => $req->display_date,
                        'is_approved' => $req->is_approved
                    ];
                })->toArray()
            ]);
            
            // Encontrar todas las solicitudes pendientes para este usuario y fecha
            $requestsToApprove = ArtworkDisplayDate::where('user_id', $userId)
                ->whereRaw('DATE(display_date) = ?', [$parsedDate])
                ->where('is_approved', false)
                ->get();

            \Log::info('Solicitudes a aprobar:', [
                'count' => $requestsToApprove->count(),
                'sql' => ArtworkDisplayDate::where('user_id', $userId)
                    ->whereRaw('DATE(display_date) = ?', [$parsedDate])
                    ->where('is_approved', false)
                    ->toSql()
            ]);

            if ($requestsToApprove->isEmpty()) {
                \Log::warning('No se encontraron solicitudes pendientes para el lote');
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron solicitudes pendientes para este lote.'
                ], 404);
            }

            // Actualizar todas las solicitudes a aprobadas
            foreach ($requestsToApprove as $request) {
                $request->update(['is_approved' => true]);
                \Log::info('Solicitud actualizada:', [
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'display_date' => $request->display_date
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lote de solicitudes aprobado correctamente'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en approveBatch: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el lote de solicitudes: ' . $e->getMessage(),
                'debug_info' => [
                    'userId' => $userId,
                    'date' => $date,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Rechaza un lote de solicitudes de exhibición para un usuario y fecha específicos.
     *
     * @param  int  $userId
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectBatch($userId, $date)
    {
        try {
            // Convertir la fecha al formato correcto
            $parsedDate = Carbon::parse($date)->format('Y-m-d');
            
            // Encontrar y eliminar todas las solicitudes pendientes para este usuario y fecha
            $deletedCount = ArtworkDisplayDate::where('user_id', $userId)
                ->whereRaw('DATE(display_date) = ?', [$parsedDate])
                ->where('is_approved', false)
                ->delete();

            Log::info('Lote de solicitudes rechazado correctamente.', ['user_id' => $userId, 'date' => $parsedDate]);
            return response()->json(['message' => 'Lote de solicitudes rechazado correctamente.']);
        } catch (\Exception $e) {
            \Log::error('Error en rejectBatch: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el lote de solicitudes: ' . $e->getMessage(),
                'debug_info' => [
                    'userId' => $userId,
                    'date' => $date,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Obtiene las obras de arte para un grupo específico de solicitudes de exhibición.
     *
     * @param  string  $groupKey  Formatted as 'userId-displayDate'
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupArtworks($groupKey)
    {
        try {
            list($userId, $date) = explode('-', $groupKey);
            $parsedDate = Carbon::parse($date)->format('Y-m-d');

            $artworks = ArtworkDisplayDate::where('user_id', $userId)
                ->where('display_date', $parsedDate)
                ->with('artwork')
                ->get()
                ->map(function ($displayDate) {
                    return [
                        'id' => $displayDate->artwork->id,
                        'title' => $displayDate->artwork->title,
                        'image_url' => $displayDate->artwork->image_path ? asset('storage/' . $displayDate->artwork->image_path) : asset('img/placeholder.jpg'),
                        'display_date_id' => $displayDate->id // ID de la solicitud de exhibición
                    ];
                });

            Log::info('Obras de arte del grupo obtenidas correctamente.', ['groupKey' => $groupKey, 'count' => $artworks->count()]);
            return response()->json($artworks);
        } catch (\Exception $e) {
            Log::error('Error al obtener obras de arte del grupo:', [
                'groupKey' => $groupKey,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Error al obtener obras de arte del grupo: ' . $e->getMessage()], 500);
        }
    }
} 