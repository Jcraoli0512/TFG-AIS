<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
use App\Models\ArtworkDisplayDate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function getEvents()
    {
        // Obtener todas las fechas de exhibición aprobadas
        $displayDates = ArtworkDisplayDate::with(['artwork', 'user'])
            ->where('is_approved', true)
            ->get()
            ->map(function ($date) {
                return [
                    'id' => 'display_' . $date->id,
                    'title' => $date->artwork->title . ' - ' . $date->user->name,
                    'start' => $date->display_date,
                    'end' => $date->display_date,
                    'color' => '#4CAF50', // Color verde para las fechas aprobadas
                    'textColor' => '#000000',
                    'display' => 'block', // Cambiado de 'background' a 'block' para mostrar el título
                    'description' => 'Obra: ' . $date->artwork->title . "\n" .
                                   'Artista: ' . $date->user->name . "\n" .
                                   'Técnica: ' . $date->artwork->technique
                ];
            });

        return response()->json($displayDates);
    }

    public function getGalleryImages($date)
    {
        $date = Carbon::parse($date);
        
        // Obtener solo las obras seleccionadas para esta fecha (espacio 3D)
        $selectedArtworks = Artwork::whereHas('displayDates', function ($query) use ($date) {
            $query->where('display_date', $date)
                  ->where('is_approved', true);
        })->with('user')->get();

        $images = $selectedArtworks->map(function ($artwork) {
            return [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'url' => $artwork->image_path ? asset('storage/' . $artwork->image_path) : asset('img/placeholder.jpg'),
                'artist' => $artwork->user->name,
                'description' => $artwork->description,
                'technique' => $artwork->technique,
                'is_owner' => Auth::check() && Auth::id() === $artwork->user_id,
                'display_date_id' => $artwork->displayDates->where('display_date', $artwork->pivot->display_date ?? Carbon::now()->toDateString())->first()->id ?? null
            ];
        });

        return response()->json($images);
    }
} 