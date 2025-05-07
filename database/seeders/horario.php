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
                'id_pelicula' => 1,
                'hora' => 21,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 2,
                'hora' => 27,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 3,
                'hora' => 33,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 4,
                'hora' => 39,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 5,
                'hora' => 45,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_sala' => 1,
                'id_pelicula' => 6,
                'hora' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
