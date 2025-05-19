<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class sala extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sala')->insert([
<<<<<<< HEAD
            ['numero_asientos' => 96],
=======
            ['numero_asientos' => 240],
>>>>>>> 2f79ab2ad8c5ffb010f97af37e44638d37d306f0
            ['numero_asientos' => 64],
            ['numero_asientos' => 64],
            ['numero_asientos' => 56],
        ]);
    }
}
