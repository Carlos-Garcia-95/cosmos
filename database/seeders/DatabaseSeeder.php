<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            administrador::class,
            ciudades::class,
            edad_recomendad::class,
            tipo_asiento::class,
            sala::class,
            asiento::class,
            impuesto::class,
            descuento::class,
            users::class,
            genero_pelicula::class,
            MenuSeeder::class,
            pelicula::class,
            pelicula_genero::class,
            hora::class,
            fecha::class,
            horario::class
        ]);
    }
}
