<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class impuesto extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('impuesto')->insert([
            ['cantidad' => 21.00],
            ['cantidad' => 10.00],
            ['cantidad' => 0],
        ]);
    }
}
