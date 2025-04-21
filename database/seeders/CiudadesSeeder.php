<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ciudad;

class CiudadesSeeder extends Seeder
{
//Falta añadir más
public function run()
{
    Ciudad::create(['nombre' => 'Madrid']);
Ciudad::create(['nombre' => 'Barcelona']);
Ciudad::create(['nombre' => 'Valencia']);
Ciudad::create(['nombre' => 'Sevilla']);
Ciudad::create(['nombre' => 'Zaragoza']);
Ciudad::create(['nombre' => 'Málaga']);
Ciudad::create(['nombre' => 'Murcia']);
Ciudad::create(['nombre' => 'Palma de Mallorca']);
Ciudad::create(['nombre' => 'Las Palmas de Gran Canaria']);
Ciudad::create(['nombre' => 'Lleida']);
Ciudad::create(['nombre' => 'Bilbao']);
Ciudad::create(['nombre' => 'Alicante']);
Ciudad::create(['nombre' => 'Córdoba']);
Ciudad::create(['nombre' => 'Valladolid']);
Ciudad::create(['nombre' => 'Cáceres']);
Ciudad::create(['nombre' => 'Salamanca']);
Ciudad::create(['nombre' => 'Girona']);
Ciudad::create(['nombre' => 'Toledo']);
Ciudad::create(['nombre' => 'Badajoz']);
Ciudad::create(['nombre' => 'Oviedo']);

}
}