<?php

namespace App\Http\Controllers;

use App\Models\Fecha;
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
        $fecha_fin = $fecha_hoy->copy()->addDays(5);

        // Recuperar las sesiones de la película seleccionada en los próximos 5 días
        $sesiones = SesionPelicula::with(['fecha', 'hora'])
                    ->where('created_at', '>=', $fecha_hoy->startOfDay())
                    ->where('created_at', '<=', $fecha_fin->endOfDay())
                    ->where('id_pelicula', $id_pelicula)
                    ->get();

        foreach ($sesiones as &$sesion) {
            // Recuperar el día de la semana de la fecha recuperada
            $fecha_sesion_id = $sesion->fecha;
            // Crear una instancia de la fecha y recuperar el día
            $fecha_sesion = Fecha::find($fecha_sesion_id);
            $fecha = Carbon::parse($fecha_sesion->fecha);
            $dia_semana = ucfirst($fecha->localeDayOfWeek);
            
            if (!isset($dia_semana)) {
                $dia_semana = "Indeterminado";
            }

            $sesion["dia_semana"] = $dia_semana;
        }


        
        return $sesiones;
    }
}
