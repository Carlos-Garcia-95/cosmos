<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ppelicula_genero extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pelicula_genero')->insert([
            [
                'id_pelicula' => 1, // ID de la película creada previamente
                'id_genero_pelicula' => 1, // ID de género creado previamente
            ],
            [
                'id_pelicula' => 1,
                'id_genero_pelicula' => 3,
            ],
            [
                'id_pelicula' => 2,
                'id_genero_pelicula' => 2,
            ],
        ]);
    }
}
