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
            genero_pelicula::class,
            tipo_asiento::class,
            sala::class,
            asiento::class,
            impuesto::class,
            descuento::class,
            users::class,
            pelicula::class,
            ppelicula_genero::class,
            MenuSeeder::class
        ]);
    }
}
