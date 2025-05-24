<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeliculasController extends Controller
{
    // Recuperar películas y sus generos asociados
    public static function recuperar_peliculas_activas() {
        $fecha_actual = Carbon::now()->toDateString();
        $hora_actual = Carbon::now()->toTimeString();

        $peliculas_objeto = Pelicula::with('generos')
                                        ->where('activa', true)
                                        ->whereHas('sesiones', function ($querySesion) use ($fecha_actual, $hora_actual) {
                                    // Filtrar sesiones que están activas
                                            $querySesion->where('activa', true)
                                    // Filtrar sesiones cuya fecha.fecha sea hoy o en el futuro
                                            ->whereHas('fechaRelacion', function ($queryFecha) use ($fecha_actual) {
                                                $queryFecha->where('fecha', '>=', $fecha_actual);
                                            })
                                    // Y para las sesiones de hoy, filtrar aquellas cuya hora.hora sea en el futuro
                                    // O para cualquier sesión en una fecha futura (donde la hora no importa tanto para este filtro inicial)
                                            ->where(function ($queryHoraCondicion) use ($fecha_actual, $hora_actual) {
                                    // Caso 1: La sesión es para una fecha futura (entonces cualquier hora es válida para "posterior")
                                                $queryHoraCondicion->whereHas('fechaRelacion', function ($queryFechaFutura) use ($fecha_actual) {
                                                    $queryFechaFutura->where('fecha', '>', $fecha_actual);
                                                })
                                    // Caso 2: O la sesión es para hoy Y la hora es posterior a la actual
                                                ->orWhere(function ($queryHoyConHoraFutura) use ($fecha_actual, $hora_actual) {
                                                    $queryHoyConHoraFutura->whereHas('fechaRelacion', function ($queryFechaHoy) use ($fecha_actual) {
                                                        $queryFechaHoy->where('fecha', '=', $fecha_actual);
                                                    })
                                                    ->whereHas('horaRelacion', function ($queryHora) use ($hora_actual) {
                                                        $queryHora->where('hora', '>', $hora_actual);
                                                    });
                                                });
                                            });
                                        })
                                        ->get();

        foreach ($peliculas_objeto as $pelicula) {
            $peliculas[$pelicula->id] = [
                'id' => $pelicula->id,
                'adult' => $pelicula->adult,
                'backdrop_ruta' => $pelicula->backdrop_ruta,
                'backdrop_url' => self::formatear_url($pelicula->backdrop_ruta),
                'id_api' => $pelicula->id_api,
                'lenguaje_original' => $pelicula->lenguaje_original,
                'titulo_original' => $pelicula->titulo_original,
                'sinopsis' => $pelicula->sinopsis,
                'poster_ruta' => $pelicula->poster_ruta,
                'poster_url' => self::formatear_url($pelicula->poster_ruta),
                'fecha_estreno' => $pelicula->fecha_estreno,
                'titulo' => $pelicula->titulo,
                'video' => $pelicula->video,
                'activa' => $pelicula->activa,
                'creacion' => $pelicula->creacion,
                'id_sala' => $pelicula->id_sala,
                'generos' => $pelicula->generos->pluck('genero')->toArray(),
                'duracion' => $pelicula->duracion,
            ];
        }

        return $peliculas;
    }

    public static function formatear_url($ruta) {
        $url_api = "https://image.tmdb.org/t/p/original/";
        $url = "";

        if (isset($ruta)) {
            $url = $url_api . $ruta;
        }

        return $url;
    }
}
