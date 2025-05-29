<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use App\Models\Artwork;
use App\Models\User;
use Carbon\Carbon;

class ExhibitionSeeder extends Seeder
{
    public function run()
    {
        // Crear un usuario artista si no existe
        $artist = User::firstOrCreate(
            ['email' => 'artist@example.com'],
            [
                'name' => 'Artista Demo',
                'password' => bcrypt('password'),
                'role' => 'artist',
                'is_active' => true
            ]
        );

        // Crear algunas obras de arte
        $artworks = [
            [
                'title' => 'Paisaje Abstracto',
                'description' => 'Una interpretación moderna de un paisaje natural',
                'technique' => 'Óleo sobre lienzo',
                'year' => 2024,
                'image_path' => 'https://picsum.photos/800/600',
                'user_id' => $artist->id
            ],
            [
                'title' => 'Retrato Digital',
                'description' => 'Exploración de la identidad en la era digital',
                'technique' => 'Arte digital',
                'year' => 2024,
                'image_path' => 'https://picsum.photos/801/600',
                'user_id' => $artist->id
            ],
            [
                'title' => 'Escultura Moderna',
                'description' => 'Formas geométricas en movimiento',
                'technique' => 'Escultura en metal',
                'year' => 2024,
                'image_path' => 'https://picsum.photos/802/600',
                'user_id' => $artist->id
            ],
            [
                'title' => 'Composición Minimalista',
                'description' => 'Simplicidad y elegancia en formas puras',
                'technique' => 'Acrílico sobre lienzo',
                'year' => 2024,
                'image_path' => 'https://picsum.photos/803/600',
                'user_id' => $artist->id
            ]
        ];

        foreach ($artworks as $artworkData) {
            Artwork::create($artworkData);
        }

        // Crear algunas exposiciones
        $exhibitions = [
            [
                'title' => 'Arte Contemporáneo 2024',
                'description' => 'Una muestra de las mejores obras contemporáneas',
                'curatorial_note' => 'Curada por el equipo de Art Indie Space',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'status' => 'approved',
                'is_public' => true,
                'user_id' => $artist->id
            ],
            [
                'title' => 'Nuevas Perspectivas',
                'description' => 'Explorando nuevos horizontes en el arte digital',
                'curatorial_note' => 'Una mirada al futuro del arte',
                'start_date' => Carbon::now()->addMonth(),
                'end_date' => Carbon::now()->addMonths(3),
                'status' => 'approved',
                'is_public' => true,
                'user_id' => $artist->id
            ]
        ];

        foreach ($exhibitions as $exhibitionData) {
            $exhibition = Exhibition::create($exhibitionData);
            
            // Asociar todas las obras a la exposición
            $exhibition->artworks()->attach(Artwork::all()->pluck('id'));
        }
    }
} 