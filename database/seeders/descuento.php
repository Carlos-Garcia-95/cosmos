<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class descuento extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('descuento')->insert([
            ['descuento' => 10, 'tipo' => 'Estudiante'],
            ['descuento' => 20, 'tipo' => 'Miembro'],
        ]);
    }
}
