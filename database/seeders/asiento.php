<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class asiento extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creación de asientos de manera dinámica (se puede modificar por salas, columnas vacías (pasillos), etc.)
        $asientos = array();

        $num_filas = 8;
        $num_columnas = 8;

        $estado_defecto = 1;
        $sala = 1;
        $tipo_asiento_defecto = 1;

        for ($row = 1; $row <= $num_filas; $row++) {
            for ($col = 1; $col <= $num_columnas; $col++) {
                $asientos[] = [
                    'estado' => $estado_defecto,
                    'columna' => $col,
                    'fila' => $row,
                    'id_sala' => $sala,
                    'id_tipo_asiento' => $tipo_asiento_defecto
                ];
            }
        }

        DB::table('asiento')->insert($asientos);
    }
}
