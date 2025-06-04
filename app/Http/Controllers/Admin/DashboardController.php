<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\ArtworkDisplayDate;

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
     * Display a listing of the users.
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
             if (!empty($request->input('role'))) {
                 $query->where('role', $request->input('role'));
             }
        }

        // Filter by status
        if ($request->has('status') && $request->input('status') !== null) {
            if (!empty($request->input('status')) || $request->input('status') === '0') { // Allow '0' for inactive
                 $query->where('is_active', $request->input('status'));
            }
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.users._users_table', compact('users'))->render()
            ]);
        }

        // Return the full view for non-AJAX requests
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
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
                // If it's an AJAX request, return only the form partial
                return response()->json([
                    'html' => view('admin.users._edit_form', compact('user'))->render()
                ]);
            }

            // If it's a regular request, return the full page view
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
                'is_active' => 'boolean'
            ]);

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
     * Delete an artwork from a user's profile.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Artwork  $artwork
     * @return \Illuminate\Http\RedirectResponse
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
     * Delete a user's panoramic image.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
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

            // Actualizar el usuario
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
     * Display a listing of exhibition requests.
     *
     * @return \Illuminate\View\View
     */
    public function exhibitionRequests()
    {
        // Obtener todas las solicitudes de exhibición pendientes de aprobación
        $requests = ArtworkDisplayDate::with(['artwork', 'user'])
            ->where('is_approved', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.exhibition-requests.index', compact('requests'));
    }

    /**
     * Approve an exhibition request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRequest($id)
    {
        try {
            $request = ArtworkDisplayDate::findOrFail($id);
            $request->update(['is_approved' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitud aprobada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
} 