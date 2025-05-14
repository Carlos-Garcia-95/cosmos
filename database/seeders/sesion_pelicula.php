<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class sesion_pelicula extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 7; $i++) {
            DB::table('sesion_pelicula')->insert([
                [
                    'id_sala' => 1,
                    'id_pelicula' => 1,
                    'hora' => 21,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id_sala' => 1,
                    'id_pelicula' => 2,
                    'hora' => 27,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                /* [
                    'id_sala' => 1,
                    'id_pelicula' => 3,
                    'hora' => 33,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id_sala' => 1,
                    'id_pelicula' => 4,
                    'hora' => 39,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ], */
                [
                    'id_sala' => 1,
                    'id_pelicula' => 5,
                    'hora' => 45,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'id_sala' => 1,
                    'id_pelicula' => 6,
                    'hora' => 3,
                    'fecha' => $i,
                    'activa' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }
}
