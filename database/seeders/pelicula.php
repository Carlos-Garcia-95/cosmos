<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class pelicula extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelicula')->insert([
            [
                'nombre' => 'Pelicula 1',
                'duracion' => 120,
                'director' => 'Director 1',
                'actor' => 'Actor 1',
                'sinopsis' => 'Sinopsis de la película 1',
                'fecha_estreno' => '2025-05-01',
                'id_edad_recomendada' => 1, // ID de edad recomendada creada previamente
                'id_sala' => 1, // ID de sala creada previamente
            ],
            [
                'nombre' => 'Pelicula 2',
                'duracion' => 90,
                'director' => 'Director 2',
                'actor' => 'Actor 2',
                'sinopsis' => 'Sinopsis de la película 2',
                'fecha_estreno' => '2025-06-01',
                'id_edad_recomendada' => 2,
                'id_sala' => 2,
            ],
        ]);
    }
}
