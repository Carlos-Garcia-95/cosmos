<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class genero_pelicula extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genero_pelicula')->insert([
            ['genero' => 'Acción'],
            ['genero' => 'Comedia'],
            ['genero' => 'Drama'],
            ['genero' => 'Terror'],
        ]);
    }
}
