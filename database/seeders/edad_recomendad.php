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
            ['tipo' => 'TP'],
            ['tipo' => '7'],
            ['tipo' => '12'],
            ['tipo' => '16'],
            ['tipo' => '18'],
        ]);
    }
}
