<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
use App\Models\ArtworkDisplayDate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function getEvents()
    {
        $exhibitions = Exhibition::where('is_public', true)
            ->where('status', 'approved')
            ->get()
            ->map(function ($exhibition) {
                return [
                    'id' => 'exhibition_' . $exhibition->id,
                    'title' => $exhibition->title,
                    'start' => $exhibition->start_date,
                    'end' => $exhibition->end_date,
                    'url' => '#',
                    'description' => $exhibition->description,
                    'type' => 'exhibition'
                ];
            });

        // Obtener fechas de exhibición de obras
        $artworkDates = ArtworkDisplayDate::where('is_approved', true)
            ->with(['artwork', 'user'])
            ->get()
            ->map(function ($displayDate) {
                return [
                    'id' => 'artwork_' . $displayDate->id,
                    'title' => $displayDate->artwork->title,
                    'start' => $displayDate->display_date,
                    'end' => $displayDate->display_date,
                    'url' => '#',
                    'description' => $displayDate->artwork->description,
                    'type' => 'artwork',
                    'artist' => $displayDate->user->name
                ];
            });

        return response()->json($exhibitions->concat($artworkDates));
    }

    public function getGalleryImages($date)
    {
        $date = Carbon::parse($date);
        
        // Obtener las obras que están en exhibición en la fecha seleccionada
        $exhibitionArtworks = Artwork::whereHas('exhibitions', function ($query) use ($date) {
            $query->where('is_public', true)
                  ->where('status', 'approved')
                  ->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->with('user')->get();

        // Obtener las obras seleccionadas para esta fecha
        $selectedArtworks = Artwork::whereHas('displayDates', function ($query) use ($date) {
            $query->where('display_date', $date)
                  ->where('is_approved', true);
        })->with('user')->get();

        // Combinar ambas colecciones
        $allArtworks = $exhibitionArtworks->concat($selectedArtworks);

        $images = $allArtworks->map(function ($artwork) {
            return [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'url' => asset($artwork->image_path),
                'artist' => $artwork->user->name,
                'description' => $artwork->description
            ];
        });

        return response()->json($images);
    }
} 