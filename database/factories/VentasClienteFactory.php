<?php

namespace Database\Factories;

use Faker\Factory as Faker;

class VentasClienteFactory
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create('es_MX');
        $this->faker->seed(9912); // Semilla fija para consistencia de datos de prueba
    }

    /**
     * Genera una lista de movimientos/transacciones de venta ficticios.
     */
    public function make(int $count = 30): array
    {
        $results = [];
        
        $zonas = ['1Z - SUR', '2Z - NORTE', '3Z - ORIENTE', '4Z - PONIENTE'];
        $vendedores = ['695 - VENDEDOR', '3345 - VDOS', '7621 - RUTA_ALE', '7853 - MIGUEL ANGEL', '8364 - RUTA ZONA SUR', '9448 - URIEL'];
        
        $clientes = [
            '1Z - SUR' => [
                ['codigo' => '2540', 'nombre' => 'CLIENTE A CREDITO 02'],
                ['codigo' => '2552', 'nombre' => 'CLIENTE A CREDITO 06'],
            ],
            '2Z - NORTE' => [
                ['codigo' => '2537', 'nombre' => 'CLIENTE A CREDITO 01'],
                ['codigo' => '2549', 'nombre' => 'CLIENTE A CREDITO 05'],
                ['codigo' => '700', 'nombre' => 'ALBERT COTA'],
            ],
            '3Z - ORIENTE' => [
                ['codigo' => '2543', 'nombre' => 'CLIENTE A CREDITO 03'],
                ['codigo' => '2555', 'nombre' => 'CLIENTE A CREDITO 07'],
            ],
            '4Z - PONIENTE' => [
                ['codigo' => '2546', 'nombre' => 'CLIENTE A CREDITO 04'],
                ['codigo' => '2558', 'nombre' => 'CLIENTE A CREDITO 08'],
            ]
        ];

        $comentarios = [
            'Entregado en tiempo y forma',
            'Pago registrado contra entrega',
            'Factura enviada al cliente por correo',
            'Cliente solicita crédito de 15 días',
            'Mercancía especial solicitada previamente',
            'Entrega parcial de productos terminados',
            'Ninguno',
            'Pendiente de firma de recibido',
        ];

        for ($i = 0; $i < $count; $i++) {
            if ($i < 3) {
                // Forzar 3 registros de hoy para el cliente inicial real
                $zona = '2Z - NORTE';
                $clienteData = ['codigo' => '2537', 'nombre' => 'CLIENTE A CREDITO 01'];
                $clienteFull = '2537 - CLIENTE A CREDITO 01';
                $vendedor = '695 - VENDEDOR';
                $fecha = '2018-09-19'; // Fecha Microsip
                
                // Mapear exactamente las facturas reales encontradas
                if ($i === 0) {
                    $movimiento = 'Venta Factura';
                    $folio = 'ACH000058';
                    $total = 6700.00;
                    $subtotal = 5775.86;
                } elseif ($i === 1) {
                    $movimiento = 'Venta Factura';
                    $folio = 'ACH000059';
                    $total = 47200.00;
                    $subtotal = 40689.66;
                } else {
                    $movimiento = 'Pedido Sincronizado';
                    $folio = 'PED-9102';
                    $total = 1200.00;
                    $subtotal = 1034.48;
                }
                $comentario = 'Sincronizado desde ERP Microsip';
            } else {
                $zona = $this->faker->randomElement($zonas);
                // Elegir un cliente aleatorio correspondiente a la zona seleccionada
                $clienteData = $this->faker->randomElement($clientes[$zona]);
                $clienteFull = $clienteData['codigo'] . ' - ' . $clienteData['nombre'];
                $vendedor = $this->faker->randomElement($vendedores);
                $fecha = $this->faker->dateTimeBetween('-5 days', 'now')->format('Y-m-d');
                
                $movimiento = $this->faker->randomElement(['Venta Factura', 'Venta Remisión', 'Pedido Sincronizado']);
                $tipoPrefijo = match ($movimiento) {
                    'Venta Factura' => 'FAC',
                    'Venta Remisión' => 'REM',
                    default => 'PED',
                };
                $folio = $tipoPrefijo . '-' . $this->faker->unique()->numberBetween(10000, 99999);
                
                $subtotal = $this->faker->randomFloat(2, 500, 45000);
                $total = round($subtotal * 1.16, 2); // 16% IVA
                $comentario = $this->faker->randomElement($comentarios);
            }

            $results[] = [
                'folio' => $folio,
                'movimiento' => $movimiento,
                'fecha' => $fecha,
                'vendedor' => $vendedor,
                'zona' => $zona,
                'cliente_codigo' => $clienteData['codigo'],
                'cliente_nombre' => $clienteData['nombre'],
                'cliente' => $clienteFull,
                'subtotal' => $subtotal,
                'total' => $total,
                'comentario' => $comentario,
            ];
        }

        // Ordenar movimientos por fecha descendente
        usort($results, fn($a, $b) => strcmp($b['fecha'], $a['fecha']));

        return $results;
    }
}
