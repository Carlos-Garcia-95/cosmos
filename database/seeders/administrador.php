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
                'nombre_user_admin' => 'Diego_Cosmos',
                'nombre' => 'Diego',
                'apellido' => 'Pérez',
                'email' => 'diego.perez@cosmosAdmin.com',
                'fecha_nacimiento' => '2000-02-03',
                'numero_telefono' => '123456789',
                'password' => bcrypt('CosmosAdmin123'),
                'codigo_administrador' => 'A001',
            ],
            [
                'nombre_user_admin' => 'Carlos_Cosmos',
                'nombre' => 'Carlos',
                'apellido' => 'García',
                'email' => 'carlos.garcia@cosmosAdmin.com',
                'fecha_nacimiento' => '1990-02-20',
                'numero_telefono' => '555654321',
                'password' => bcrypt('CosmosAdmin456'),
                'codigo_administrador' => 'A002',
            ],
        ]);
    }
}
