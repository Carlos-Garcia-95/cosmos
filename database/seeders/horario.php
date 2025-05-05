<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class horario extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('horario')->insert([
            [
                'id_sala' => 1,
                'id_pelicula' => 1471014,
                'hora' => '12:30:00',
                'activo' => 1
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 950387,
                'hora' => '14:00:00',
                'activo' => 1
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 822119,
                'hora' => '18:30:00',
                'activo' => 1
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 1225915,
                'hora' => '21:00:00',
                'activo' => 1
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 1233069,
                'hora' => '23:30:00',
                'activo' => 1
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 324544,
                'hora' => '01:00:00',
                'activo' => 1
            ]
        ]);
    }
}
