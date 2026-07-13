<?php

namespace Database\Factories;

use Faker\Factory as Faker;

class CobranzaFactory
{
    protected $faker;
    public function __construct()
    {
        $this->faker = Faker::create('es_MX');
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
        $montoTotal = $this->faker->randomFloat(2, 1000, 80000);
        $estado = $this->faker->randomElement(['Pagado', 'Parcial', 'Pendiente', 'Vencido']);

        $montoPagado = match ($estado) {
            'Pagado' => $montoTotal,
            'Parcial' => round($montoTotal * $this->faker->randomFloat(2, 0.3, 0.8), 2),
            default => 0,
        };

        return [
            'id' => 'FAC-' . str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'cliente' => $this->faker->company(),
            'fecha' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'monto_total' => $montoTotal,
            'monto_pagado' => $montoPagado,
            'estado' => $estado,
            'metodo_pago' => $this->faker->randomElement(['Efectivo', 'Transferencia', 'Cheque', 'Credito']),
            'vendedor' => $this->faker->randomElement(['Carlos Ruiz', 'Ana Lopez', 'Miguel Torres', 'Laura Garcia', 'Pedro Sanchez']),
            'referencia' => $estado !== 'Pendiente' ? strtoupper($this->faker->bothify('??-####')) : null,
        ];
    }
}
