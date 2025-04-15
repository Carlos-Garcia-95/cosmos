<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class usuario extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuario')->insert([
            [
                'nombre_usuario' => 'juan123',
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'email' => 'juan.perez@gmail.com',
                'fecha_nacimiento' => '2000-04-10',
                'numero_telefono' => '555111111',
                'dni' => '123456789',
                'direccion' => 'Av. Siempre Viva 123',
                'ciudad' => 'Ciudad X',
                'codigo_postal' => '12345',
                'password' => bcrypt('password123'),
                'id_descuento' => 1, // Esto debería existir previamente
            ],
        ]);
    }
}
