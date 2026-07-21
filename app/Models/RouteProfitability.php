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
}
