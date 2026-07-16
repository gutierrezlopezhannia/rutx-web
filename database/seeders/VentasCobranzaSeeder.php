<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\CobranzaFactory;

class VentasCobranzaSeeder extends Seeder
{
    public function run(): void
    {
        $factory = new CobranzaFactory();
        $datos = $factory->make(25);

        $this->command->info("Se generaron " . count($datos) . " facturas de cobranza (mock data).");
        $this->command->newLine();

        $this->command->table(
            ['ID', 'Cliente', 'Monto Total', 'Pagado', 'Estado', 'Metodo'],
            array_map(fn($d) => [
                $d['id'],
                $d['cliente'],
                '$' . number_format($d['monto_total'], 2),
                '$' . number_format($d['monto_pagado'], 2),
                $d['estado'],
                $d['metodo_pago'],
            ], $datos)
        );
    }
}
