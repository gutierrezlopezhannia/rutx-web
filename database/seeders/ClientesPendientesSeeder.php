<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\ClientesPendientesFactory;

class ClientesPendientesSeeder extends Seeder
{
    public function run(): void
    {
        $factory = new ClientesPendientesFactory();
        $datos = $factory->make(30);

        $this->command->info("Se generaron " . count($datos) . " clientes pendientes (mock data).");
        $this->command->newLine();

        $this->command->table(
            ['Ruta', 'Vendedor', 'Cliente', 'Orden', 'Estado', 'Fecha'],
            array_map(fn($d) => [
                $d['ruta'],
                $d['vendedor'],
                $d['cliente'],
                $d['orden'],
                $d['estado'],
                $d['fecha'],
            ], $datos)
        );
    }
}
