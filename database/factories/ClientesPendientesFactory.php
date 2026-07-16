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
            'ruta'     => $this->faker->randomElement(['Ruta 1', 'Ruta 2', 'Ruta 3']),
            'vendedor' => $this->faker->randomElement(['Ana María', 'Carlos Díaz', 'Jorge Pérez']),
            'cliente'  => $this->faker->company(),
            'orden'    => $this->faker->numberBetween(1, 30),
            'estado'   => 'Pendiente',
            'fecha'    => $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
        ];
    }
}
