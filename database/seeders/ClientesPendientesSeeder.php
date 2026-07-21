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
            [
                'Zona',
                'Ruta',
                'Cliente',
                'Dirección',
                'Tipo Agenda',
                'Estatus',
                'Borrado'
            ],
            array_map(fn($d) => [

                $d['zona'],
                $d['ruta'],
                $d['cliente'],
                $d['direccion'],
                $d['tipo_agenda'],
                $d['estatus'],
                $d['borrado'],

            ], $datos)
        );
    }
}
