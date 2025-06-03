<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use App\Models\Artwork;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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
        $artworksData = [
            [
                'title' => 'Paisaje Abstracto',
                'description' => 'Una interpretación moderna de un paisaje natural',
                'technique' => 'Óleo sobre lienzo',
                'year' => 2024,
                'external_image_url' => 'https://picsum.photos/800/600',
            ],
            [
                'title' => 'Retrato Digital',
                'description' => 'Exploración de la identidad en la era digital',
                'technique' => 'Arte digital',
                'year' => 2024,
                'external_image_url' => 'https://picsum.photos/801/600',
            ],
            [
                'title' => 'Escultura Moderna',
                'description' => 'Formas geométricas en movimiento',
                'technique' => 'Escultura en metal',
                'year' => 2024,
                'external_image_url' => 'https://picsum.photos/802/600',
            ],
            [
                'title' => 'Composición Minimalista',
                'description' => 'Simplicidad y elegancia en formas puras',
                'technique' => 'Acrílico sobre lienzo',
                'year' => 2024,
                'external_image_url' => 'https://picsum.photos/803/600',
            ]
        ];

        foreach ($artworksData as $artworkData) {
            try {
                // Descargar la imagen desde la URL externa
                $imageContents = file_get_contents($artworkData['external_image_url']);

                if ($imageContents === false) {
                    throw new \Exception('No se pudo descargar la imagen de la URL: ' . $artworkData['external_image_url']);
                }

                // Generar un nombre de archivo único
                $filename = 'artworks/' . Str::random(40) . '.jpg'; // Asumimos .jpg por simplicidad, se podría detectar la extensión

                // Guardar la imagen en el disco 'public'
                Storage::disk('public')->put($filename, $imageContents);

                // Crear la obra en la base de datos con la ruta de almacenamiento local
                Artwork::create([
                    'user_id' => $artist->id,
                    'title' => $artworkData['title'],
                    'description' => $artworkData['description'],
                    'technique' => $artworkData['technique'],
                    'year' => $artworkData['year'],
                    'image_path' => $filename, // Usamos la ruta relativa guardada por Storage
                ]);

            } catch (\Exception $e) {
                // Registrar cualquier error durante la descarga o guardado
                Log::error('Error seeding artwork image:', ['url' => $artworkData['external_image_url'], 'error' => $e->getMessage()]);
                // Opcional: crear la obra sin imagen o con una ruta placeholder si falla la descarga
                Artwork::create([
                    'user_id' => $artist->id,
                    'title' => $artworkData['title'],
                    'description' => $artworkData['description'],
                    'technique' => $artworkData['technique'],
                    'year' => $artworkData['year'],
                    'image_path' => null, // O una ruta a una imagen por defecto local
                ]);
            }
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