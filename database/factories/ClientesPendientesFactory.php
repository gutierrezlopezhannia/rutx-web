<?php

namespace Database\Factories;

use Faker\Factory as Faker;

class ClientesPendientesFactory
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create('es_MX');
        $this->faker->seed(9012);
    }

    public function make(int $count = 1): array
    {
        $results = [];
        for ($i = 0; $i < $count; $i++) {
            $results[] = $this->makeOne();
        }
        return $results;
    }

    public function makeOne(): array
    {
        return [
            'zona' => '1Z - Zona 1',

            'ruta' => $this->faker->randomElement([
                '3983 - RUTA01',
                '3984 - RUTA02',
                '3985 - RUTA03',
            ]),

            'cliente' => $this->faker->company(),

            'direccion' => $this->faker->streetAddress(),

            'estatus' => $this->faker->randomElement([
                'Activo',
                'Inactivo'
            ]),

            'borrado' => $this->faker->randomElement([
                'Sí',
                'No'
            ]),

            'tipo_agenda' => $this->faker->randomElement([
                'Agenda General',
                'Agenda de Entrega'
            ]),

            'fecha' => now()->format('Y-m-d'),
        ];
    }
}
