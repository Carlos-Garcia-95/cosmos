<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class administrador extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('administrador')->insert([
            [
                'nombre_usuario_admin' => 'admin1',
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'email' => 'juan.perez@admin.com',
                'fecha_nacimiento' => '1985-01-15',
                'numero_telefono' => '555123456',
                'contrasena' => bcrypt('password123'),
                'codigo_administrador' => 'A001',
            ],
            [
                'nombre_usuario_admin' => 'admin2',
                'nombre' => 'Ana',
                'apellido' => 'Gomez',
                'email' => 'ana.gomez@admin.com',
                'fecha_nacimiento' => '1990-02-20',
                'numero_telefono' => '555654321',
                'contrasena' => bcrypt('password456'),
                'codigo_administrador' => 'A002',
            ],
        ]);
    }
}
