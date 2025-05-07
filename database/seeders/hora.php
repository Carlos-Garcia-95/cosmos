<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class hora extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hora')->insert([
            [
                "id" => 1,
                "hora" => '09:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 2,
                "hora" => '09:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 3,
                "hora" => '10:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 4,
                "hora" => '10:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 5,
                "hora" => '11:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 6,
                "hora" => '11:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 7,
                "hora" => '12:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 8,
                "hora" => '12:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 9,
                "hora" => '13:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 10,
                "hora" => '13:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 11,
                "hora" => '14:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 12,
                "hora" => '14:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 13,
                "hora" => '15:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 14,
                "hora" => '15:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 15,
                "hora" => '16:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 16,
                "hora" => '16:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 17,
                "hora" => '17:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 18,
                "hora" => '17:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 19,
                "hora" => '18:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 20,
                "hora" => '18:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 21,
                "hora" => '19:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 22,
                "hora" => '19:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
             [
                "id" => 23,
                "hora" => '20:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 24,
                "hora" => '20:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 25,
                "hora" => '21:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 26,
                "hora" => '21:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 27,
                "hora" => '22:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 28,
                "hora" => '22:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 29,
                "hora" => '23:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 30,
                "hora" => '23:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 31,
                "hora" => '00:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 32,
                "hora" => '00:30',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                "id" => 33,
                "hora" => '01:00',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
