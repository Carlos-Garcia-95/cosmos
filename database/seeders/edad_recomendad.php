<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class edad_recomendad extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('edad_recomendada')->insert([
            ['tipo' => 'Todos'],
            ['tipo' => 'Mayores de 13'],
            ['tipo' => 'Mayores de 18'],
        ]);
    }
}
