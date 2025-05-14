<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Exhibition;
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
                    'id' => $exhibition->id,
                    'title' => $exhibition->title,
                    'start' => $exhibition->start_date,
                    'end' => $exhibition->end_date,
                    'url' => '#', // Por ahora no redirigimos a ninguna página
                    'description' => $exhibition->description
                ];
            });

        return response()->json($exhibitions);
    }

    public function getGalleryImages($date)
    {
        $date = Carbon::parse($date);
        
        // Obtener las obras que están en exhibición en la fecha seleccionada
        $artworks = Artwork::whereHas('exhibitions', function ($query) use ($date) {
            $query->where('is_public', true)
                  ->where('status', 'approved')
                  ->where('start_date', '<=', $date)
                  ->where('end_date', '>=', $date);
        })->with('user')->get();

        $images = $artworks->map(function ($artwork) {
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