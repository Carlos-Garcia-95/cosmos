<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class users extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nombre' => 'Juan',
                'apellidos' => 'Perez',
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
            [
                'nombre' => 'Pepe',
                'apellidos' => 'Tolini',
                'email' => 'pepe@gmail.com',
                'fecha_nacimiento' => '1998-07-19',
                'numero_telefono' => '758694257',
                'dni' => '55324856Y',
                'direccion' => 'Av. Siempre Muerta 321',
                'ciudad' => 'Ciudad Gotham',
                'codigo_postal' => '65432',
                'password' => bcrypt('123'),
                'id_descuento' => 1,
            ]
        ]);
    }
}
