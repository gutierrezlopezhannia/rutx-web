<?php

namespace Database\Factories;

use Faker\Factory as Faker;

class LevantamientoFactory
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create('es_MX');
        $this->faker->seed(1234);
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
        $tipo = $this->faker->randomElement(['Venta', 'Visita sin venta']);

        return [
            'ruta' => $this->faker->randomElement(['Ruta 1', 'Ruta 2', 'Ruta 3']),
            'vendedor' => $this->faker->randomElement(['Ana María', 'Carlos Díaz', 'Jorge Pérez']),
            'cliente' => $this->faker->company(),
            'tipo' => $tipo,
            'fecha_hora' => $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d h:i A'),
            'latitud' => $this->faker->latitude(19.0, 20.0),
            'longitud' => $this->faker->longitude(-99.5, -99.0),
        ];
    }
}
