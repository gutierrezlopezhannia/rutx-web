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

        // 1. Datos base para Pruebas Automatizadas (Sandbox de Tests)
        Zone::create(['id' => '99-PRUEBA', 'name' => 'ZONA PRUEBA']);
        
        Seller::create(['id' => '999001 - VENDEDOR PRUEBA', 'name' => 'VENDEDOR PRUEBA', 'oculto' => 'N']);

        $clientesPrueba = [
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
            ]
        ];
        foreach ($clientesPrueba as $c) {
            Customer::create($c);
        }

        $invoicesPrueba = [
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
        foreach ($invoicesPrueba as $inf) {
            Invoice::create($inf);
        }

        // 2. Datos reales de CHOCOLATES.fdb (Sólo se cargan en Desarrollo, NO en Testing)
        if (app()->environment('testing')) {
            return;
        }

        // Zonas adicionales
        $zonasReales = [
            ['id' => '1Z - SUR', 'name' => 'ZONA SUR'],
            ['id' => '2Z - NORTE', 'name' => 'ZONA NORTE'],
            ['id' => '3Z - ORIENTE', 'name' => 'ZONA ORIENTE'],
            ['id' => '4Z - PONIENTE', 'name' => 'ZONA PONIENTE'],
        ];
        foreach ($zonasReales as $zr) {
            Zone::create($zr);
        }

        // Vendedores adicionales
        $vendedoresReales = [
            ['id' => '695 - VENDEDOR', 'name' => 'VENDEDOR', 'oculto' => 'N'],
            ['id' => '3345 - VDOS', 'name' => 'VDOS', 'oculto' => 'N'],
            ['id' => '7621 - RUTA_ALE', 'name' => 'RUTA ALE', 'oculto' => 'N'],
            ['id' => '7853 - MIGUEL ANGEL', 'name' => 'MIGUEL ANGEL', 'oculto' => 'N'],
            ['id' => '8364 - RUTA ZONA SUR', 'name' => 'RUTA ZONA SUR', 'oculto' => 'N'],
            ['id' => '9448 - URIEL', 'name' => 'URIEL', 'oculto' => 'N'],
        ];
        foreach ($vendedoresReales as $vr) {
            Seller::create($vr);
        }

        // Clientes adicionales
        $clientesReales = [
            [
                'id' => '2540 - CLIENTE A CREDITO 02',
                'clave' => '2540',
                'nombre' => 'CLIENTE A CREDITO 02',
                'direccion' => 'Domicilio Conocido, Zona Sur',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000001',
                'plazo' => '15 días',
                'limite' => 50000.00,
                'saldo' => 12000.00,
                'zona_id' => '1Z - SUR'
            ],
            [
                'id' => '2552 - CLIENTE A CREDITO 06',
                'clave' => '2552',
                'nombre' => 'CLIENTE A CREDITO 06',
                'direccion' => 'Domicilio Conocido, Zona Sur',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000002',
                'plazo' => '30 días',
                'limite' => 60000.00,
                'saldo' => 18000.00,
                'zona_id' => '1Z - SUR'
            ],
            [
                'id' => '2537 - CLIENTE A CREDITO 01',
                'clave' => '2537',
                'nombre' => 'CLIENTE A CREDITO 01',
                'direccion' => 'Domicilio Conocido, Zona Norte',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000003',
                'plazo' => '15 días',
                'limite' => 100000.00,
                'saldo' => 24000.00,
                'zona_id' => '2Z - NORTE'
            ],
            [
                'id' => '2549 - CLIENTE A CREDITO 05',
                'clave' => '2549',
                'nombre' => 'CLIENTE A CREDITO 05',
                'direccion' => 'Domicilio Conocido, Zona Norte',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000004',
                'plazo' => '30 días',
                'limite' => 80000.00,
                'saldo' => 15000.00,
                'zona_id' => '2Z - NORTE'
            ],
            [
                'id' => '700 - ALBERT COTA',
                'clave' => '700',
                'nombre' => 'ALBERT COTA',
                'direccion' => 'Domicilio Conocido, Zona Norte',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000005',
                'plazo' => 'Contado',
                'limite' => 15000.00,
                'saldo' => 0.00,
                'zona_id' => '2Z - NORTE'
            ],
            [
                'id' => '2543 - CLIENTE A CREDITO 03',
                'clave' => '2543',
                'nombre' => 'CLIENTE A CREDITO 03',
                'direccion' => 'Domicilio Conocido, Zona Oriente',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000006',
                'plazo' => '8 días',
                'limite' => 45000.00,
                'saldo' => 8000.00,
                'zona_id' => '3Z - ORIENTE'
            ],
            [
                'id' => '2555 - CLIENTE A CREDITO 07',
                'clave' => '2555',
                'nombre' => 'CLIENTE A CREDITO 07',
                'direccion' => 'Domicilio Conocido, Zona Oriente',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000007',
                'plazo' => '15 días',
                'limite' => 50000.00,
                'saldo' => 5000.00,
                'zona_id' => '3Z - ORIENTE'
            ],
            [
                'id' => '2546 - CLIENTE A CREDITO 04',
                'clave' => '2546',
                'nombre' => 'CLIENTE A CREDITO 04',
                'direccion' => 'Domicilio Conocido, Zona Poniente',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000008',
                'plazo' => 'Contado',
                'limite' => 30000.00,
                'saldo' => 14000.00,
                'zona_id' => '4Z - PONIENTE'
            ],
            [
                'id' => '2558 - CLIENTE A CREDITO 08',
                'clave' => '2558',
                'nombre' => 'CLIENTE A CREDITO 08',
                'direccion' => 'Domicilio Conocido, Zona Poniente',
                'rfc' => 'XAXX010101000',
                'telefono' => '5500000009',
                'plazo' => '30 días',
                'limite' => 70000.00,
                'saldo' => 9000.00,
                'zona_id' => '4Z - PONIENTE'
            ],
        ];
        foreach ($clientesReales as $cr) {
            Customer::create($cr);
        }

        // Invoices reales iniciales
        $invoicesReales = [
            [
                'folio' => 'ACH000058',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '695 - VENDEDOR',
                'zona_id' => '2Z - NORTE',
                'customer_id' => '2537 - CLIENTE A CREDITO 01',
                'subtotal' => 5775.86,
                'total' => 6700.00,
                'abono' => 1500.00,
                'saldo' => 5200.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            [
                'folio' => 'ACH000059',
                'movimiento' => 'Venta Factura',
                'fecha' => '2018-09-19',
                'vendedor_id' => '695 - VENDEDOR',
                'zona_id' => '2Z - NORTE',
                'customer_id' => '2537 - CLIENTE A CREDITO 01',
                'subtotal' => 40689.66,
                'total' => 47200.00,
                'abono' => 0.00,
                'saldo' => 47200.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ],
            [
                'folio' => 'PED-9102',
                'movimiento' => 'Pedido Sincronizado',
                'fecha' => '2018-09-19',
                'vendedor_id' => '695 - VENDEDOR',
                'zona_id' => '2Z - NORTE',
                'customer_id' => '2537 - CLIENTE A CREDITO 01',
                'subtotal' => 1034.48,
                'total' => 1200.00,
                'abono' => 0.00,
                'saldo' => 1200.00,
                'comentario' => 'Sincronizado desde ERP Microsip'
            ]
        ];
        foreach ($invoicesReales as $ir) {
            Invoice::create($ir);
        }

        // Invoices Mock Adicionales para rellenar en desarrollo
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

        $vendedoresIdsReales = array_column($vendedoresReales, 'id');

        for ($i = 0; $i < 120; $i++) {
            $cliente = $faker->randomElement($clientesReales);
            $clienteId = $cliente['id'];
            $zonaId = $cliente['zona_id'];
            $vendedorId = $faker->randomElement($vendedoresIdsReales);

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
                'fecha' => $faker->dateTimeBetween('-20 days', 'now')->format('Y-m-d'),
                'vendedor_id' => $vendedorId,
                'zona_id' => $zonaId,
                'customer_id' => $clienteId,
                'subtotal' => $subtotal,
                'total' => $total,
                'abono' => 0.00,
                'saldo' => $total,
                'comentario' => $faker->randomElement($comentarios)
            ]);
        }

        // Sembrar facturas con la fecha de hoy para consultas predeterminadas del reporte
        // Asegurando que cada combinación de (Zona, Vendedor) tenga datos hoy
        $todayStr = date('Y-m-d');
        $allZones = Zone::all();
        $allSellers = Seller::all();
        
        $comboIndex = 0;
        foreach ($allZones as $zone) {
            $customersInZone = Customer::where('zona_id', $zone->id)->get();
            if ($customersInZone->isEmpty()) {
                continue;
            }
            
            foreach ($allSellers as $seller) {
                for ($j = 1; $j <= 2; $j++) {
                    $customer = $customersInZone->random();
                    $subtotal = 8000.00 + ($comboIndex * 150) + ($j * 100) + rand(100, 1000);
                    $total = round($subtotal * 1.16, 2);
                    
                    Invoice::create([
                        'folio' => 'TOD-CB-' . $comboIndex . '-' . $j,
                        'movimiento' => 'Venta Factura',
                        'fecha' => $todayStr,
                        'vendedor_id' => $seller->id,
                        'zona_id' => $zone->id,
                        'customer_id' => $customer->id,
                        'subtotal' => $subtotal,
                        'total' => $total,
                        'abono' => 0.00,
                        'saldo' => $total,
                        'comentario' => "Factura hoy para {$zone->id} y {$seller->id}"
                    ]);
                }
                $comboIndex++;
            }
        }
    }
}
