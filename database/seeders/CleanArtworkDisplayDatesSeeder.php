<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArtworkDisplayDate;

class CleanArtworkDisplayDatesSeeder extends Seeder
{
    public function run()
    {
        // Eliminar todas las fechas de exhibición existentes
        ArtworkDisplayDate::truncate();
    }
} 