<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\Invoice;
use Faker\Factory as Faker;

class PruebaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tablas para evitar duplicados
        Invoice::truncate();
        Customer::truncate();
        Seller::truncate();
        Zone::truncate();

        // 1. Zonas
        $zonas = [
            ['id' => '99-PRUEBA', 'name' => 'ZONA PRUEBA'],
        ];
        foreach ($zonas as $z) {
            Zone::create($z);
        }

        // 2. Vendedores
        $vendedores = [
            ['id' => '999001 - VENDEDOR PRUEBA', 'name' => 'VENDEDOR PRUEBA', 'oculto' => 'N'],
        ];
        foreach ($vendedores as $v) {
            Seller::create($v);
        }

        // 3. Clientes
        $clientes = [
            [
                'id' => '999001 - CLIENTE PRUEBA 01',
                'clave' => '999001',
                'nombre' => 'CLIENTE PRUEBA 01',
                'direccion' => 'Calle Uno 1, Col. Uno, CP 29200',
                'rfc' => 'XAXX010101000',
                'telefono' => '5512345678',
                'plazo' => '15 días',
                'limite' => 1000000.00,
                'saldo' => 52400.00,
                'zona_id' => '99-PRUEBA'
            ],
            [
                'id' => '999002 - CLIENTE PRUEBA 02',
                'clave' => '999002',
                'nombre' => 'CLIENTE PRUEBA 02',
                'direccion' => 'Calle Uno 1, Col. Uno, CP 29200',
                'rfc' => 'XAXX010101000',
                'telefono' => '5512345678',
                'plazo' => '30 días',
                'limite' => 1000000.00,
                'saldo' => 53500.00,
                'zona_id' => '99-PRUEBA'
            ],
            [
                'id' => '999003 - CLIENTE PRUEBA 03',
                'clave' => '999003',
                'nombre' => 'CLIENTE PRUEBA 03',
                'direccion' => 'Calle Uno 1, Col. Uno, CP 29200',
                'rfc' => 'XAXX010101000',
                'telefono' => '5512345678',
                'plazo' => '8 días',
                'limite' => 1000000.00,
                'saldo' => 10000.00,
                'zona_id' => '99-PRUEBA'
            ],
            [
                'id' => '999004 - CLIENTE PRUEBA 04',
                'clave' => '999004',
                'nombre' => 'CLIENTE PRUEBA 04',
                'direccion' => 'Calle Uno 1, Col. Uno, CP 29200',
                'rfc' => 'XAXX010101000',
                'telefono' => '5522119988',
                'plazo' => 'Contado',
                'limite' => 1000000.00,
                'saldo' => 14000.00,
                'zona_id' => '99-PRUEBA'
            ],
        ];
        foreach ($clientes as $c) {
            Customer::create($c);
        }

        // 4. Invoices Reales/Fijos (de chocolates.fdb)
        $invoicesFijos = [
            // Cliente 999001
            [
                'folio' => 'PRU000001',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999001 - CLIENTE PRUEBA 01',
                'subtotal' => 5775.86,
                'total' => 6700.00,
                'abono' => 1500.00,
                'saldo' => 5200.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            [
                'folio' => 'PRU000002',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999001 - CLIENTE PRUEBA 01',
                'subtotal' => 40689.66,
                'total' => 47200.00,
                'abono' => 0.00,
                'saldo' => 47200.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            [
                'folio' => 'PRU000003',
                'movimiento' => 'Pedido Sincronizado',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999001 - CLIENTE PRUEBA 01',
                'subtotal' => 1034.48,
                'total' => 1200.00,
                'abono' => 0.00,
                'saldo' => 1200.00,
                'comentario' => 'Pedido de prueba'
            ],
            // Cliente 999002
            [
                'folio' => 'PRU000004',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999002 - CLIENTE PRUEBA 02',
                'subtotal' => 31724.14,
                'total' => 36800.00,
                'abono' => 0.00,
                'saldo' => 36800.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            [
                'folio' => 'PRU000005',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999002 - CLIENTE PRUEBA 02',
                'subtotal' => 14396.55,
                'total' => 16700.00,
                'abono' => 0.00,
                'saldo' => 16700.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            // Cliente 999003
            [
                'folio' => 'PRU000006',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999003 - CLIENTE PRUEBA 03',
                'subtotal' => 8620.69,
                'total' => 10000.00,
                'abono' => 0.00,
                'saldo' => 10000.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            // Cliente 999004
            [
                'folio' => 'PRU000007',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '999001 - VENDEDOR PRUEBA',
                'zona_id' => '99-PRUEBA',
                'customer_id' => '999004 - CLIENTE PRUEBA 04',
                'subtotal' => 12068.97,
                'total' => 14000.00,
                'abono' => 0.00,
                'saldo' => 14000.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ]
        ];
        foreach ($invoicesFijos as $inf) {
            Invoice::create($inf);
        }

        // 5. Invoices Mock Adicionales para rellenar (solo en entorno que no sea testing)
        if (app()->environment('testing')) {
            return;
        }

        $faker = Faker::create();
        $movimientos = ['Venta Factura', 'Venta Remisión', 'Pedido Sincronizado'];
        $comentarios = [
            'Entregado en tiempo y forma',
            'Pago registrado contra entrega',
            'Factura enviada al cliente por correo',
            'Cliente solicita crédito de 15 días',
            'Mercancía especial solicitada previamente',
            'Entrega parcial de productos terminados',
            'Ninguno',
        ];

        for ($i = 0; $i < 50; $i++) {
            $cliente = $faker->randomElement($clientes);
            $vendedor = $faker->randomElement($vendedores);
            $mov = $faker->randomElement($movimientos);
            $prefijo = match ($mov) {
                'Venta Factura' => 'FAC',
                'Venta Remisión' => 'REM',
                default => 'PED',
            };
            $subtotal = $faker->randomFloat(2, 500, 30000);
            $total = round($subtotal * 1.16, 2);

            Invoice::create([
                'folio' => $prefijo . '-' . $faker->unique()->numberBetween(10000, 99999),
                'movimiento' => $mov,
                'fecha' => $faker->dateTimeBetween('-10 days', 'now')->format('Y-m-d'),
                'vendedor_id' => $vendedor['id'],
                'zona_id' => $cliente['zona_id'],
                'customer_id' => $cliente['id'],
                'subtotal' => $subtotal,
                'total' => $total,
                'abono' => 0.00,
                'saldo' => $total,
                'comentario' => $faker->randomElement($comentarios)
            ]);
        }
    }
}
