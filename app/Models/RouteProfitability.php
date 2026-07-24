<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteProfitability extends Model
{
    use HasFactory;

    protected $table = 'route_profitabilities';

    protected $fillable = [
        'zona_id',
        'ruta_id',
        'fecha',
        'vendedor',
        'ventas_netas',
        'ventas_contado',
        'ventas_credito',
        'preventas',
        'costo_compra_preventa',
        'clientes_preventa',
        'entregas',
        'costo_producto',
        'devolucion_contado',
        'dias',
        'gastos_operativos',
        'costo_venta',
        'utilidad_ruta',
        'porcentaje'
    ];

    protected $attributes = [
        'ventas_netas' => 0,
        'ventas_contado' => 0,
        'ventas_credito' => 0,
        'preventas' => 0,
        'costo_compra_preventa' => 0,
        'entregas' => 0,
        'costo_producto' => 0,
        'devolucion_contado' => 0,
        'dias' => 0,
        'gastos_operativos' => 0,
        'costo_venta' => 0,
        'utilidad_ruta' => 0,
        'porcentaje' => 0,
    ];
}
