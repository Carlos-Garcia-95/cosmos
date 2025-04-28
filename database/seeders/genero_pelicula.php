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
            ['genero' => 'Aventura'],
            ['genero' => 'Comedia'],
            ['genero' => 'Drama'],
            ['genero' => 'Terror'],
            ['genero' => 'Horror'],
            ['genero' => 'Fantasía'],
            ['genero' => 'Ciencia Ficción'],
            ['genero' => 'Suspense'],
            ['genero' => 'Thriller'],
            ['genero' => 'Misterio'],
            ['genero' => 'Romance'],
            ['genero' => 'Animación'],
            ['genero' => 'Documental'],
            ['genero' => 'Musical'],
            ['genero' => 'Familiar'],
            ['genero' => 'Western'],
            ['genero' => 'Crimen'],
            ['genero' => 'Bélico'],
            ['genero' => 'Histórico'],
            ['genero' => 'Biografía'],
            ['genero' => 'Deportes'],
            ['genero' => 'Artes Marciales'],
            ['genero' => 'Superhéroes'],
            ['genero' => 'Cine Negro'],
            ['genero' => 'Psicológico'],
            ['genero' => 'Cortometraje'],
            ['genero' => 'Experimental'],
            ['genero' => 'Catástrofe'],
            ['genero' => 'Supervivencia'],
            ['genero' => 'Espías'],
            ['genero' => 'Judicial'],
            ['genero' => 'Legal'],
            ['genero' => 'Político'],
            ['genero' => 'Monstruos'],
            ['genero' => 'Criaturas'],
            ['genero' => 'Distopía'],
            ['genero' => 'Post Apocalíptico'],
            ['genero' => 'Realista'],
        ]);
    }
}
