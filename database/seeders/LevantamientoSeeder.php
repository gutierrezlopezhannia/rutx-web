<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\LevantamientoFactory;

class LevantamientoSeeder extends Seeder
{
    public function run(): void
    {
        $factory = new LevantamientoFactory();
        $datos = $factory->make(30);

        $this->command->info("Se generaron " . count($datos) . " registros de levantamiento (mock data).");
        $this->command->newLine();

        $this->command->table(
            ['Ruta', 'Vendedor', 'Cliente', 'Tipo', 'Fecha y Hora'],
            array_map(fn($d) => [
                $d['ruta'],
                $d['vendedor'],
                $d['cliente'],
                $d['tipo'],
                $d['fecha_hora'],
            ], $datos)
        );
    }
}
