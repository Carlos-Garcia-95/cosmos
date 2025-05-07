<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class fecha extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fechaInicio = Carbon::now(); // Gets the current date
        $fechaFin = Carbon::now()->addDays(180); // Adds 30 days to the current date

        $fechas = [];
        for ($date = $fechaInicio->copy(); $date->lte($fechaFin); $date->addDay()) {
            $fechas[] = [
                'fecha' => $date->format('Y-m-d'), // Format the date as YYYY-MM-DD
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('fecha')->insert($fechas);
    }
}
