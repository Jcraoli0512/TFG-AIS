<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exhibition;
use Illuminate\Support\Facades\DB;

class CleanExhibitionsSeeder extends Seeder
{
    public function run()
    {
        // Desactivar la verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Eliminar todas las exposiciones existentes
        Exhibition::truncate();
        
        // Eliminar las relaciones en la tabla pivot
        DB::table('artwork_exhibition')->truncate();
        
        // Reactivar la verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
} 