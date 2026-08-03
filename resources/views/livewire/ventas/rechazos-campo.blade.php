<?php

use function Livewire\Volt\{state, layout};
use Carbon\Carbon;

layout('layouts.app');

// Función para generar dinámicamente el detalle de los productos rechazados a partir de las facturas sembradas
function obtenerRechazosDesdeInvoices() {
    $invoices = \App\Models\Invoice::all();
    
    $rechazos = [];
    $motivos = [
        'Producto Dañado / Caducado',
        'No Solicitado por el Cliente',
        'Precio Incorrecto',
        'Falta de Presupuesto',
        'Fuera de Horario de Entrega'
    ];
    $productos = [
        ['id' => 'P001', 'nombre' => 'Coca-Cola 600ml', 'precio' => 18.50],
        ['id' => 'P002', 'nombre' => 'Sabritas Original 50g', 'precio' => 22.00],
        ['id' => 'P003', 'nombre' => 'Gansito Marinela 50g', 'precio' => 15.00],
        ['id' => 'P004', 'nombre' => 'Leche Entera 1L', 'precio' => 26.00],
        ['id' => 'P005', 'nombre' => 'Cerveza Corona 355ml', 'precio' => 20.00]
    ];
    
    foreach ($invoices as $inv) {
        if (str_contains(strtolower($inv->comentario), 'rechazado') || str_starts_with($inv->folio, 'PED-908')) {
            $seed = crc32($inv->folio);
            $numProds = (abs($seed) % 3) + 1;
            
            for ($k = 0; $k < $numProds; $k++) {
                $pIndex = (abs($seed) + $k) % 5;
                $prod = $productos[$pIndex];
                $qty = (abs($seed + $k * 7) % 24) + 1;
                $motivo = $motivos[(abs($seed + $k * 13)) % count($motivos)];
                
                $rechazos[] = [
                    'folio_pedido' => $inv->folio,
                    'zona' => $inv->zona_id,
                    'ruta' => $inv->vendedor_id,
                    'cliente' => $inv->customer_id,
                    'fecha' => $inv->fecha,
                    'producto_id' => $prod['id'],
                    'producto_nombre' => $prod['nombre'],
                    'cantidad_rechazada' => $qty,
                    'precio_unitario' => $prod['precio'],
                    'motivo' => $motivo
                ];
            }
        }
    }
    
    return $rechazos;
}

state([
    'registros' => fn() => obtenerRechazosDesdeInvoices(),
    'registrosFiltrados' => [],
    'filtro_zona' => '1Z - Zona 1',
    'filtro_ruta' => 'todos',
    'fecha_inicio' => '2026-08-03',
    'fecha_fin' => '2026-08-03',
    'agrupar_por' => 'Producto',
    'buscar_folio' => '',
    'consultado' => false,
    'kpis' => [
        'total_unidades' => 0,
        'total_importe' => 0,
        'motivo_principal' => 'N/A',
        'tasa_rechazo' => '0.0%'
    ],
    'datosTabla' => [],
    'zonas' => fn() => \App\Models\Zone::all()->toArray(),
]);

$updatedFiltroZona = function() {
    $this->filtro_ruta = 'todos';
};

$obtenerRutas = function() {
    if ($this->filtro_zona === 'todos') {
        return \App\Models\Seller::where('oculto', 'N')->get()->toArray();
    }
    
    $vendedorIds = \App\Models\Invoice::where('zona_id', $this->filtro_zona)
        ->pluck('vendedor_id')
        ->unique();
        
    return \App\Models\Seller::whereIn('id', $vendedorIds)
        ->where('oculto', 'N')
        ->get()
        ->toArray();
};

