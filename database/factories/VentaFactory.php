<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VentaFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 500, 50000);

        return [
            'cliente_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory(),
            'vendedor_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'fecha' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'impuestos' => round($subtotal * 0.16, 2),
            'total' => round($subtotal * 1.16, 2),
            'estado' => $this->faker->randomElement(['Completada', 'Pendiente', 'Cancelada']),
            'tipo' => $this->faker->randomElement(['Contado', 'Credito']),
        ];
    }
}
