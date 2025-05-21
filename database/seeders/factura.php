<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class factura extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('factura')->insert([
            'id_factura' => 1,
            'monto_total' => "50",
            'ultimos_digitos' => "1234",
            'titular' => "Pepe Perez",
            'id_user' => 1,
            'id_impuesto' => 1,
        ]);
    }
}
