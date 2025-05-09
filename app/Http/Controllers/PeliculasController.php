<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;

class PeliculasController extends Controller
{
    // Recuperar películas y sus generos asociados
    public static function recuperar_peliculas_activas() {
        $peliculas_objeto = Pelicula::with('generos')->where('activa', true)->get();

        foreach ($peliculas_objeto as $pelicula) {
            $peliculas[$pelicula->id] = [
                'id' => $pelicula->id,
                'adult' => $pelicula->adult,
                'backdrop_ruta' => $pelicula->backdrop_ruta,
                'backdrop_url' => self::formatear_url_backdrop($pelicula->backdrop_ruta),
                'id_api' => $pelicula->id_api,
                'lenguaje_original' => $pelicula->lenguaje_original,
                'titulo_original' => $pelicula->titulo_original,
                'sinopsis' => $pelicula->sinopsis,
                'poster_ruta' => $pelicula->poster_ruta,
                'poster_url' => self::formatear_url_poster($pelicula->poster_ruta),
                'fecha_estreno' => $pelicula->fecha_estreno,
                'titulo' => $pelicula->titulo,
                'video' => $pelicula->video,
                'activa' => $pelicula->activa,
                'creacion' => $pelicula->creacion,
                'id_sala' => $pelicula->id_sala,
                'generos' => $pelicula->generos->pluck('genero')->toArray(),
            ];
        }

        return $peliculas;
    }

    private static function formatear_url_backdrop($ruta) {
        $url_api = "https://image.tmdb.org/t/p/original/";
        $url = "";

        if (isset($ruta)) {
            $url = $url_api . $ruta;
        }

        return $url;
    }

    private static function formatear_url_poster($ruta) {
        $url_api = "https://image.tmdb.org/t/p/original/";
        $url = "";

        if (isset($ruta)) {
            $url = $url_api . $ruta;
        }

        return $url;
    }
}
