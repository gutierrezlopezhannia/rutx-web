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
        $zonas = ['1Z - Zona 1', '2Z - Zona 2', '3Z - Zona 3'];
        $vendedores = ['4686 - RUTA06', '3201 - RUTA01', '4102 - RUTA02', '5210 - RUTA03', '6100 - RUTA07'];
        $movimientos = ['Venta', 'Preventa'];
        $tipos_venta = ['Contado', 'Crédito'];

        $subtotal = $this->faker->randomFloat(2, 100, 5000);
        $impuesto = round($subtotal * 0.00, 2);
        $total    = $subtotal + $impuesto;

        return [
            'zona'       => $this->faker->randomElement($zonas),
            'vendedor'   => $this->faker->randomElement($vendedores),
            'cliente'    => strtoupper($this->faker->company()),
            'movimiento' => $this->faker->randomElement($movimientos),
            'tipo_venta' => $this->faker->randomElement($tipos_venta),
            'subtotal'   => $subtotal,
            'impuesto'   => $impuesto,
            'total'      => $total,
            'fecha'      => $this->faker->dateTimeBetween('-7 days', 'now')->format('Y-m-d'),
        ];
    }
}
