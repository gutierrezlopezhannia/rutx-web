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
            ['id' => 'ZONA-01', 'name' => 'ZONA NORTE'],
            ['id' => 'ZONA-02', 'name' => 'ZONA SUR'],
        ];
        foreach ($zonas as $z) {
            Zone::create($z);
        }

        // 2. Vendedores
        $vendedores = [
            ['id' => '999001 - VENDEDOR PRUEBA', 'name' => 'VENDEDOR PRUEBA', 'oculto' => 'N'],
            ['id' => 'RUTA-01 - VENDEDOR NORTE', 'name' => 'VENDEDOR NORTE', 'oculto' => 'N'],
            ['id' => 'RUTA-02 - VENDEDOR SUR', 'name' => 'VENDEDOR SUR', 'oculto' => 'N'],
        ];
        foreach ($vendedores as $v) {
            Seller::create($v);
        }

        // 3. Clientes
        $clientes = [
            // Clientes Originales (para que pasen las pruebas)
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
            // Nuevos Clientes en Zonas
            [
                'id' => '999005 - CLIENTE NORTE 01',
                'clave' => '999005',
                'nombre' => 'CLIENTE NORTE 01',
                'direccion' => 'Calle Norte 1, Col. Norte, CP 54000',
                'rfc' => 'XAXX010101000',
                'telefono' => '5512345678',
                'plazo' => '15 días',
                'limite' => 1000000.00,
                'saldo' => 20000.00,
                'zona_id' => 'ZONA-01'
            ],
            [
                'id' => '999006 - CLIENTE SUR 01',
                'clave' => '999006',
                'nombre' => 'CLIENTE SUR 01',
                'direccion' => 'Calle Sur 1, Col. Sur, CP 98000',
                'rfc' => 'XAXX010101000',
                'telefono' => '5512345678',
                'plazo' => 'Contado',
                'limite' => 1000000.00,
                'saldo' => 30000.00,
                'zona_id' => 'ZONA-02'
            ]
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
            ],
            // Cliente 999005 (Zona NORTE, Vendedor NORTE)
            [
                'folio' => 'PRU000008',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => 'RUTA-01 - VENDEDOR NORTE',
                'zona_id' => 'ZONA-01',
                'customer_id' => '999005 - CLIENTE NORTE 01',
                'subtotal' => 5000.00,
                'total' => 5800.00,
                'abono' => 800.00,
                'saldo' => 5000.00,
                'comentario' => 'Sincronizado Zona Norte'
            ],
            // Cliente 999006 (Zona SUR, Vendedor SUR)
            [
                'folio' => 'PRU000009',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => 'RUTA-02 - VENDEDOR SUR',
                'zona_id' => 'ZONA-02',
                'customer_id' => '999006 - CLIENTE SUR 01',
                'subtotal' => 15000.00,
                'total' => 17400.00,
                'abono' => 0.00,
                'saldo' => 17400.00,
                'comentario' => 'Sincronizado Zona Sur'
            ],
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
            'Mercancía especial solicitada',
            'Ninguno',
        ];

        // Mapeo de cliente a su vendedor correspondiente por zona
        $vendedorPorZona = [
            '99-PRUEBA' => '999001 - VENDEDOR PRUEBA',
            'ZONA-01' => 'RUTA-01 - VENDEDOR NORTE',
            'ZONA-02' => 'RUTA-02 - VENDEDOR SUR',
        ];

        for ($i = 0; $i < 60; $i++) {
            $cliente = $faker->randomElement($clientes);
            $zonaId = $cliente['zona_id'];
            $vendedorId = $vendedorPorZona[$zonaId];
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
                'vendedor_id' => $vendedorId,
                'zona_id' => $zonaId,
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
