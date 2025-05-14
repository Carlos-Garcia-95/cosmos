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
                'dni' => '12345678U',
                'direccion' => 'Av. Siempre Viva 123',
                'ciudad' => '1',
                'codigo_postal' => '12345',
                'tipo_usuario' => '2',
                'mayor_edad' => 1,
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
                'ciudad' => '12',
                'codigo_postal' => '65432',
                'tipo_usuario' => '3',
                'mayor_edad' => 1,
                'password' => bcrypt('123456789'),
                'id_descuento' => 2,
            ],[
                'nombre' => 'Diego',
                'apellidos' => 'Pérez',
                'email' => 'diego.perez@cosmosAdmin.com',
                'fecha_nacimiento' => '2000-02-03',
                'numero_telefono' => '123456789',
                'dni' => '55324856J',
                'direccion' => 'Av. Siempre Muerta 321',
                'ciudad' => '13',
                'codigo_postal' => '65432',
                'tipo_usuario' => '1',
                'mayor_edad' => 1,
                'password' => bcrypt('CosmosAdmin123'),
                'id_descuento' => 1,
            ],
            [
                'nombre' => 'Carlos',
                'apellidos' => 'García',
                'email' => 'carlos.garcia@cosmosAdmin.com',
                'numero_telefono' => '123456789',
                'dni' => '55324859P',
                'direccion' => 'Av. Siempre Muerta 321',
                'ciudad' => '14',
                'codigo_postal' => '65432',
                'tipo_usuario' => '1',
                'fecha_nacimiento' => '1990-02-20',
                'numero_telefono' => '555654321',
                'mayor_edad' => 1,
                'password' => bcrypt('CosmosAdmin456'),
                'id_descuento' => 1,
            ],
        ]);
    }
}
