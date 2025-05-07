<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class pelicula_genero extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelicula_genero')->insert([
            [
                'id_pelicula' => 1, // ID de la película creada previamente
                'id_genero_pelicula' => 28, // ID de género creado previamente
            ],
            [
                'id_pelicula' => 1,
                'id_genero_pelicula' => 12,
            ],
            [
                'id_pelicula' => 1,
                'id_genero_pelicula' => 878,
            ],
            [
                'id_pelicula' => 2,
                'id_genero_pelicula' => 28,
            ],
            [
                'id_pelicula' => 2,
                'id_genero_pelicula' => 80,
            ],
            [
                'id_pelicula' => 2,
                'id_genero_pelicula' => 53,
            ],
            [
                'id_pelicula' => 3,
                'id_genero_pelicula' => 28,
            ],
            [
                'id_pelicula' => 3,
                'id_genero_pelicula' => 80,
            ],
            [
                'id_pelicula' => 3,
                'id_genero_pelicula' => 53,
            ],
            [
                'id_pelicula' => 4,
                'id_genero_pelicula' => 10751,
            ],
            [
                'id_pelicula' => 4,
                'id_genero_pelicula' => 35,
            ],
            [
                'id_pelicula' => 4,
                'id_genero_pelicula' => 12,
            ],
            [
                'id_pelicula' => 4,
                'id_genero_pelicula' => 14,
            ],
            [
                'id_pelicula' => 5,
                'id_genero_pelicula' => 37,
            ],
            [
                'id_pelicula' => 6,
                'id_genero_pelicula' => 53,
            ],
            [
                'id_pelicula' => 6,
                'id_genero_pelicula' => 28,
            ],
        ]);
    }
}
