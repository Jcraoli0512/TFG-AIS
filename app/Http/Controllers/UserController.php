<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the artists with search functionality.
     */
    public function indexArtists(Request $request)
    {
        $query = User::where('role', 'artist'); // Filtrar solo por artistas

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Cargar la biografía para mostrar un fragmento en la tarjeta y las obras para la modal
        $artists = $query->with('artworks')->withCount('artworks')->paginate(12); // Paginación y cargar obras para la modal

        // Si es una petición AJAX, devolver JSON
        if ($request->ajax()) {
            $artistsHtml = view('artists._artists_grid', compact('artists'))->render();
            $paginationHtml = $artists->links()->toHtml();
            
            return response()->json([
                'artists' => $artistsHtml,
                'pagination' => $paginationHtml,
                'total' => $artists->total(),
                'current_page' => $artists->currentPage()
            ]);
        }

        return view('artists', compact('artists'));
    }

    /**
     * Display the specified artist and their artworks for API request.
     */
    public function showArtist(User $artist): JsonResponse
    {
        // Asegurarse de que el usuario sea un artista y cargar sus obras
        if ($artist->role !== 'artist') {
            return response()->json(['message' => 'El usuario no es un artista.'], 404);
        }

        // Cargar todas las obras del artista
        $artist->load('artworks');

        // Asegurar que las URLs sean absolutas
        $artist->profile_photo_url = $artist->profile_photo_url ? asset($artist->profile_photo_url) : null;
        
        // Modificar las URLs de las obras para que sean absolutas
        $artist->artworks->transform(function ($artwork) {
            $artwork->image_path = $artwork->image_path ? asset('storage/' . $artwork->image_path) : null;
            return $artwork;
        });

        // Añadir información de depuración
        Log::info('Artist data being sent:', [
            'artist_id' => $artist->id,
            'name' => $artist->name,
            'has_photo' => !empty($artist->profile_photo_url),
            'artworks_count' => $artist->artworks->count()
        ]);

        return response()->json($artist);
    }
} 