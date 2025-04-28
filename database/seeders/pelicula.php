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
                'nombre' => 'El Padrino',
                'duracion' => 175,
                'director' => 'Francis Ford Coppola',
                'actor' => 'Marlon Brando, Al Pacino, James Caan, Robert Duvall',
                'sinopsis' => 'El envejecido patriarca de una dinastía del crimen organizado en Nueva York transfiere el control de su imperio clandestino a su reacio hijo.',
                'fecha_estreno' => '1972-10-25',
                'id_edad_recomendada' => 5,
                'id_sala' => 1,
            ],
            [
                'nombre' => 'Parque Jurásico',
                'duracion' => 127,
                'director' => 'Steven Spielberg',
                'actor' => 'Sam Neill, Laura Dern, Jeff Goldblum, Richard Attenborough',
                'sinopsis' => 'Un excéntrico millonario crea un parque temático biológico en una isla remota, donde una arquitecta genética ha conseguido clonar dinosaurios reales a partir de ADN encontrado en ámbar.',
                'fecha_estreno' => '1993-09-30',
                'id_edad_recomendada' => 2,
                'id_sala' => 1,
            ],
            [
                'nombre' => 'El Viaje de Chihiro',
                'duracion' => 125,
                'director' => 'Hayao Miyazaki',
                'actor' => 'Rumi Hiiragi (voz), Miyu Irino (voz), Mari Natsuki (voz)',
                'sinopsis' => 'Una niña de 10 años se muda con sus padres a un nuevo barrio y se ve atrapada en un mundo mágico habitado por espíritus, donde debe trabajar para liberar a sus padres y regresar a su propio mundo.',
                'fecha_estreno' => '2002-10-18',
                'id_edad_recomendada' => 1,
                'id_sala' => 1,
            ],
            [
                'nombre' => 'Amelie',
                'duracion' => 122,
                'director' => 'Jean-Pierre Jeunet',
                'actor' => 'Audrey Tautou, Mathieu Kassovitz',
                'sinopsis' => 'Una joven camarera parisina, con una imaginación desbordante, decide dedicarse a observar y manipular sutilmente la vida de las personas que la rodean para traerles pequeños momentos de felicidad.',
                'fecha_estreno' => '2001-10-19',
                'id_edad_recomendada' => 2,
                'id_sala' => 1,
            ],
            [
                'nombre' => 'Tesis',
                'duracion' => 125,
                'director' => 'Alejandro Amenábar',
                'actor' => 'Ana Torrent, Fele Martínez, Eduardo Noriega',
                'sinopsis' => 'Una estudiante de cine, mientras prepara su tesis sobre la violencia audiovisual, descubre una película snuff, lo que la arrastra a un oscuro y peligroso mundo.',
                'fecha_estreno' => '1996-04-26',
                'id_edad_recomendada' => 5,
                'id_sala' => 1,
            ],
            [
                'nombre' => 'Mad Max: Furia en la Carretera',
                'duracion' => 120,
                'director' => 'George Miller',
                'actor' => 'Tom Hardy, Charlize Theron, Nicholas Hoult',
                'sinopsis' => 'En un desierto post-apocalíptico, un vagabundo solitario llamado Max se une a Furiosa, una mujer rebelde que huye de un tirano opresor con un grupo de prisioneras, desatando una frenética persecución.',
                'fecha_estreno' => '2015-05-14',
                'id_edad_recomendada' => 4,
                'id_sala' => 1,
            ],
        ]);
    }
}
