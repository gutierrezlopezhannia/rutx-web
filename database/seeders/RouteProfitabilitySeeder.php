<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RouteProfitability;

class RouteProfitabilitySeeder extends Seeder
{
    public function run(): void
    {
        RouteProfitability::truncate();

        // 1. Datos que coinciden exactamente con el esquema de pruebas de RUTX (usado en PruebaSeeder)
        // Sembrado para la fecha histórica '2018-09-19'
        RouteProfitability::create([
            'zona_id' => '99-PRUEBA',
            'ruta_id' => '999001 - VENDEDOR PRUEBA',
            'fecha' => '2018-09-19',
            'vendedor' => '999001 - VENDEDOR PRUEBA',
            'ventas_netas' => 115900.00,
            'ventas_contado' => 65000.00,
            'ventas_credito' => 50900.00,
            'preventas' => 84500.00,
            'costo_compra_preventa' => 56000.00,
            'clientes_preventa' => 8,
            'entregas' => 84500.00,
            'costo_producto' => 28000.00,
            'devolucion_contado' => 450.00,
            'dias' => 3,
            'gastos_operativos' => 12500.00,
            'costo_venta' => 68000.00,
            'utilidad_ruta' => 35400.00,
            'porcentaje' => 30.54,
        ]);

        RouteProfitability::create([
            'zona_id' => '99-PRUEBA',
            'ruta_id' => '999002 - RUTA AUXILIAR',
            'fecha' => '2018-09-19',
            'vendedor' => '999002 - RUTA AUXILIAR',
            'ventas_netas' => 45000.00,
            'ventas_contado' => 30000.00,
            'ventas_credito' => 15000.00,
            'preventas' => 35000.00,
            'costo_compra_preventa' => 22000.00,
            'clientes_preventa' => 4,
            'entregas' => 35000.00,
            'costo_producto' => 12000.00,
            'devolucion_contado' => 0.00,
            'dias' => 1,
            'gastos_operativos' => 5000.00,
            'costo_venta' => 26000.00,
            'utilidad_ruta' => 14000.00,
            'porcentaje' => 31.11,
        ]);

        // Sembrado para la fecha actual de desarrollo del mockup '2026-07-21' (para que funcione directamente sin cambiar el calendario)
        RouteProfitability::create([
            'zona_id' => '99-PRUEBA',
            'ruta_id' => '999001 - VENDEDOR PRUEBA',
            'fecha' => '2026-07-21',
            'vendedor' => '999001 - VENDEDOR PRUEBA',
            'ventas_netas' => 115900.00,
            'ventas_contado' => 65000.00,
            'ventas_credito' => 50900.00,
            'preventas' => 84500.00,
            'costo_compra_preventa' => 56000.00,
            'clientes_preventa' => 8,
            'entregas' => 84500.00,
            'costo_producto' => 28000.00,
            'devolucion_contado' => 450.00,
            'dias' => 3,
            'gastos_operativos' => 12500.00,
            'costo_venta' => 68000.00,
            'utilidad_ruta' => 35400.00,
            'porcentaje' => 30.54,
        ]);

        RouteProfitability::create([
            'zona_id' => '99-PRUEBA',
            'ruta_id' => '999002 - RUTA AUXILIAR',
            'fecha' => '2026-07-21',
            'vendedor' => '999002 - RUTA AUXILIAR',
            'ventas_netas' => 45000.00,
            'ventas_contado' => 30000.00,
            'ventas_credito' => 15000.00,
            'preventas' => 35000.00,
            'costo_compra_preventa' => 22000.00,
            'clientes_preventa' => 4,
            'entregas' => 35000.00,
            'costo_producto' => 12000.00,
            'devolucion_contado' => 0.00,
            'dias' => 1,
            'gastos_operativos' => 5000.00,
            'costo_venta' => 26000.00,
            'utilidad_ruta' => 14000.00,
            'porcentaje' => 31.11,
        ]);


        // 2. Datos mock oficiales bajo la Zona '1Z - Zona 1' de las capturas para el día '2026-07-21'
        // Generamos 25 rutas con algunos datos simulados para que tengan ventas y la paginación sea atractiva
        for ($i = 1; $i <= 25; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $ruta = "3983 - RUTA{$num}";
            
            // Variamos un poco las ventas de cada ruta para que no todas sean cero y se vea real
            $ventas = $i * 5000.00;
            $contado = $ventas * 0.6;
            $credito = $ventas * 0.4;
            $preventas = $ventas * 0.8;
            $costo_prev = $preventas * 0.65;
            $clientes = $i % 2 === 0 ? 4 : 8;
            $entregas = $preventas;
            $costo_prod = $ventas * 0.25;
            $devolucion = $i % 5 === 0 ? 250.00 : 0.00;
            $dias = $i % 3 === 0 ? 2 : 1;
            $gastos = $ventas * 0.1;
            $costo_venta = $ventas * 0.55;
            $utilidad = $ventas - $costo_venta - $gastos;
            $porcentaje = $ventas > 0 ? ($utilidad / $ventas) * 100 : 0;

            RouteProfitability::create([
                'zona_id' => '1Z - Zona 1',
                'ruta_id' => $ruta,
                'fecha' => '2026-07-21',
                'vendedor' => $ruta,
                'ventas_netas' => $ventas,
                'ventas_contado' => $contado,
                'ventas_credito' => $credito,
                'preventas' => $preventas,
                'costo_compra_preventa' => $costo_prev,
                'clientes_preventa' => $clientes,
                'entregas' => $entregas,
                'costo_producto' => $costo_prod,
                'devolucion_contado' => $devolucion,
                'dias' => $dias,
                'gastos_operativos' => $gastos,
                'costo_venta' => $costo_venta,
                'utilidad_ruta' => $utilidad,
                'porcentaje' => $porcentaje,
            ]);
        }

        // Datos de prueba adicionales para 1Z - Zona 1 en fechas anteriores
        RouteProfitability::create([
            'zona_id' => '1Z - Zona 1',
            'ruta_id' => '3983 - RUTA01',
            'fecha' => '2026-07-20',
            'vendedor' => 'Ana María',
            'ventas_netas' => 15420.00,
            'ventas_contado' => 9250.00,
            'ventas_credito' => 6170.00,
            'preventas' => 11200.00,
            'costo_compra_preventa' => 7400.00,
            'clientes_preventa' => 5,
            'entregas' => 11200.00,
            'costo_producto' => 3800.00,
            'devolucion_contado' => 320.00,
            'dias' => 1,
            'gastos_operativos' => 1200.00,
            'costo_venta' => 8600.00,
            'utilidad_ruta' => 5620.00,
            'porcentaje' => 36.45,
        ]);
    }
}
