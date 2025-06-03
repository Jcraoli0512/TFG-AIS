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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            return view('artworks._create_form')->render();
        }

        // Fallback for non-AJAX request (optional, can redirect or show full page)
        // return view('artworks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technique' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB
            'collection_id' => 'nullable|exists:collections,id'
        ]);

        // Guardar la imagen
        $imagePath = $request->file('image')->store('artworks', 'public');

        // Crear la obra
        $artwork = Artwork::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'technique' => $validated['technique'],
            'year' => $validated['year'],
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
     * Display the specified resource.
     */
    public function show(Artwork $artwork)
    {
        return response()->json([
            'title' => $artwork->title,
            'description' => $artwork->description,
            'technique' => $artwork->technique,
            'year' => $artwork->year,
            'image_url' => Storage::url($artwork->image_path)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        // Verificar que el usuario es el propietario de la obra
        if ($artwork->user_id !== Auth::id()) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para eliminar esta obra');
        }

        // Eliminar la imagen del almacenamiento
        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }

        // Eliminar la obra
        $artwork->delete();

        return redirect()->back()
            ->with('success', 'Obra eliminada correctamente');
    }

    // Nuevo método para obtener la vista parcial de selección de obras
    public function getArtworkSelectionPartial()
    {
        $user = Auth::user();
        $artworks = $user->artworks;
        Log::info('ArtworkController@getArtworkSelectionPartial: Obras encontradas para el usuario', ['user_id' => $user->id, 'count' => $artworks->count()]);
        return view('artworks._artwork_selection_partial', compact('artworks'));
    }
}
