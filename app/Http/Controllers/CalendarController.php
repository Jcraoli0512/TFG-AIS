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
        
        // Obtener las fechas de exhibición aprobadas para esta fecha
        $displayDates = ArtworkDisplayDate::with(['artwork.user'])
            ->where('display_date', $date)
            ->where('is_approved', true)
            ->get();

        $images = $displayDates->map(function ($displayDate) {
            return [
                'id' => $displayDate->artwork->id,
                'title' => $displayDate->artwork->title,
                'url' => $displayDate->artwork->image_path ? asset('storage/' . $displayDate->artwork->image_path) : asset('img/placeholder.jpg'),
                'artist' => $displayDate->artwork->user->name,
                'artist_id' => $displayDate->artwork->user->id,
                'artist_biography' => $displayDate->artwork->user->biography,
                'description' => $displayDate->artwork->description,
                'technique' => $displayDate->artwork->technique,
                'is_owner' => Auth::check() && Auth::id() === $displayDate->artwork->user_id,
                'is_admin' => Auth::check() && Auth::user()->isAdmin(),
                'display_date_id' => $displayDate->id
            ];
        });

        return response()->json($images);
    }
} 