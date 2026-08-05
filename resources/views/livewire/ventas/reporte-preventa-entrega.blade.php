<?php

use function Livewire\Volt\{state, layout};
use Carbon\Carbon;

layout('layouts.app');

state([
    'filtro_zona' => '1Z - Zona 1',
    'filtro_ruta' => 'todos',
    'fecha_inicio' => '2026-08-03',
    'fecha_fin' => '2026-08-05',
    'agrupar_por' => 'Producto',
    'filtro_lineas' => [],
    'filtro_linea_familia' => 'todos',
    'buscar_folio' => '',
    'tipo_reporte' => 'Preventa -> Entrega',
    'consultado' => false,
    'datosTabla' => [],
    'totalPreventa' => 0.0,
    'totalEntrega' => 0.0,
    'totalDiferencia' => 0.0,
    'zonas' => fn() => \App\Models\Zone::all()->toArray(),
]);

$updatedFiltroZona = function() {
    $this->filtro_ruta = 'todos';
    $this->consultado = false;
    $this->datosTabla = [];
};

$updatedFiltroRuta = function() {
    $this->consultado = false;
    $this->datosTabla = [];
};

$updatedFiltroLinea = function() {
    $this->filtro_linea_familia = 'todos';
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedFiltroLineaFamilia = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedAgruparPor = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedBuscarFolio = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedTipoReporte = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$obtenerRutas = function() {
    if ($this->filtro_zona === 'todos') {
        return \App\Models\Seller::where('oculto', 'N')->get()->toArray();
    }
    
    $vendedorIds = \App\Models\Invoice::where('zona_id', $this->filtro_zona)
        ->pluck('vendedor_id')
        ->unique();
        
    if ($vendedorIds->isEmpty()) {
        if ($this->filtro_zona === '1Z - Zona 1') {
            return \App\Models\Seller::where('id', 'like', '%RUTA%')->get()->toArray();
        }
    }
        
    return \App\Models\Seller::whereIn('id', $vendedorIds)
        ->where('oculto', 'N')
        ->get()
        ->toArray();
};

$obtenerFamilias = function() {
    if (empty($this->filtro_lineas)) {
        return [];
    }
    
    $map = [
        'ALIMENTOS' => ['ENLATADOS', 'GRANOS'],
        'DULCERIA' => ['PASTELILLOS', 'PALETAS', 'CHOCOLATES'],
        'FARMACIA' => ['ANALGESICOS', 'CURACION'],
        'HIG Y DESECHABLE' => ['HIGIENICOS', 'DESECHABLES'],
        'HIG PERSONAL' => ['JABONES', 'DENTAL'],
        'LIMPIEZA' => ['LAVANDERIA', 'DESINFECTANTES'],
        'VARIOS' => ['ACCESORIOS'],
        'MASCOTAS' => ['PERROS', 'GATOS'],
        'BEBIDAS' => ['REFRESCOS', 'AGUAS', 'JUGOS'],
        'CHICHARRINES' => ['BOTANAS'],
        'TIRAS GRANDES' => ['TIRAS'],
        'TIRAS CHICAS' => ['TIRAS'],
    ];
    
    $fams = [];
    foreach ($this->filtro_lineas as $linea) {
        if (isset($map[$linea])) {
            $fams = array_merge($fams, $map[$linea]);
        }
    }
    return array_unique($fams);
};

$toggleLinea = function($linea) {
    if (in_array($linea, $this->filtro_lineas)) {
        $this->filtro_lineas = array_values(array_diff($this->filtro_lineas, [$linea]));
    } else {
        $this->filtro_lineas[] = $linea;
    }
    $this->filtro_linea_familia = 'todos';
};

$obtenerComparativa = function() {
    $query = \App\Models\Invoice::query();
    
    if ($this->filtro_zona !== 'todos') {
        $query->where('zona_id', $this->filtro_zona);
    }
    if ($this->filtro_ruta !== 'todos' && !empty($this->filtro_ruta)) {
        $query->where('vendedor_id', $this->filtro_ruta);
    }
    
    if (!empty($this->fecha_inicio)) {
        $query->where('fecha', '>=', $this->fecha_inicio);
    }
    if (!empty($this->fecha_fin)) {
        $query->where('fecha', '<=', $this->fecha_fin);
    }
    
    $invoices = $query->get();
    
    $productos = [
        // BEBIDAS
        ['id' => 'PROD-901', 'nombre' => 'Coca-Cola 600ml', 'precio' => 18.50, 'linea' => 'BEBIDAS', 'familia' => 'REFRESCOS'],
        ['id' => 'PROD-902', 'nombre' => 'Agua Mineral 1.5L', 'precio' => 22.00, 'linea' => 'BEBIDAS', 'familia' => 'AGUAS'],
        ['id' => 'PROD-903', 'nombre' => 'Jugo de Naranja 1L', 'precio' => 25.00, 'linea' => 'BEBIDAS', 'familia' => 'JUGOS'],
        // ALIMENTOS
        ['id' => 'PROD-101', 'nombre' => 'Atún en Agua 140g', 'precio' => 21.50, 'linea' => 'ALIMENTOS', 'familia' => 'ENLATADOS'],
        ['id' => 'PROD-102', 'nombre' => 'Arroz Súper Extra 1kg', 'precio' => 28.00, 'linea' => 'ALIMENTOS', 'familia' => 'GRANOS'],
        ['id' => 'PROD-103', 'nombre' => 'Frijol Negro 1kg', 'precio' => 35.50, 'linea' => 'ALIMENTOS', 'familia' => 'GRANOS'],
        // DULCERIA
        ['id' => 'PROD-201', 'nombre' => 'Gansito Marinela 50g', 'precio' => 15.50, 'linea' => 'DULCERIA', 'familia' => 'PASTELILLOS'],
        ['id' => 'PROD-202', 'nombre' => 'Paleta Payaso 45g', 'precio' => 18.00, 'linea' => 'DULCERIA', 'familia' => 'PALETAS'],
        ['id' => 'PROD-203', 'nombre' => 'Chocolate Snickers 52g', 'precio' => 24.50, 'linea' => 'DULCERIA', 'familia' => 'CHOCOLATES'],
        // FARMACIA
        ['id' => 'PROD-301', 'nombre' => 'Paracetamol 500mg 10 Tabs', 'precio' => 12.00, 'linea' => 'FARMACIA', 'familia' => 'ANALGESICOS'],
        ['id' => 'PROD-302', 'nombre' => 'Alcohol Etílico 500ml', 'precio' => 30.00, 'linea' => 'FARMACIA', 'familia' => 'CURACION'],
        // HIG Y DESECHABLE
        ['id' => 'PROD-401', 'nombre' => 'Papel Higiénico 4 Rollos', 'precio' => 26.50, 'linea' => 'HIG Y DESECHABLE', 'familia' => 'HIGIENICOS'],
        ['id' => 'PROD-402', 'nombre' => 'Servilletas 100 Piezas', 'precio' => 15.00, 'linea' => 'HIG Y DESECHABLE', 'familia' => 'DESECHABLES'],
        // HIG PERSONAL
        ['id' => 'PROD-501', 'nombre' => 'Jabón de Tocador 150g', 'precio' => 19.50, 'linea' => 'HIG PERSONAL', 'familia' => 'JABONES'],
        ['id' => 'PROD-502', 'nombre' => 'Pasta Dental 100ml', 'precio' => 32.00, 'linea' => 'HIG PERSONAL', 'familia' => 'DENTAL'],
        // LIMPIEZA
        ['id' => 'PROD-601', 'nombre' => 'Detergente en Polvo 1kg', 'precio' => 38.00, 'linea' => 'LIMPIEZA', 'familia' => 'LAVANDERIA'],
        ['id' => 'PROD-602', 'nombre' => 'Cloro Líquido 1L', 'precio' => 17.50, 'linea' => 'LIMPIEZA', 'familia' => 'DESINFECTANTES'],
        // VARIOS
        ['id' => 'PROD-701', 'nombre' => 'Encendedor Multiusos', 'precio' => 20.00, 'linea' => 'VARIOS', 'familia' => 'ACCESORIOS'],
        // MASCOTAS
        ['id' => 'PROD-801', 'nombre' => 'Alimento para Perro 2kg', 'precio' => 110.00, 'linea' => 'MASCOTAS', 'familia' => 'PERROS'],
        ['id' => 'PROD-802', 'nombre' => 'Alimento para Gato 1kg', 'precio' => 65.00, 'linea' => 'MASCOTAS', 'familia' => 'GATOS'],
        // CHICHARRINES
        ['id' => 'PROD-1001', 'nombre' => 'Chicharrón de Cerdo 100g', 'precio' => 35.00, 'linea' => 'CHICHARRINES', 'familia' => 'BOTANAS'],
        ['id' => 'PROD-1002', 'nombre' => 'Papas con Sal 100g', 'precio' => 28.00, 'linea' => 'CHICHARRINES', 'familia' => 'BOTANAS'],
        // TIRAS GRANDES
        ['id' => 'PROD-1101', 'nombre' => 'Tira de Dulces Gde', 'precio' => 45.00, 'linea' => 'TIRAS GRANDES', 'familia' => 'TIRAS'],
        // TIRAS CHICAS
        ['id' => 'PROD-1201', 'nombre' => 'Tira de Dulces Ch', 'precio' => 25.00, 'linea' => 'TIRAS CHICAS', 'familia' => 'TIRAS'],
    ];
    
    $grouped = $invoices->groupBy(function($inv) {
        return $inv->customer_id . '_' . $inv->fecha;
    });
    
    $comparativa = [];
    
    foreach ($grouped as $key => $invs) {
        $pedido = $invs->firstWhere('movimiento', 'Pedido Sincronizado');
        $factura = $invs->first(function($inv) {
            return $inv->movimiento === 'Venta Factura' || $inv->movimiento === 'Venta Remisión';
        });
        
        if (!$pedido && !$factura) {
            continue;
        }
        
        $customer = $pedido ? $pedido->customer : ($factura ? $factura->customer : null);
        $fecha = $pedido ? $pedido->fecha : ($factura ? $factura->fecha : '');
        $vendedor = $pedido ? $pedido->vendedor_id : ($factura ? $factura->vendedor_id : '');
        $zona = $pedido ? $pedido->zona_id : ($factura ? $factura->zona_id : '');
        
        $seedString = ($customer ? $customer->id : 'cust') . '_' . $fecha;
        $seed = crc32($seedString);
        srand(abs($seed));
        
        $numProds = (abs($seed) % 4) + 2; 
        $selectedKeys = array_rand($productos, $numProds);
        if (!is_array($selectedKeys)) {
            $selectedKeys = [$selectedKeys];
        }
        
        $items = [];
        foreach ($selectedKeys as $pk) {
            $prod = $productos[$pk];
            
            $qtyPreventa = (rand(2, 8) * 5) + rand(1, 4); 
            
            $deliverChance = rand(0, 10);
            if ($deliverChance <= 6) {
                $qtyEntrega = $qtyPreventa;
            } elseif ($deliverChance <= 8) {
                $qtyEntrega = rand(2, $qtyPreventa - 1);
            } else {
                $qtyEntrega = 0;
            }
            
            if ($pedido && !$factura) {
                $qtyEntrega = 0;
            } elseif (!$pedido && $factura) {
                $qtyPreventa = 0;
                $qtyEntrega = (rand(2, 6) * 5);
            }
            
            $items[] = [
                'producto_id' => $prod['id'],
                'producto_nombre' => $prod['nombre'],
                'linea' => $prod['linea'],
                'familia' => $prod['familia'],
                'precio' => $prod['precio'],
                'qty_preventa' => $qtyPreventa,
                'qty_entrega' => $qtyEntrega,
                'monto_preventa' => $qtyPreventa * $prod['precio'],
                'monto_entrega' => $qtyEntrega * $prod['precio'],
            ];
        }
        
        $comparativa[] = [
            'key' => $key,
            'customer_id' => $customer ? $customer->id : '',
            'customer_nombre' => $customer ? $customer->nombre : 'Cliente Desconocido',
            'fecha' => $fecha,
            'vendedor_id' => $vendedor,
            'zona_id' => $zona,
            'pedido_folio' => $pedido ? $pedido->folio : 'N/A',
            'factura_folio' => $factura ? $factura->folio : 'N/A',
            'items' => $items,
        ];
    }
    
    srand();
    
    return $comparativa;
};

$aplicarFiltros = function() {
    $comparativa = $this->obtenerComparativa();
    $filtrados = [];
    
    foreach ($comparativa as $grupo) {
        if (!empty($this->buscar_folio)) {
            $term = strtolower($this->buscar_folio);
            $match = str_contains(strtolower($grupo['pedido_folio']), $term) ||
                     str_contains(strtolower($grupo['factura_folio']), $term) ||
                     str_contains(strtolower($grupo['customer_nombre']), $term) ||
                     str_contains(strtolower($grupo['customer_id']), $term);
                     
            if (!$match) {
                $anyProdMatch = false;
                foreach ($grupo['items'] as $it) {
                    if (str_contains(strtolower($it['producto_id']), $term) ||
                        str_contains(strtolower($it['producto_nombre']), $term)) {
                        $anyProdMatch = true;
                        break;
                    }
                }
                if (!$anyProdMatch) {
                    continue;
                }
            }
        }
        
        $grupoItemsFiltrados = [];
        foreach ($grupo['items'] as $it) {
            if (!empty($this->filtro_lineas)) {
                if (!in_array($it['linea'], $this->filtro_lineas)) {
                    continue;
                }
            }
            if ($this->filtro_linea_familia !== 'todos' && !empty($this->filtro_linea_familia)) {
                if ($it['familia'] !== $this->filtro_linea_familia) {
                    continue;
                }
            }
            $grupoItemsFiltrados[] = $it;
        }
        
        if (empty($grupoItemsFiltrados)) {
            continue;
        }
        
        $grupo['items'] = $grupoItemsFiltrados;
        
        if ($this->tipo_reporte === 'Solo Preventa') {
            if ($grupo['pedido_folio'] === 'N/A') {
                continue;
            }
            $totalDelivered = collect($grupo['items'])->sum('qty_entrega');
            if ($totalDelivered > 0) {
                continue;
            }
        } elseif ($this->tipo_reporte === 'Solo Entrega') {
            if ($grupo['factura_folio'] === 'N/A' || $grupo['pedido_folio'] !== 'N/A') {
                continue;
            }
        }
        
        $filtrados[] = $grupo;
    }
    
    $datosTabla = [];
    $totalPreventaGlobal = 0.0;
    $totalEntregaGlobal = 0.0;
    
    if ($this->agrupar_por === 'Producto') {
        $prodGroups = [];
        foreach ($filtrados as $g) {
            foreach ($g['items'] as $it) {
                $pId = $it['producto_id'];
                if (!isset($prodGroups[$pId])) {
                    $prodGroups[$pId] = [
                        'producto_id' => $pId,
                        'descripcion' => $it['producto_nombre'],
                        'linea' => $it['linea'],
                        'familia' => $it['familia'],
                        'precio' => $it['precio'],
                        'qty_preventa' => 0,
                        'qty_entrega' => 0,
                        'monto_preventa' => 0.0,
                        'monto_entrega' => 0.0,
                    ];
                }
                $prodGroups[$pId]['qty_preventa'] += $it['qty_preventa'];
                $prodGroups[$pId]['qty_entrega'] += $it['qty_entrega'];
                $prodGroups[$pId]['monto_preventa'] += $it['monto_preventa'];
                $prodGroups[$pId]['monto_entrega'] += $it['monto_entrega'];
            }
        }
        
        foreach ($prodGroups as $pg) {
            $pg['diferencia_qty'] = $pg['qty_entrega'] - $pg['qty_preventa'];
            $pg['diferencia_monto'] = $pg['monto_entrega'] - $pg['monto_preventa'];
            
            $totalPreventaGlobal += $pg['monto_preventa'];
            $totalEntregaGlobal += $pg['monto_entrega'];
            
            $datosTabla[] = $pg;
        }
        
        usort($datosTabla, function($a, $b) {
            return strcmp($a['producto_id'], $b['producto_id']);
        });
        
    } elseif ($this->agrupar_por === 'Pedido') {
        foreach ($filtrados as $g) {
            $qtyPrev = collect($g['items'])->sum('qty_preventa');
            $qtyEnt = collect($g['items'])->sum('qty_entrega');
            $montoPrev = collect($g['items'])->sum('monto_preventa');
            $montoEnt = collect($g['items'])->sum('monto_entrega');
            
            $diffQty = $qtyEnt - $qtyPrev;
            $diffMonto = $montoEnt - $montoPrev;
            
            $estadoCarga = 'Completo';
            if ($qtyEnt === 0) {
                $estadoCarga = 'Rechazado';
            } elseif ($diffQty < 0) {
                $estadoCarga = 'Surtido Parcial';
            }
            
            $totalPreventaGlobal += $montoPrev;
            $totalEntregaGlobal += $montoEnt;
            
            $datosTabla[] = [
                'pedido_folio' => $g['pedido_folio'],
                'factura_folio' => $g['factura_folio'],
                'cliente' => $g['customer_nombre'],
                'fecha' => $g['fecha'],
                'vendedor_id' => $g['vendedor_id'],
                'qty_preventa' => $qtyPrev,
                'qty_entrega' => $qtyEnt,
                'diferencia_qty' => $diffQty,
                'monto_preventa' => $montoPrev,
                'monto_entrega' => $montoEnt,
                'diferencia_monto' => $diffMonto,
                'estado_carga' => $estadoCarga,
            ];
        }
        
        usort($datosTabla, function($a, $b) {
            return strcmp($a['pedido_folio'], $b['pedido_folio']);
        });
        
    } else { 
        foreach ($filtrados as $g) {
            foreach ($g['items'] as $it) {
                $qtyPrev = $it['qty_preventa'];
                $qtyEnt = $it['qty_entrega'];
                $montoPrev = $it['monto_preventa'];
                $montoEnt = $it['monto_entrega'];
                
                $totalPreventaGlobal += $montoPrev;
                $totalEntregaGlobal += $montoEnt;
                
                $datosTabla[] = [
                    'pedido_folio' => $g['pedido_folio'],
                    'factura_folio' => $g['factura_folio'],
                    'cliente' => $g['customer_nombre'],
                    'producto_id' => $it['producto_id'],
                    'descripcion' => $it['producto_nombre'],
                    'qty_preventa' => $qtyPrev,
                    'qty_entrega' => $qtyEnt,
                    'diferencia_qty' => $qtyEnt - $qtyPrev,
                    'monto_preventa' => $montoPrev,
                    'monto_entrega' => $montoEnt,
                    'diferencia_monto' => $montoEnt - $montoPrev,
                ];
            }
        }
        
        usort($datosTabla, function($a, $b) {
            $cmp = strcmp($a['pedido_folio'], $b['pedido_folio']);
            if ($cmp === 0) {
                return strcmp($a['producto_id'], $b['producto_id']);
            }
            return $cmp;
        });
    }
    
    $this->datosTabla = $datosTabla;
    $this->totalPreventa = $totalPreventaGlobal;
    $this->totalEntrega = $totalEntregaGlobal;
    $this->totalDiferencia = $totalEntregaGlobal - $totalPreventaGlobal;
};

$consultar = function() {
    if ($this->filtro_ruta === 'todos' || empty($this->filtro_ruta)) {
        return;
    }
    $this->consultado = true;
    $this->aplicarFiltros();
};

$exportarCSV = function() {
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=reporte_preventa_entrega_" . Carbon::now()->format('YmdHis') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($this->agrupar_por === 'Producto') {
            fputcsv($file, ['Cód. Producto', 'Descripción', 'Línea', 'Línea Familia', 'Cant. Preventa', 'Cant. Entrega', 'Dif. Cantidad', 'Monto Preventa', 'Monto Entrega', 'Dif. Monto']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [
                    $row['producto_id'], 
                    $row['descripcion'], 
                    $row['linea'], 
                    $row['familia'], 
                    $row['qty_preventa'], 
                    $row['qty_entrega'], 
                    $row['diferencia_qty'], 
                    $row['monto_preventa'], 
                    $row['monto_entrega'], 
                    $row['diferencia_monto']
                ]);
            }
        } elseif ($this->agrupar_por === 'Pedido') {
            fputcsv($file, ['Folio Pedido', 'Folio Entrega', 'Cliente', 'Fecha', 'Vendedor', 'Uds. Preventa', 'Uds. Entrega', 'Dif. Unidades', 'Importe Preventa', 'Importe Entrega', 'Dif. Importe', 'Estado Carga']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [
                    $row['pedido_folio'], 
                    $row['factura_folio'], 
                    $row['cliente'], 
                    $row['fecha'], 
                    $row['vendedor_id'], 
                    $row['qty_preventa'], 
                    $row['qty_entrega'], 
                    $row['diferencia_qty'], 
                    $row['monto_preventa'], 
                    $row['monto_entrega'], 
                    $row['diferencia_monto'], 
                    $row['estado_carga']
                ]);
            }
        } else {
            fputcsv($file, ['Folio Pedido', 'Folio Entrega', 'Cliente', 'Cód. Producto', 'Descripción', 'Cant. Preventa', 'Cant. Entrega', 'Diferencia', 'Monto Preventa', 'Monto Entrega', 'Dif. Monto']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [
                    $row['pedido_folio'], 
                    $row['factura_folio'], 
                    $row['cliente'], 
                    $row['producto_id'], 
                    $row['descripcion'], 
                    $row['qty_preventa'], 
                    $row['qty_entrega'], 
                    $row['diferencia_qty'], 
                    $row['monto_preventa'], 
                    $row['monto_entrega'], 
                    $row['diferencia_monto']
                ]);
            }
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-6 px-1 no-print">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] dark:text-orange-400 font-bold">Reporte Preventa Entrega</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-6">
                {{-- Title --}}
                <h2 class="text-lg font-bold text-[#1f2937] dark:text-white mb-5 select-none">Reporte de Preventa y Entrega</h2>

                {{-- Filters Grid - Row 1 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                    {{-- Zona --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas las Zonas</option>
                            @foreach($zonas as $z)
                                <option value="{{ $z['id'] }}">{{ $z['id'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ruta --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ruta</label>
                        <select wire:model.live="filtro_ruta" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            <option value="todos">Ruta</option>
                            @foreach($this->obtenerRutas() as $r)
                                <option value="{{ $r['id'] }}">{{ $r['id'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Fecha inicial</label>
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1">
                            <input type="date" wire:model.live="fecha_inicio" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm cursor-pointer" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Fecha final</label>
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1">
                            <input type="date" wire:model.live="fecha_fin" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm cursor-pointer" />
                        </div>
                    </div>

                    {{-- Consultar Button --}}
                    <div>
                        <button wire:click="consultar" 
                                @disabled($filtro_ruta === 'todos')
                                class="w-full px-6 py-2 rounded text-sm font-semibold transition duration-150 shadow-sm {{ $filtro_ruta === 'todos' ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-[#005fa3] hover:bg-[#004e86] text-white cursor-pointer' }}">
                            Consultar
                        </button>
                    </div>
                </div>

                {{-- Filters Grid - Row 2 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 items-end mt-5 pt-3 border-t border-gray-100 dark:border-gray-700/80">
                    {{-- Agrupar por --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Agrupar por</label>
                        <select wire:model.live="agrupar_por" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            <option value="Producto">Producto</option>
                            <option value="Pedido">Pedido</option>
                            <option value="Producto por Pedido">Producto por Pedido</option>
                        </select>
                    </div>

                    {{-- Linea --}}
                    <div class="flex flex-col w-full relative" x-data="{ open: false }">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1 select-none">Linea</label>
                        
                        <!-- trigger / chip container -->
                        <div @click="open = !open" 
                             class="min-h-[32px] border-b border-gray-300 dark:border-gray-600 pb-1 flex flex-wrap gap-1 items-center cursor-pointer pr-6 relative w-full">
                            
                            @if(empty($filtro_lineas))
                                <span class="text-sm text-gray-400">Linea</span>
                            @else
                                @foreach($filtro_lineas as $linea)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-250 rounded text-[10px] font-bold border border-gray-200 dark:border-gray-600">
                                        {{ $linea }}
                                        <button type="button" wire:click.stop="toggleLinea('{{ $linea }}')" class="text-gray-400 hover:text-rose-500 font-bold focus:outline-none text-xs">×</button>
                                    </span>
                                @endforeach
                            @endif
                            
                            <span class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </span>
                        </div>
                        
                        <!-- options dropdown -->
                        <div x-show="open" @click.outside="open = false" 
                             class="absolute z-50 left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg max-h-60 overflow-y-auto"
                             x-transition style="display: none;">
                            <div class="p-1.5 flex flex-col gap-0.5">
                                @foreach(['ALIMENTOS', 'DULCERIA', 'FARMACIA', 'HIG Y DESECHABLE', 'HIG PERSONAL', 'LIMPIEZA', 'VARIOS', 'MASCOTAS', 'BEBIDAS', 'CHICHARRINES', 'TIRAS GRANDES', 'TIRAS CHICAS'] as $opt)
                                    <label class="flex items-center gap-2.5 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-750/50 rounded cursor-pointer text-xs font-semibold text-gray-750 dark:text-gray-200 select-none">
                                        <input type="checkbox" wire:click="toggleLinea('{{ $opt }}')" @checked(in_array($opt, $filtro_lineas))
                                               class="rounded border-gray-300 dark:border-gray-600 text-[#005fa3] focus:ring-[#005fa3] w-3.5 h-3.5" />
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Linea Familia --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Linea Familia</label>
                        <select wire:model.live="filtro_linea_familia" 
                                class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            @if(empty($filtro_lineas))
                                <option value="todos">Lista vacía</option>
                            @else
                                <option value="todos">Linea Familia</option>
                                @foreach($this->obtenerFamilias() as $fam)
                                    <option value="{{ $fam }}">{{ $fam }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Buscar Folio --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Buscar Folio...</label>
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1">
                            <input type="text" wire:model.live="buscar_folio" placeholder="Buscar Folio..." class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Tipo --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Tipo</label>
                        <select wire:model.live="tipo_reporte" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            <option value="Preventa -> Entrega">Preventa -> Entrega</option>
                            <option value="Entrega -> Preventa">Entrega -> Preventa</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Results Area --}}
            @if($consultado)
                @if(!empty($datosTabla))
                    {{-- KPIs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
                        {{-- Total Preventa --}}
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Total Preventa</p>
                                <h3 class="text-2xl font-bold text-gray-850 dark:text-white mt-1">${{ number_format($totalPreventa, 2) }}</h3>
                            </div>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-500 dark:text-blue-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>

                        {{-- Total Entrega --}}
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Total Entrega</p>
                                <h3 class="text-2xl font-bold text-gray-850 dark:text-white mt-1">${{ number_format($totalEntrega, 2) }}</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg text-emerald-500 dark:text-emerald-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Total Diferencia --}}
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Diferencia</p>
                                <h3 class="text-2xl font-bold mt-1 {{ $totalDiferencia >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $totalDiferencia >= 0 ? '+' : '' }}${{ number_format($totalDiferencia, 2) }}
                                </h3>
                            </div>
                            <div class="p-3 rounded-lg {{ $totalDiferencia >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-500 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Data Table Card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-sm text-[#1f2937] dark:text-white">
                                Detalle de Comparación (Agrupado por {{ $agrupar_por }})
                            </h3>
                            <button wire:click="exportarCSV" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded border border-gray-300 dark:border-gray-600 transition shadow-sm cursor-pointer">
                                Exportar CSV
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-semibold select-none">
                                        @if($agrupar_por === 'Producto')
                                            <th class="p-3">#</th>
                                            <th class="p-3">Clave</th>
                                            <th class="p-3">Producto</th>
                                            <th class="p-3">Cod. de barra</th>
                                            @if($tipo_reporte === 'Entrega -> Preventa')
                                                <th class="p-3 text-right">Cantidad entregada</th>
                                                <th class="p-3 text-right">Precio entrega</th>
                                                <th class="p-3 text-right">Total Entrega</th>
                                                <th class="p-3 text-right">Cantidad solicitada</th>
                                                <th class="p-3 text-right">Precio Preventa</th>
                                                <th class="p-3 text-right">Total</th>
                                            @else
                                                <th class="p-3 text-right">Cantidad solicitada</th>
                                                <th class="p-3 text-right">Precio Preventa</th>
                                                <th class="p-3 text-right">Total</th>
                                                <th class="p-3 text-right">Cantidad entregada</th>
                                                <th class="p-3 text-right">Precio entrega</th>
                                                <th class="p-3 text-right">Total Entrega</th>
                                            @endif
                                            <th class="p-3 text-right">Cantidad rechazada</th>
                                            <th class="p-3">Comentario</th>
                                        @elseif($agrupar_por === 'Pedido')
                                            <th class="p-3">Folio Pedido</th>
                                            <th class="p-3">Folio Entrega</th>
                                            <th class="p-3">Cliente</th>
                                            <th class="p-3">Fecha</th>
                                            @if($tipo_reporte === 'Entrega -> Preventa')
                                                <th class="p-3 text-right">Uds. Entrega</th>
                                                <th class="p-3 text-right">Uds. Preventa</th>
                                            @else
                                                <th class="p-3 text-right">Uds. Preventa</th>
                                                <th class="p-3 text-right">Uds. Entrega</th>
                                            @endif
                                            <th class="p-3 text-right">Dif. Unidades</th>
                                            @if($tipo_reporte === 'Entrega -> Preventa')
                                                <th class="p-3 text-right">Monto Entrega</th>
                                                <th class="p-3 text-right">Monto Preventa</th>
                                            @else
                                                <th class="p-3 text-right">Monto Preventa</th>
                                                <th class="p-3 text-right">Monto Entrega</th>
                                            @endif
                                            <th class="p-3 text-right">Dif. Monto</th>
                                            <th class="p-3 text-center">Estado</th>
                                        @else
                                            <th class="p-3">Folio Pedido</th>
                                            <th class="p-3">Folio Entrega</th>
                                            <th class="p-3">Cliente</th>
                                            <th class="p-3">Cód. Producto</th>
                                            <th class="p-3">Descripción</th>
                                            @if($tipo_reporte === 'Entrega -> Preventa')
                                                <th class="p-3 text-right">Cant. Entrega</th>
                                                <th class="p-3 text-right">Cant. Preventa</th>
                                            @else
                                                <th class="p-3 text-right">Cant. Preventa</th>
                                                <th class="p-3 text-right">Cant. Entrega</th>
                                            @endif
                                            <th class="p-3 text-right">Diferencia</th>
                                            @if($tipo_reporte === 'Entrega -> Preventa')
                                                <th class="p-3 text-right">Monto Entrega</th>
                                                <th class="p-3 text-right">Monto Preventa</th>
                                            @else
                                                <th class="p-3 text-right">Monto Preventa</th>
                                                <th class="p-3 text-right">Monto Entrega</th>
                                            @endif
                                            <th class="p-3 text-right">Dif. Monto</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                                    @foreach($datosTabla as $index => $row)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition">
                                            @if($agrupar_por === 'Producto')
                                                <td class="p-3 text-gray-500 font-medium select-none">{{ $index + 1 }}</td>
                                                <td class="p-3 font-semibold text-gray-600 dark:text-gray-400">{{ $row['producto_id'] }}</td>
                                                <td class="p-3 font-medium">{{ $row['descripcion'] }}</td>
                                                <td class="p-3 font-mono text-gray-500">{{ '750' . str_pad(abs(crc32($row['producto_id'])) % 1000000000, 9, '0', STR_PAD_LEFT) }}</td>
                                                @if($tipo_reporte === 'Entrega -> Preventa')
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['precio'], 2) }}</td>
                                                    <td class="p-3 text-right font-semibold">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['precio'], 2) }}</td>
                                                    <td class="p-3 text-right font-semibold">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                @else
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['precio'], 2) }}</td>
                                                    <td class="p-3 text-right font-semibold">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['precio'], 2) }}</td>
                                                    <td class="p-3 text-right font-semibold">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                @endif
                                                <td class="p-3 text-right font-bold {{ $row['qty_preventa'] - $row['qty_entrega'] <= 0 ? 'text-gray-500' : 'text-rose-600 dark:text-rose-455' }}">
                                                    {{ number_format(max(0, $row['qty_preventa'] - $row['qty_entrega'])) }}
                                                </td>
                                                <td class="p-3">
                                                    @if($row['qty_preventa'] == 0)
                                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded text-[10px] font-bold">Venta directa</span>
                                                    @elseif($row['qty_entrega'] == 0)
                                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded text-[10px] font-bold">Rechazo total</span>
                                                    @elseif($row['qty_entrega'] < $row['qty_preventa'])
                                                        <span class="px-2 py-0.5 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded text-[10px] font-bold">Rechazo parcial</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded text-[10px] font-bold">Completo</span>
                                                    @endif
                                                </td>
                                            @elseif($agrupar_por === 'Pedido')
                                                <td class="p-3 font-semibold text-gray-600 dark:text-gray-400">{{ $row['pedido_folio'] }}</td>
                                                <td class="p-3 font-semibold text-gray-500 dark:text-gray-555">{{ $row['factura_folio'] }}</td>
                                                <td class="p-3 font-medium truncate max-w-[200px]" title="{{ $row['cliente'] }}">{{ $row['cliente'] }}</td>
                                                <td class="p-3 text-gray-500 dark:text-gray-400">{{ Carbon::parse($row['fecha'])->format('d/m/Y') }}</td>
                                                @if($tipo_reporte === 'Entrega -> Preventa')
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                @else
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                @endif
                                                <td class="p-3 text-right font-bold {{ $row['diferencia_qty'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-455' }}">
                                                    {{ $row['diferencia_qty'] >= 0 ? '+' : '' }}{{ number_format($row['diferencia_qty']) }}
                                                </td>
                                                @if($tipo_reporte === 'Entrega -> Preventa')
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                @else
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                @endif
                                                <td class="p-3 text-right font-bold {{ $row['diferencia_monto'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-455' }}">
                                                    {{ $row['diferencia_monto'] >= 0 ? '+' : '' }}${{ number_format($row['diferencia_monto'], 2) }}
                                                </td>
                                                <td class="p-3 text-center">
                                                    @if($row['estado_carga'] === 'Completo')
                                                        <span class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-bold">Completo</span>
                                                    @elseif($row['estado_carga'] === 'Surtido Parcial')
                                                        <span class="px-2 py-0.5 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-full text-[10px] font-bold">Surtido Parcial</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full text-[10px] font-bold">Rechazado</span>
                                                    @endif
                                                </td>
                                            @else
                                                <td class="p-3 font-semibold text-gray-650 dark:text-gray-400">{{ $row['pedido_folio'] }}</td>
                                                <td class="p-3 font-semibold text-gray-500 dark:text-gray-555">{{ $row['factura_folio'] }}</td>
                                                <td class="p-3 font-medium truncate max-w-[150px]" title="{{ $row['cliente'] }}">{{ $row['cliente'] }}</td>
                                                <td class="p-3 font-semibold text-gray-600 dark:text-gray-400">{{ $row['producto_id'] }}</td>
                                                <td class="p-3 font-medium">{{ $row['descripcion'] }}</td>
                                                @if($tipo_reporte === 'Entrega -> Preventa')
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                @else
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_preventa']) }}</td>
                                                    <td class="p-3 text-right font-medium">{{ number_format($row['qty_entrega']) }}</td>
                                                @endif
                                                <td class="p-3 text-right font-bold {{ $row['diferencia_qty'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-455' }}">
                                                    {{ $row['diferencia_qty'] >= 0 ? '+' : '' }}{{ number_format($row['diferencia_qty']) }}
                                                </td>
                                                @if($tipo_reporte === 'Entrega -> Preventa')
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                @else
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_preventa'], 2) }}</td>
                                                    <td class="p-3 text-right font-medium">${{ number_format($row['monto_entrega'], 2) }}</td>
                                                @endif
                                                <td class="p-3 text-right font-bold {{ $row['diferencia_monto'] >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-455' }}">
                                                    {{ $row['diferencia_monto'] >= 0 ? '+' : '' }}${{ number_format($row['diferencia_monto'], 2) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-10 text-center mb-8">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">
                            No se encontraron datos comparativos para la combinación de filtros seleccionada.
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-550 mt-1">
                            Intente ajustando el rango de fechas, seleccionando otra ruta o removiendo los términos de búsqueda.
                        </p>
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-12 text-center mb-8">
                    <svg class="w-12 h-12 text-blue-400/60 dark:text-blue-500/40 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 20M3 11.627a9.321 9.321 0 004.122.952 9.38 9.38 0 002.624-.372m0-3.072A9.001 9.001 0 1120.124 12A9.39 9.39 0 0018 11.622a9.39 9.39 0 00-6.276 2.308M7.5 7.5a3 3 0 100-6 3 3 0 000 6zm9 0a3 3 0 100-6 3 3 0 000 6zM18.896 14.714a6 6 0 11-7.792 0" />
                    </svg>
                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">
                        Consulta Preventa vs Entrega Real
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-md mx-auto">
                        Seleccione una Zona y una Ruta válida, defina el rango de fechas de interés y haga clic en <strong>Consultar</strong> para desplegar la información.
                    </p>
            @endif

        </div>
    </div>
</div>
