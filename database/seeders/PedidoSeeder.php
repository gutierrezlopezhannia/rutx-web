<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\PedidoFactory;

class PedidoSeeder extends Seeder
{
    public function run(): void
    {
        $factory = new PedidoFactory();
        $datos = $factory->make(30);

        $this->command->info("Se generaron " . count($datos) . " pedidos (mock data).");
        $this->command->newLine();

        $this->command->table(
            ['Ruta', 'Vendedor', 'Cliente', 'Estado', 'Tipo de Pago', 'Fecha y Hora'],
            array_map(fn($d) => [
                $d['ruta'],
                $d['vendedor'],
                $d['cliente'],
                $d['estado'],
                $d['tipo_pago'],
                $d['fecha_hora'],
            ], $datos)
        );
    }
}