$aplicarFiltros = function() {
    $filtrados = collect($this->registros);
    
    // Filtrar por Zona
    if ($this->filtro_zona !== 'todos') {
        $filtrados = $filtrados->where('zona', $this->filtro_zona);
    }
    
    // Filtrar por Ruta
    if ($this->filtro_ruta !== 'todos') {
        $filtrados = $filtrados->where('ruta', $this->filtro_ruta);
    }
    
    // Filtrar por Fecha
    if (!empty($this->fecha_inicio)) {
        try {
            $inicio = Carbon::parse($this->fecha_inicio)->startOfDay();
            $filtrados = $filtrados->filter(fn($item) => Carbon::parse($item['fecha']) >= $inicio);
        } catch (\Exception $e) {}
    }
    
    if (!empty($this->fecha_fin)) {
        try {
            $fin = Carbon::parse($this->fecha_fin)->endOfDay();
            $filtrados = $filtrados->filter(fn($item) => Carbon::parse($item['fecha']) <= $fin);
        } catch (\Exception $e) {}
    }
    
    // Filtrar por Buscador de Folio (si no está vacío)
    if (!empty($this->buscar_folio)) {
        $term = strtolower($this->buscar_folio);
        $filtrados = $filtrados->filter(function($item) use ($term) {
            return str_contains(strtolower($item['folio_pedido']), $term) ||
                   str_contains(strtolower($item['cliente']), $term) ||
                   str_contains(strtolower($item['producto_nombre']), $term) ||
                   str_contains(strtolower($item['producto_id']), $term);
        });
    }

    $registrosFiltradosCollection = $filtrados->values();
    $this->registrosFiltrados = $registrosFiltradosCollection->toArray();

    // Calcular KPIs
    $totalUnidades = $registrosFiltradosCollection->sum('cantidad_rechazada');
    $totalImporte = $registrosFiltradosCollection->sum(fn($r) => $r['cantidad_rechazada'] * $r['precio_unitario']);
    
    $motivos = $registrosFiltradosCollection->groupBy('motivo')->map->count();
    $motivoPrincipal = $motivos->count() > 0 ? $motivos->sortDesc()->keys()->first() : 'N/A';
    
    // Tasa de rechazo (simulada basada en el total, ej. total de unidades vendidas es 1500)
    $tasaRechazo = $totalUnidades > 0 ? number_format(($totalUnidades / 1500) * 100, 1) . '%' : '0.0%';

    $this->kpis = [
        'total_unidades' => $totalUnidades,
        'total_importe' => $totalImporte,
        'motivo_principal' => $motivoPrincipal,
        'tasa_rechazo' => $tasaRechazo
    ];

    // Formatear datos de tabla según agrupación
    if ($this->agrupar_por === 'Producto') {
        $this->datosTabla = $registrosFiltradosCollection->groupBy('producto_id')->map(function($items, $prodId) use ($totalImporte) {
            $first = $items->first();
            $cantidad = $items->sum('cantidad_rechazada');
            $importe = $items->sum(fn($r) => $r['cantidad_rechazada'] * $r['precio_unitario']);
            return [
                'producto_id' => $prodId,
                'descripcion' => $first['producto_nombre'],
                'cantidad' => $cantidad,
                'precio_promedio' => $first['precio_unitario'],
                'importe' => $importe,
                'porcentaje' => $totalImporte > 0 ? number_format(($importe / $totalImporte) * 100, 1) . '%' : '0.0%'
            ];
        })->values()->toArray();
    } elseif ($this->agrupar_por === 'Pedido') {
        $this->datosTabla = $registrosFiltradosCollection->groupBy('folio_pedido')->map(function($items, $folio) {
            $first = $items->first();
            $lineas = $items->count();
            $cantidad = $items->sum('cantidad_rechazada');
            $importe = $items->sum(fn($r) => $r['cantidad_rechazada'] * $r['precio_unitario']);
            
            $motivos = $items->groupBy('motivo')->map->count();
            $motivoDominante = $motivos->count() > 0 ? $motivos->sortDesc()->keys()->first() : 'N/A';

            return [
                'folio_pedido' => $folio,
                'ruta' => $first['ruta'],
                'cliente' => $first['cliente'],
                'fecha' => $first['fecha'],
                'lineas' => $lineas,
                'cantidad' => $cantidad,
                'importe' => $importe,
                'motivo_principal' => $motivoDominante
            ];
        })->values()->toArray();
    } else { // Producto por Pedido
        $this->datosTabla = $registrosFiltradosCollection->map(function($item) {
            return [
                'folio_pedido' => $item['folio_pedido'],
                'cliente' => $item['cliente'],
                'ruta' => $item['ruta'],
                'producto_id' => $item['producto_id'],
                'descripcion' => $item['producto_nombre'],
                'cantidad' => $item['cantidad_rechazada'],
                'precio_unitario' => $item['precio_unitario'],
                'importe' => $item['cantidad_rechazada'] * $item['precio_unitario'],
                'motivo' => $item['motivo'],
                'fecha' => $item['fecha']
            ];
        })->values()->toArray();
    }
};

