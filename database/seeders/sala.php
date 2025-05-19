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
            ['numero_asientos' => 240],
            ['numero_asientos' => 64],
            ['numero_asientos' => 64],
            ['numero_asientos' => 56],
        ]);
    }
}
