<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuario base para pruebas manuales
        User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@rutx.test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);


        // 2. Generar 10 usuarios y 50 clientes simulados
        User::factory(10)->create();
        Customer::factory(50)->create();
    }
}