$consultar = function() {
    $this->consultado = true;
    $this->aplicarFiltros();
};

$exportarCSV = function() {
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=reporte_rechazos_" . Carbon::now()->format('YmdHis') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() {
        $file = fopen('php://output', 'w');
        // UTF-8 BOM para soporte correcto en Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($this->agrupar_por === 'Producto') {
            fputcsv($file, ['Cód. Producto', 'Descripción', 'Cant. Rechazada', 'Precio Promedio', 'Importe Total', '% Distribución']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [$row['producto_id'], $row['descripcion'], $row['cantidad'], $row['precio_promedio'], $row['importe'], $row['porcentaje']]);
            }
        } elseif ($this->agrupar_por === 'Pedido') {
            fputcsv($file, ['Folio Pedido', 'Ruta / Vendedor', 'Cliente', 'Fecha', 'Líneas Rechazadas', 'Total Unidades', 'Importe Rechazado', 'Motivo Principal']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [$row['folio_pedido'], $row['ruta'], $row['cliente'], $row['fecha'], $row['lineas'], $row['cantidad'], $row['importe'], $row['motivo_principal']]);
            }
        } else {
            fputcsv($file, ['Folio Pedido', 'Cliente', 'Ruta', 'Cód. Producto', 'Descripción', 'Cantidad', 'Precio Unit.', 'Importe', 'Motivo de Rechazo', 'Fecha']);
            foreach ($this->datosTabla as $row) {
                fputcsv($file, [$row['folio_pedido'], $row['cliente'], $row['ruta'], $row['producto_id'], $row['descripcion'], $row['cantidad'], $row['precio_unitario'], $row['importe'], $row['motivo'], $row['fecha']]);
            }
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
};
?>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endpush
@endonce

