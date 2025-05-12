<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
use App\Models\Pelicula;
use App\Models\SesionPelicula;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class RecuperarSesionPelicula extends Controller
{
    function recuperar_sesion_pelicula($id_pelicula) {
        // Cambiamos el lenguaje a español
        App::setLocale('es');

        // Recuperar la fecha de hoy y la de 5 días despúes de hoy
        $fecha_hoy = Carbon::today();
        $fecha_fin = $fecha_hoy->copy()->addDays(6);

        // Recuperar las sesiones de la película seleccionada en los próximos 5 días
        $sesiones = SesionPelicula::with(['fecha', 'hora'])
                    ->join('fecha', 'sesion_pelicula.fecha', '=', 'fecha.id')
                    ->where('fecha.fecha', '>=', $fecha_hoy->startOfDay())
                    ->where('fecha.fecha', '<=', $fecha_fin->endOfDay())
                    ->where('sesion_pelicula.id_pelicula', $id_pelicula)
                    ->select('sesion_pelicula.*')
                    ->get();

        foreach ($sesiones as &$sesion) {
            // Crear una instancia de la fecha y recuperar el día
            $fecha_values = Fecha::find($sesion->fecha);
            $fecha = Carbon::parse($fecha_values->fecha);
            $dia_semana = ucfirst($fecha->localeDayOfWeek);
            
            if (!isset($dia_semana)) {
                $dia_semana = "Indeterminado";
            }

            $sesion["dia_semana"] = $dia_semana;
        }


        
        return $sesiones;
    }
}
