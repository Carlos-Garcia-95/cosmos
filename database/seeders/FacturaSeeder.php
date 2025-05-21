<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Impuesto;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de tener usuarios e impuestos en tu BD
        $userIds = User::pluck('id')->toArray();
        if (empty($userIds)) {
            $this->command->error('No hay usuarios en la base de datos. Por favor, ejecuta UserSeeder primero.');
            return;
        }

        // Obtener IDs de impuestos existentes (del seeder de Impuesto que ya tienes)
        // Asumiendo que tienes 'IVA' (21%), 'Reducido' (10%), 'Ninguno' (0%)
        $impuestoIvaId = Impuesto::where('tipo', 'IVA')->value('id_impuesto');
        $impuestoReducidoId = Impuesto::where('tipo', 'Reducido')->value('id_impuesto');
        $impuestoNingunoId = Impuesto::where('tipo', 'Ninguno')->value('id_impuesto');

        if (!$impuestoIvaId || !$impuestoReducidoId || !$impuestoNingunoId) {
            $this->command->error('No se encontraron todos los tipos de impuestos necesarios (IVA, Reducido, Ninguno). Por favor, ejecuta ImpuestoSeeder.');
            return;
        }

        $impuestoIds = [$impuestoIvaId, $impuestoReducidoId, $impuestoNingunoId];

        $facturas = [];
        $faker = \Faker\Factory::create('es_ES'); // Usar Faker para datos más realistas

        // Generar facturas para los últimos 12 meses, incluyendo el actual
        for ($i = 0; $i < 50; $i++) { // Generar 50 facturas de ejemplo
            $randomDaysAgo = rand(0, 365); // Facturas en el último año
            $createdAt = Carbon::now()->subDays($randomDaysAgo)->subHours(rand(0,23))->subMinutes(rand(0,59));

            // monto_total es la base imponible (antes de impuestos)
            $montoBase = rand(1000, 15000) / 100; // Entre 10.00 y 150.00

            $facturas[] = [
                'monto_total' => $montoBase,
                'ultimos_digitos' => $faker->randomNumber(4, true), // 4 últimos dígitos de una tarjeta
                'titular' => $faker->name,
                'id_user' => $userIds[array_rand($userIds)], // Un ID de usuario aleatorio
                'id_impuesto' => $impuestoIds[array_rand($impuestoIds)], // Un ID de impuesto aleatorio
                'created_at' => $createdAt,
                'updated_at' => $createdAt, // O Carbon::now() si quieres que sea diferente
            ];
        }

        // Generar algunas facturas para HOY específicamente (para probar el resumen diario)
        for ($i = 0; $i < 5; $i++) {
            $createdAtHoy = Carbon::now()->subHours(rand(0, Carbon::now()->hour))->subMinutes(rand(0,59));
            $montoBaseHoy = rand(500, 5000) / 100;

            $facturas[] = [
                'monto_total' => $montoBaseHoy,
                'ultimos_digitos' => $faker->randomNumber(4, true),
                'titular' => $faker->name,
                'id_user' => $userIds[array_rand($userIds)],
                'id_impuesto' => $impuestoIds[array_rand($impuestoIds)],
                'created_at' => $createdAtHoy,
                'updated_at' => $createdAtHoy,
            ];
        }


        DB::table('factura')->insert($facturas);
        $this->command->info(count($facturas) . ' facturas de ejemplo creadas.');
    }
}