<div>
    <div class="py-4" x-data="{
        chartMotivo: null,
        chartRuta: null,
        initCharts() {
            if (window.rechazosReportsInit) {
                window.rechazosReportsInit(this);
            } else {
                document.addEventListener('charts-script-loaded', () => {
                    window.rechazosReportsInit(this);
                });
            }
        }
    }" x-init="initCharts()">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-6 px-1 no-print">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] dark:text-orange-400 font-bold">Reporte de Productos Rechazados</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-6">
                {{-- Title --}}
                <h2 class="text-lg font-bold text-[#1f2937] dark:text-white mb-5 select-none">Reporte de Productos Rechazados</h2>

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
                            <input type="date" wire:model.live="fecha_inicio" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Fecha final</label>
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1">
                            <input type="date" wire:model.live="fecha_fin" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Consultar Button --}}
                    <div>
                        <button wire:click="consultar" class="w-full px-6 py-2 bg-[#005fa3] hover:bg-[#004e86] text-white rounded text-sm font-semibold transition duration-150 cursor-pointer shadow-sm">
                            Consultar
                        </button>
                    </div>
                </div>

                {{-- Filters Grid - Row 2 --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-end mt-5 pt-3 border-t border-gray-100 dark:border-gray-700/80">
                    {{-- Agrupar por --}}
                    <div class="flex flex-col w-full md:col-span-2">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Agrupar por</label>
                        <select wire:model.live="agrupar_por" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            <option value="Producto">Producto</option>
                            <option value="Pedido">Pedido</option>
                            <option value="Producto por Pedido">Producto por Pedido</option>
                        </select>
                    </div>

                    {{-- Buscar Folio --}}
                    <div class="flex flex-col w-full md:col-span-3">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Buscar Folio / Cliente / Producto...</label>
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mr-2 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" wire:model.live.debounce.300ms="buscar_folio" placeholder="Buscar Folio, Cliente o Producto..." class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 dark:text-gray-200 font-semibold text-sm" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Results Area --}}
            @if($consultado && !empty($registrosFiltrados))
                {{-- KPIs --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                    {{-- Total Unidades --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Unidades Rechazadas</p>
                            <h3 class="text-2xl font-bold text-gray-850 dark:text-white mt-1">{{ number_format($kpis['total_unidades']) }}</h3>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-500 dark:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>

                    {{-- Total Importe --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Importe Rechazado</p>
                            <h3 class="text-2xl font-bold text-gray-850 dark:text-white mt-1">${{ number_format($kpis['total_importe'], 2) }}</h3>
                        </div>
                        <div class="p-3 bg-orange-50 dark:bg-orange-900/30 rounded-lg text-orange-500 dark:text-orange-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Motivo Principal --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Motivo Principal</p>
                            <h3 class="text-sm font-bold text-gray-850 dark:text-white mt-1 truncate max-w-[185px]" title="{{ $kpis['motivo_principal'] }}">
                                {{ $kpis['motivo_principal'] }}
                            </h3>
                        </div>
                        <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-lg text-red-500 dark:text-red-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Tasa Rechazo --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-550 uppercase tracking-wider select-none">Tasa de Rechazo</p>
                            <h3 class="text-2xl font-bold text-gray-850 dark:text-white mt-1">{{ $kpis['tasa_rechazo'] }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg text-emerald-500 dark:text-emerald-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Charts container --}}
                <div id="chart-data-rechazos" data-chart-raw="{{ json_encode($registrosFiltrados) }}" class="hidden"></div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 no-print">
                    {{-- Chart 1 --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80">
                        <h3 class="font-bold text-sm text-gray-700 dark:text-gray-200 mb-4 select-none">Importe Rechazado por Motivo</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="chartRechazosMotivo" wire:ignore></canvas>
                        </div>
                    </div>

                    {{-- Chart 2 --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80">
                        <h3 class="font-bold text-sm text-gray-700 dark:text-gray-200 mb-4 select-none">Importe Rechazado por Ruta</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="chartRechazosRuta" wire:ignore></canvas>
                        </div>
                    </div>
                </div>

                {{-- Data Table Card --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-sm text-[#1f2937] dark:text-white">
                            Detalle de Rechazos (Agrupado por {{ $agrupar_por }})
                        </h3>
                        <button wire:click="exportarCSV" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded border border-gray-300 dark:border-gray-600 transition shadow-sm cursor-pointer">
                            Exportar CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-705 border-b border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-semibold select-none">
                                    @if($agrupar_por === 'Producto')
                                        <th class="p-3">Cód. Producto</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3 text-right">Cant. Rechazada</th>
                                        <th class="p-3 text-right">Precio Promedio</th>
                                        <th class="p-3 text-right">Importe Total</th>
                                        <th class="p-3 text-right">% Distribución</th>
                                    @elseif($agrupar_por === 'Pedido')
                                        <th class="p-3">Folio Pedido</th>
                                        <th class="p-3">Ruta / Vendedor</th>
                                        <th class="p-3">Cliente</th>
                                        <th class="p-3">Fecha</th>
                                        <th class="p-3 text-right">Líneas Rechazadas</th>
                                        <th class="p-3 text-right">Total Unidades</th>
                                        <th class="p-3 text-right">Importe Rechazado</th>
                                        <th class="p-3">Motivo Principal</th>
                                    @else
                                        <th class="p-3">Folio Pedido</th>
                                        <th class="p-3">Cliente</th>
                                        <th class="p-3">Ruta</th>
                                        <th class="p-3">Cód. Producto</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3 text-right">Cantidad</th>
                                        <th class="p-3 text-right">Precio Unit.</th>
                                        <th class="p-3 text-right">Importe</th>
                                        <th class="p-3">Motivo de Rechazo</th>
                                        <th class="p-3">Fecha</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700/80">
                                @forelse($datosTabla as $fila)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 text-gray-700 dark:text-gray-300">
                                        @if($agrupar_por === 'Producto')
                                            <td class="p-3 font-medium text-gray-905 dark:text-white">{{ $fila['producto_id'] }}</td>
                                            <td class="p-3">{{ $fila['descripcion'] }}</td>
                                            <td class="p-3 text-right">{{ number_format($fila['cantidad']) }}</td>
                                            <td class="p-3 text-right">${{ number_format($fila['precio_promedio'], 2) }}</td>
                                            <td class="p-3 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($fila['importe'], 2) }}</td>
                                            <td class="p-3 text-right text-gray-500 dark:text-gray-400">{{ $fila['porcentaje'] }}</td>
                                        @elseif($agrupar_por === 'Pedido')
                                            <td class="p-3 font-medium text-gray-905 dark:text-white">{{ $fila['folio_pedido'] }}</td>
                                            <td class="p-3">{{ $fila['ruta'] }}</td>
                                            <td class="p-3">{{ $fila['cliente'] }}</td>
                                            <td class="p-3">{{ $fila['fecha'] }}</td>
                                            <td class="p-3 text-right">{{ $fila['lineas'] }}</td>
                                            <td class="p-3 text-right">{{ number_format($fila['cantidad']) }}</td>
                                            <td class="p-3 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($fila['importe'], 2) }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 bg-red-50 dark:bg-red-950/30 text-red-650 dark:text-red-400 rounded text-[10px] font-medium border border-red-100 dark:border-red-900/30">
                                                    {{ $fila['motivo_principal'] }}
                                                </span>
                                            </td>
                                        @else
                                            <td class="p-3 font-medium text-gray-905 dark:text-white">{{ $fila['folio_pedido'] }}</td>
                                            <td class="p-3">{{ $fila['cliente'] }}</td>
                                            <td class="p-3">{{ $fila['ruta'] }}</td>
                                            <td class="p-3">{{ $fila['producto_id'] }}</td>
                                            <td class="p-3">{{ $fila['descripcion'] }}</td>
                                            <td class="p-3 text-right">{{ number_format($fila['cantidad']) }}</td>
                                            <td class="p-3 text-right">${{ number_format($fila['precio_unitario'], 2) }}</td>
                                            <td class="p-3 text-right font-semibold text-gray-950 dark:text-white">${{ number_format($fila['importe'], 2) }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-0.5 bg-red-50 dark:bg-red-950/30 text-red-650 dark:text-red-400 rounded text-[10px] font-medium border border-red-100 dark:border-red-900/30">
                                                    {{ $fila['motivo'] }}
                                                </span>
                                            </td>
                                            <td class="p-3">{{ $fila['fecha'] }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="p-8 text-center text-gray-400 dark:text-gray-500">
                                            No se encontraron registros de rechazos con los filtros seleccionados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                {{-- Empty State (Matches screenshot 1 style) --}}
                <div class="h-64 flex flex-col items-center justify-center text-gray-450 dark:text-gray-500 select-none">
                    <svg class="w-12 h-12 mb-3 text-gray-350 dark:text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm font-medium">Seleccione los filtros y presione Consultar para visualizar el reporte analítico.</p>
                </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
window.rechazosReportsInit = function(comp) {
    comp.getChartData = function() {
        const el = document.getElementById('chart-data-rechazos');
        if (!el) return { motives: { labels: [], values: [] }, routes: { labels: [], values: [] } };
        let rows = [];
        try {
            rows = JSON.parse(el.getAttribute('data-chart-raw') || '[]');
        } catch (e) {
            console.error('Error parsing chart data:', e);
        }
        if (!Array.isArray(rows)) rows = [];

        // Motives Grouping
        const motivesMap = {};
        rows.forEach(r => {
            const val = parseFloat(r.cantidad_rechazada || r.cantidad || 0) * parseFloat(r.precio_unitario || r.precio_promedio || 0);
            const mot = r.motivo || r.motivo_principal || 'Desconocido';
            motivesMap[mot] = (motivesMap[mot] || 0) + val;
        });

        // Routes Grouping
        const routesMap = {};
        rows.forEach(r => {
            const val = parseFloat(r.cantidad_rechazada || r.cantidad || 0) * parseFloat(r.precio_unitario || r.precio_promedio || 0);
            const rt = r.ruta || 'Desconocido';
            routesMap[rt] = (routesMap[rt] || 0) + val;
        });

        return {
            motives: {
                labels: Object.keys(motivesMap),
                values: Object.values(motivesMap)
            },
            routes: {
                labels: Object.keys(routesMap),
                values: Object.values(routesMap)
            }
        };
    };

    comp.buildCharts = function(data) {
        const motivoCtx = document.getElementById('chartRechazosMotivo');
        const rutaCtx = document.getElementById('chartRechazosRuta');

        if (comp.chartMotivo) {
            try { comp.chartMotivo.destroy(); } catch(e) {}
            comp.chartMotivo = null;
        }
        if (comp.chartRuta) {
            try { comp.chartRuta.destroy(); } catch(e) {}
            comp.chartRuta = null;
        }

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js is not loaded yet.');
            return;
        }

        // Gráfico de Motivos (Doughnut)
        if (motivoCtx && data && data.motives && data.motives.values.length > 0) {
            try {
                comp.chartMotivo = new Chart(motivoCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.motives.labels,
                        datasets: [{
                            data: data.motives.values,
                            backgroundColor: [
                                '#3b82f6', // blue
                                '#ef4444', // red
                                '#f59e0b', // amber
                                '#10b981', // emerald
                                '#8b5cf6'  // violet
                            ],
                            borderWidth: 1.5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 10 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' Monto: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (err) {
                console.error('Error rendering chartMotivo:', err);
            }
        }

        // Gráfico de Ruta (Bar)
        if (rutaCtx && data && data.routes && data.routes.values.length > 0) {
            try {
                const ctx = rutaCtx.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 250);
                gradient.addColorStop(0, 'rgba(59, 130, 246, 0.9)');
                gradient.addColorStop(1, 'rgba(37, 99, 235, 0.2)');

                comp.chartRuta = new Chart(rutaCtx, {
                    type: 'bar',
                    data: {
                        labels: data.routes.labels,
                        datasets: [{
                            data: data.routes.values,
                            backgroundColor: gradient,
                            borderColor: '#2563eb',
                            borderWidth: 1.5,
                            borderRadius: 4,
                            barThickness: 28
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' Monto: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { font: { size: 10 } }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) { return '$' + value; },
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            } catch (err) {
                console.error('Error rendering chartRuta:', err);
            }
        }
    };

    comp.updateCharts = function(data) {
        if (typeof Chart === 'undefined') return;
        if (comp.chartMotivo && data && data.motives) {
            try {
                comp.chartMotivo.data.labels = data.motives.labels;
                comp.chartMotivo.data.datasets[0].data = data.motives.values;
                comp.chartMotivo.update();
            } catch(e) {}
        }
        if (comp.chartRuta && data && data.routes) {
            try {
                comp.chartRuta.data.labels = data.routes.labels;
                comp.chartRuta.data.datasets[0].data = data.routes.values;
                comp.chartRuta.update();
            } catch(e) {}
        }
    };

    comp.$nextTick(() => {
        comp.buildCharts(comp.getChartData());
    });

    const registerHook = () => {
        try {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    comp.$nextTick(() => {
                        const data = comp.getChartData();
                        const canvasMotivo = document.getElementById('chartRechazosMotivo');
                        const canvasRuta = document.getElementById('chartRechazosRuta');

                        if (comp.chartMotivo && comp.chartMotivo.canvas !== canvasMotivo) {
                            try { comp.chartMotivo.destroy(); } catch(e) {}
                            comp.chartMotivo = null;
                        }
                        if (comp.chartRuta && comp.chartRuta.canvas !== canvasRuta) {
                            try { comp.chartRuta.destroy(); } catch(e) {}
                            comp.chartRuta = null;
                        }

                        if (!comp.chartMotivo || !comp.chartRuta) {
                            comp.buildCharts(data);
                        } else {
                            comp.updateCharts(data);
                        }
                    });
                });
            });
        } catch (e) {
            console.error('Error registering Livewire commit hook:', e);
        }
    };

    if (window.Livewire) {
        registerHook();
    } else {
        document.addEventListener('livewire:init', registerHook);
    }
};
document.dispatchEvent(new CustomEvent('charts-script-loaded'));
</script>
@endpush
