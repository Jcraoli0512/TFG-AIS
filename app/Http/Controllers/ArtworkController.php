<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ArtworkController extends Controller
{
    /**
     * Mostrar un listado del recurso.
     */
    public function index()
    {
        //
    }

    /**
     * Mostrar el formulario para crear un nuevo recurso.
     */
    public function create(Request $request)
    {
        // Verificar autenticación manualmente
        if (!auth()->check()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No autenticado'], 401);
            }
            return redirect()->route('login');
        }

        if ($request->ajax()) {
            return view('artworks._create_form');
        }

        // Fallback para solicitudes no AJAX (opcional, puede redirigir o mostrar página completa)
        // return view('artworks.create');
    }

    /**
     * Almacenar un recurso recién creado en el almacenamiento.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'technique' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'collection_id' => 'nullable|exists:collections,id'
        ]);

        // Guardar la imagen si se proporcionó una
        $imagePath = null;
        if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('artworks', 'public');
        }

        // Crear la obra
        $artwork = Artwork::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'technique' => $validated['technique'] ?? null,
            'year' => $validated['year'] ?? null,
            'image_path' => $imagePath,
            'collection_id' => $validated['collection_id'] ?? null
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Obra creada exitosamente',
                'artwork' => $artwork
            ]);
        }

        return redirect()->route('profile.show', ['user' => Auth::user()])
            ->with('success', 'Obra creada exitosamente');
    }

    /**
     * Mostrar el recurso especificado.
     */
    public function show(Artwork $artwork)
    {
        // Generar URL de imagen que funcione para todos los usuarios
        $imageUrl = null;
        if ($artwork->image_path) {
            // Usar asset() para generar URL pública que funcione para invitados
            $imageUrl = asset('storage/' . $artwork->image_path);
        }

        return response()->json([
            'title' => $artwork->title,
            'description' => $artwork->description,
            'technique' => $artwork->technique,
            'year' => $artwork->year,
            'image_url' => $imageUrl,
            'is_owner' => Auth::check() && Auth::id() === $artwork->user_id
        ]);
    }

    /**
     * Mostrar el formulario para editar el recurso especificado.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualizar el recurso especificado en el almacenamiento.
     */
    public function update(Request $request, Artwork $artwork)
    {
        // Verificar si el usuario autenticado es el propietario de la obra
        if ($artwork->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para editar esta obra.'], 403);
        }

        $validated = $request->validate([
            'technique' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'description' => 'nullable|string',
        ]);

        $artwork->update([
            'technique' => $validated['technique'] ?? $artwork->technique,
            'year' => $validated['year'] ?? $artwork->year,
            'description' => $validated['description'] ?? $artwork->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Obra actualizada correctamente.']);
    }

    /**
     * Eliminar el recurso especificado del almacenamiento.
     */
    public function destroy(Artwork $artwork)
    {
        // Verificar que el usuario es el propietario de la obra
        if ($artwork->user_id !== Auth::id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar esta obra'], 403);
            }
            return redirect()->back()
                ->with('error', 'No tienes permiso para eliminar esta obra');
        }

        try {
            // Verificar si la obra está siendo exhibida
            $hasExhibitions = $artwork->displayDates()->exists();
            if ($hasExhibitions) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'No se puede eliminar esta obra porque está siendo exhibida. Primero debes cancelar todas sus exhibiciones.'
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', 'No se puede eliminar esta obra porque está siendo exhibida. Primero debes cancelar todas sus exhibiciones.');
            }

            // Eliminar la imagen del almacenamiento
            if ($artwork->image_path) {
                Storage::disk('public')->delete($artwork->image_path);
            }

            // Eliminar la obra
            $artwork->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Obra eliminada correctamente']);
            }

            return redirect()->back()
                ->with('success', 'Obra eliminada correctamente');
        } catch (\Exception $e) {
            // Si es un error de integridad referencial, mostrar mensaje específico
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $message = 'No se puede eliminar esta obra porque está siendo exhibida. Primero debes cancelar todas sus exhibiciones.';
            } else {
                $message = 'Error al eliminar la obra: ' . $e->getMessage();
            }

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return redirect()->back()
                ->with('error', $message);
        }
    }

    // Nuevo método para obtener la vista parcial de selección de obras
    public function getArtworkSelectionPartial()
    {
        try {
        $user = Auth::user();
            if (!$user) {
                Log::error('ArtworkController@getArtworkSelectionPartial: Usuario no autenticado');
                return response()->json(['message' => 'Usuario no autenticado.'], 401);
            }
        $artworks = $user->artworks;
        Log::info('ArtworkController@getArtworkSelectionPartial: Obras encontradas para el usuario', ['user_id' => $user->id, 'count' => $artworks->count()]);
            
            // Verificar si el usuario tiene obras
            if ($artworks->isEmpty()) {
                 Log::info('ArtworkController@getArtworkSelectionPartial: Usuario sin obras disponibles', ['user_id' => $user->id]);
                 // Retornar la vista parcial incluso si está vacía, la vista maneja el caso @empty
            }
            
        return view('artworks._artwork_selection_partial', compact('artworks'));
        } catch (\Exception $e) {
            Log::error('ArtworkController@getArtworkSelectionPartial: Error al obtener obras', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            // Retornar un error genérico al frontend
            return response()->json(['message' => 'Error interno del servidor al cargar las obras.'], 500);
        }
    }

    /**
     * Mostrar una galería de obras de arte aleatorias.
     *
     * @return \Illuminate\View\View
     */
    public function indexGallery(): View
    {
        $artworks = Artwork::with('user')
                        ->inRandomOrder()
                        ->get();

        return view('gallery', compact('artworks'));
    }

    /**
     * Obtener una selección aleatoria de obras de arte.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function randomArtworks()
    {
        $artworks = Artwork::with('user') // Cargar la relación con el usuario (artista)
                        ->inRandomOrder()
                        ->get()
                        ->map(function ($artwork) {
                            return [
                                'id' => $artwork->id,
                                'title' => $artwork->title,
                                'description' => $artwork->description,
                                'technique' => $artwork->technique,
                                'year' => $artwork->year,
                                'image_url' => Storage::url($artwork->image_path),
                                'artist_name' => $artwork->user->name, // Nombre del artista
                                'artist_id' => $artwork->user->id, // ID del artista
                            ];
                        });

        return response()->json($artworks);
    }

    /**
     * Verificar si una obra está siendo exhibida
     */
    public function checkExhibitions(Artwork $artwork)
    {
        $hasExhibitions = $artwork->displayDates()->exists();
        
        return response()->json([
            'has_exhibitions' => $hasExhibitions
        ]);
    }
}
