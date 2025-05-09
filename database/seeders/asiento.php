<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Constants\Salas;

class asiento extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Recoger la configuracion por defecto de las salas
        $salas = Salas::SALAS;

        // Por cada sala hay una configuración de asientos distinta
        foreach ($salas as $id_sala => $sala) {
            $filas = $sala['filas'];
            $columnas = $sala['columnas'];
            $estado_defecto = $sala['estado_defecto'];
            $tipo_defecto = $sala['tipo_defecto'];

            $sesiones_pelicula = DB::table('sesion_pelicula')->get();

            // Genera los asientos para la sesión actual
            foreach ($sesiones_pelicula as $sesion) {
                $asientos = array();

                // Se recupera el número total de filas y columnas
                $max_fila = max($filas);
                $max_columna = max($columnas);

                // Se generan los asientos de forma dinámica según las filas y columnas de cada sala
                // Si no existe esa fila o columna, se rellena como false
                for ($fila = 1; $fila <= $max_fila; $fila++) {
                    for ($columna = 1; $columna <= $max_columna; $columna++) {
                        // Si existe la fila y la columna, se intruduce fila y columna de asiento como integer
                        if (in_array($fila, $filas) && in_array($columna, $columnas)) {
                            $asientos[] = [
                                'id_sesion_pelicula' => $sesion->id,
                                'estado' => $estado_defecto,
                                'columna' => $columna,
                                'fila' => $fila,
                                'id_sala' => $id_sala,
                                'id_tipo_asiento' => $tipo_defecto,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        } else {            // Si no hay fila o columna, se introduce un hueco (false)
                            $asientos[] = [
                                'id_sesion_pelicula' => $sesion->id,
                                'estado' => $estado_defecto,
                                'columna' => -1,
                                'fila' => -1,
                                'id_sala' => $id_sala,
                                'id_tipo_asiento' => $tipo_defecto,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }
                
                // Inserta los asientos generados para la sesión
                DB::table('asiento')->insert($asientos);
            }
        }
    }
}
