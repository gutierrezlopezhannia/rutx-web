<?php

use function Livewire\Volt\{state, layout, mount};
use Carbon\Carbon;

layout('layouts.app');

state([
    'registrosFiltrados' => [],
    'consultado' => false,            // Controla si se ha hecho clic en Consultar

    // Filtros
    'filtro_zona' => 'todos',
    'vendedores_seleccionados' => [], // Array de vendedores seleccionados
    'fecha_inicio' => '2026-07-20',   // Ajustado a las capturas
    'fecha_fin' => '2026-07-24',      // Ajustado a las capturas

    // Paginación y filas por página
    'filas_por_pagina' => 1000,       // Por defecto 1000 de las capturas

    // Columnas visibles
    'visibleColumns' => [
        'rank' => true,
        'cliente' => true,
        'ruta' => true,
        'total' => true,
        'contado' => true,
        'credito' => true,
        'ventas' => true,
        'venta_mes' => true,
    ],

    // Búsqueda en los resultados (mantenida en backend para compatibilidad con tests)
    'search' => '',
]);

$aplicarFiltros = function () {
    $invoicesQuery = \App\Models\Invoice::query();

    // Filtro por Zona
    if ($this->filtro_zona !== 'todos') {
        $invoicesQuery->where('zona_id', $this->filtro_zona);
    }
    
    // Filtro por Vendedores seleccionados (Multi-select)
    if (!empty($this->vendedores_seleccionados)) {
        $invoicesQuery->whereIn('vendedor_id', $this->vendedores_seleccionados);
    }
    
    // Filtro por Fechas
    if (!empty($this->fecha_inicio)) {
        $invoicesQuery->where('fecha', '>=', $this->fecha_inicio);
    }
    if (!empty($this->fecha_fin)) {
        $invoicesQuery->where('fecha', '<=', $this->fecha_fin);
    }

    // Agrupar por cliente y ruta
    $groupedData = $invoicesQuery->selectRaw('customer_id, vendedor_id as ruta, SUM(total) as total_sales, SUM(abono) as total_contado, SUM(saldo) as total_credito, COUNT(id) as total_orders')
        ->groupBy('customer_id', 'vendedor_id')
        ->get();

    // Calcular la duración en meses para "Venta por mes"
    $start = Carbon::parse($this->fecha_inicio);
    $end = Carbon::parse($this->fecha_fin);
    $months = $start->diffInMonths($end);

    // Suma de ventas para porcentaje si es necesario (o solo ranking)
    $grandTotalSales = (double) $groupedData->sum('total_sales');
    
    // Ordenar de mayor a menor total
    $groupedData = $groupedData->sortByDesc('total_sales');

    // Cargar todos los clientes asociados por lote
    $customerIds = $groupedData->pluck('customer_id')->filter()->unique()->toArray();
    $customers = \App\Models\Customer::whereIn('id', $customerIds)->get()->keyBy('id');

    // Mapear registros
    $allRecords = $groupedData->values()->map(function ($row, $index) use ($grandTotalSales, $customers, $months) {
        $customer = $customers->get($row->customer_id);
        $totalSales = (double) $row->total_sales;
        $totalContado = (double) $row->total_contado;
        $totalCredito = (double) $row->total_credito;
        $totalOrders = (int) $row->total_orders;
        
        $ventaMes = $months > 0 ? $totalSales / $months : 0.0;

        // Limpiar identificadores y nombres para visualización
        $clienteCodigo = $customer->clave ?? '';
        $clienteNombre = $customer->nombre ?? 'Desconocido';
        $vendedorNombre = $row->ruta ?? 'Desconocido';

        // Intentar separar el ID de la ruta en la visualización
        if (str_contains($vendedorNombre, ' - ')) {
            $parts = explode(' - ', $vendedorNombre);
            $vendedorNombre = trim($parts[0]) . ' ' . trim($parts[1]);
        }

        return [
            'rank' => $index + 1,
            'cliente_id' => $row->customer_id,
            'cliente_codigo' => $clienteCodigo,
            'cliente_nombre' => $clienteNombre,
            'ruta' => $vendedorNombre,
            'total_sales' => $totalSales,
            'total_contado' => $totalContado,
            'total_credito' => $totalCredito,
            'total_orders' => $totalOrders,
            'venta_mes' => $ventaMes,
        ];
    });

    // Aplicar término de búsqueda
    if (!empty($this->search)) {
        $searchLower = mb_strtolower($this->search);
        $allRecords = $allRecords->filter(function ($item) use ($searchLower) {
            return mb_strpos(mb_strtolower($item['cliente_nombre']), $searchLower) !== false ||
                   mb_strpos(mb_strtolower($item['cliente_codigo']), $searchLower) !== false;
        });
    }

    $this->registrosFiltrados = $allRecords->values()->toArray();
};

// Consultar explícitamente (Botón Consultar de la captura)
$consultar = function () {
    $this->consultado = true;
    $this->aplicarFiltros();
};

mount(function () {
    // Solo inicializa las fechas, NO aplica filtros (para mantener el estado inicial limpio)
    $this->fecha_inicio = '2026-07-20';
    $this->fecha_fin = '2026-07-24';
});

$updatedSearch = function () {
    $this->aplicarFiltros();
};

$descargarCSV = function () {
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=clientes_con_mayor_venta_" . date('Ymd_His') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Posición', 'Cliente', 'Ruta', 'Total ($)', 'Total de Contado ($)', 'Total de Crédito ($)', 'Ventas (#)', 'Venta por Mes ($)'];

    $callback = function() use($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($this->registrosFiltrados as $row) {
            fputcsv($file, [
                $row['rank'],
                $row['cliente_codigo'] . ' ' . $row['cliente_nombre'],
                $row['ruta'],
                number_format($row['total_sales'], 2, '.', ''),
                number_format($row['total_contado'], 2, '.', ''),
                number_format($row['total_credito'], 2, '.', ''),
                $row['total_orders'],
                number_format($row['venta_mes'], 2, '.', '')
            ]);
        }
        fclose($file);
    };

    session()->flash('mensaje_exito', 'El archivo CSV de Clientes con Mayor Venta se ha exportado correctamente.');
    return response()->stream($callback, 200, $headers);
};

?>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endpush
@endonce

<style>
/* Estilos para impresión de PDF limpios de leaks de diseño */
@media print {
    body > div:not(#print-area) {
        display: none !important;
    }
    #print-area {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        background: white !important;
        color: black !important;
        padding: 20px !important;
        margin: 0 !important;
    }
    #print-area .no-print {
        display: none !important;
    }
    table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    th, td {
        border: 1px solid #cbd5e1 !important;
        padding: 8px !important;
        font-size: 11px !important;
    }
}
</style>

<div>
    <div class="py-4" x-data="topClientsReports()" x-init="initCharts()">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1 no-print">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500 font-medium">Clientes con Mayor Venta</span>
            </div>

            {{-- Alertas --}}
            @if (session()->has('mensaje_exito'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg flex items-center justify-between shadow-sm no-print">
                    <span>{{ session('mensaje_exito') }}</span>
                    <button class="text-green-500 hover:text-green-700 font-bold focus:outline-none" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            {{-- Contenedor de Filtros (Card principal de la captura) --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-6 mb-6 no-print">
                
                <h2 class="text-base font-bold text-gray-800 mb-6">Clientes con mayor Venta</h2>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 w-full">
                    {{-- Filtro Zona --}}
                    <div class="w-44 flex flex-col shrink-0">
                        <label class="text-xs text-gray-400 font-semibold mb-1">Zona</label>
                        <select wire:model="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#3b82f6] focus:ring-0 bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas las Zonas</option>
                            @foreach(\App\Models\Zone::pluck('id')->sort() as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro Vendedor (Multi-select dropdown con checkboxes) --}}
                    <div x-data="{ open: false, selected: @entangle('vendedores_seleccionados') }" class="relative flex-1 min-w-[200px] flex flex-col">
                        <label class="text-xs text-gray-400 font-semibold mb-1">Vendedor</label>
                        <div @click="open = !open" @click.away="open = false" class="flex items-center justify-between border-0 border-b border-gray-300 py-1 cursor-pointer">
                            <span class="text-sm text-gray-700 font-semibold truncate select-none">
                                <template x-if="selected.length === 0">
                                    <span class="text-gray-400 font-medium">Vendedor</span>
                                </template>
                                <template x-if="selected.length === 1">
                                    <span x-text="selected[0]"></span>
                                </template>
                                <template x-if="selected.length > 1">
                                    <span x-text="selected[0] + ', +' + (selected.length - 1)"></span>
                                </template>
                            </span>
                            <div class="flex items-center gap-1.5">
                                <template x-if="selected.length > 0">
                                    <button type="button" @click.stop="selected = []" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </template>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Panel desplegable --}}
                        <div x-show="open" style="display:none;" class="absolute left-0 mt-14 w-full bg-white border border-gray-200 shadow-xl rounded-lg z-50 p-2 max-h-60 overflow-y-auto">
                            @foreach(\App\Models\Seller::pluck('id')->sort() as $sellerId)
                                <label class="flex items-center space-x-3 px-2 py-1.5 hover:bg-gray-50 cursor-pointer rounded transition">
                                    <input type="checkbox" value="{{ $sellerId }}" x-model="selected"
                                        class="text-[#3b82f6] rounded border-gray-300 focus:ring-[#3b82f6] w-4 h-4" />
                                    <span class="text-sm text-gray-700 font-semibold">{{ $sellerId }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="w-36 flex flex-col shrink-0">
                        <label class="text-xs text-gray-400 font-semibold mb-1">Fecha inicial</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model="fecha_inicio" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm w-full cursor-pointer" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="w-36 flex flex-col shrink-0">
                        <label class="text-xs text-gray-400 font-semibold mb-1">Fecha final</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model="fecha_fin" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm w-full cursor-pointer" />
                        </div>
                    </div>

                    {{-- Botón Consultar --}}
                    <div class="w-28 flex flex-col shrink-0">
                        <button wire:click="consultar" class="w-full py-1.5 bg-[#3b82f6] hover:bg-[#2563eb] text-white rounded text-sm font-semibold transition duration-150 shadow-sm cursor-pointer text-center">
                            Consultar
                        </button>
                    </div>
                </div>

            </div>

            @if($consultado)
                {{-- Área del Reporte que se Imprime --}}
                <div id="print-area">
                    
                    {{-- Encabezado solo para Impresión --}}
                    <div class="hidden print:block mb-6">
                        <h1 class="text-lg font-bold text-gray-800">Clientes con mayor Venta</h1>
                        <p class="text-[10px] text-gray-400 font-semibold mt-1">Período: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
                    </div>

                    {{-- Card de la Tabla --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 mb-6">
                        
                        {{-- Iconos de Acción a la Derecha (dentro del card, arriba de la tabla) --}}
                        <div class="flex justify-end items-center space-x-2 text-gray-400 mb-4 no-print">
                            
                            {{-- Editar Columnas --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false"
                                    class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition duration-150 cursor-pointer" title="Editar Columnas">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </button>
                                <div x-show="open" style="display:none;"
                                    class="absolute right-0 mt-2 w-52 bg-white border border-gray-200 shadow-xl rounded-lg z-50 p-2">
                                    <div class="text-[11px] font-bold text-gray-400 mb-2 px-2 uppercase tracking-wider">Editar Columnas</div>
                                    @foreach([
                                        'rank' => 'No.',
                                        'cliente' => 'Cliente',
                                        'ruta' => 'Ruta',
                                        'total' => 'Total',
                                        'contado' => 'Total de contado',
                                        'credito' => 'Total de crédito',
                                        'ventas' => 'Ventas',
                                        'venta_mes' => 'Venta por mes',
                                    ] as $key => $label)
                                        <label class="flex items-center space-x-3 px-2 py-1.5 hover:bg-gray-50 cursor-pointer rounded transition">
                                            <input type="checkbox" wire:model.live="visibleColumns.{{ $key }}"
                                                class="text-[#3b82f6] rounded border-gray-300 focus:ring-[#3b82f6] w-4 h-4" />
                                            <span class="text-xs text-gray-700 font-semibold">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Exportar Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false"
                                    class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition duration-150 cursor-pointer" title="Exportar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </button>
                                <div x-show="open" style="display:none;"
                                    class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 shadow-xl rounded-lg z-50 py-1 text-xs text-start">
                                    <button wire:click="descargarCSV" class="w-full text-start px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition cursor-pointer font-medium">
                                        Exportar a CSV
                                    </button>
                                    <button @click="window.print()" class="w-full text-start px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition cursor-pointer font-medium">
                                        Exportar a PDF
                                    </button>
                                </div>
                            </div>

                            {{-- Actualizar --}}
                            <button wire:click="aplicarFiltros" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition duration-150 cursor-pointer" title="Actualizar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                                </svg>
                            </button>

                        </div>

                        <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                            <table class="min-w-full text-xs text-left whitespace-nowrap">
                                <thead class="bg-gray-50/70 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    <tr>
                                        @if($visibleColumns['rank'])      <th class="px-4 py-3 text-center w-12">No.</th> @endif
                                        @if($visibleColumns['cliente'])   <th class="px-4 py-3">Cliente</th> @endif
                                        @if($visibleColumns['ruta'])      <th class="px-4 py-3">Ruta</th> @endif
                                        @if($visibleColumns['total'])     <th class="px-4 py-3 text-right">Total <span class="text-[9px] font-normal text-gray-400 ml-0.5">↓</span></th> @endif
                                        @if($visibleColumns['contado'])   <th class="px-4 py-3 text-right">Total de contado</th> @endif
                                        @if($visibleColumns['credito'])   <th class="px-4 py-3 text-right">Total de crédito</th> @endif
                                        @if($visibleColumns['ventas'])    <th class="px-4 py-3 text-center">Ventas</th> @endif
                                        @if($visibleColumns['venta_mes']) <th class="px-4 py-3 text-center">Venta por mes</th> @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white text-gray-700 font-medium">
                                    @forelse(array_slice($registrosFiltrados, 0, (int) $filas_por_pagina) as $row)
                                        <tr class="hover:bg-gray-50/40 transition duration-150">
                                            @if($visibleColumns['rank'])
                                                <td class="px-4 py-3 text-center font-bold font-mono text-gray-500">{{ $row['rank'] }}</td>
                                            @endif
                                            @if($visibleColumns['cliente'])
                                                <td class="px-4 py-3 text-gray-800 font-semibold">{{ $row['cliente_codigo'] }} {{ $row['cliente_nombre'] }}</td>
                                            @endif
                                            @if($visibleColumns['ruta'])
                                                <td class="px-4 py-3 text-gray-500 font-semibold">{{ $row['ruta'] }}</td>
                                            @endif
                                            @if($visibleColumns['total'])
                                                <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">${{ number_format($row['total_sales']) }}</td>
                                            @endif
                                            @if($visibleColumns['contado'])
                                                <td class="px-4 py-3 text-right font-mono text-gray-600">${{ number_format($row['total_contado'], 2) }}</td>
                                            @endif
                                            @if($visibleColumns['credito'])
                                                <td class="px-4 py-3 text-right font-mono text-gray-600">${{ number_format($row['total_credito'], 2) }}</td>
                                            @endif
                                            @if($visibleColumns['ventas'])
                                                <td class="px-4 py-3 text-center font-mono font-semibold text-gray-700">{{ $row['total_orders'] }}</td>
                                            @endif
                                            @if($visibleColumns['venta_mes'])
                                                <td class="px-4 py-3 text-center font-mono text-gray-500">{{ $row['venta_mes'] > 0 ? '$'.number_format($row['venta_mes'], 2) : '0' }}</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-6 text-center text-gray-400 font-semibold bg-white">
                                                No hay Registros para mostrar
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                
                                {{-- Fila de Totales --}}
                                @php
                                    $filtered = collect($registrosFiltrados);
                                @endphp
                                <tfoot class="bg-white font-bold border-t border-gray-200 text-[#1f2937]">
                                    <tr>
                                        @if($visibleColumns['rank'])      <td class="px-4 py-3.5"></td> @endif
                                        @if($visibleColumns['cliente'])   <td class="px-4 py-3.5"></td> @endif
                                        @if($visibleColumns['ruta'])      <td class="px-4 py-3.5"></td> @endif
                                        @if($visibleColumns['total'])     <td class="px-4 py-3.5 text-right font-mono text-gray-900 font-extrabold">{{ $filtered->sum('total_sales') > 0 ? '$'.number_format($filtered->sum('total_sales')) : '$0' }}</td> @endif
                                        @if($visibleColumns['contado'])   <td class="px-4 py-3.5 text-right font-mono font-extrabold">{{ $filtered->sum('total_contado') > 0 ? '$'.number_format($filtered->sum('total_contado'), 2) : '$0' }}</td> @endif
                                        @if($visibleColumns['credito'])   <td class="px-4 py-3.5 text-right font-mono font-extrabold">{{ $filtered->sum('total_credito') > 0 ? '$'.number_format($filtered->sum('total_credito'), 2) : '$0' }}</td> @endif
                                        @if($visibleColumns['ventas'])    <td class="px-4 py-3.5 text-center font-mono font-extrabold">{{ $filtered->sum('total_orders') }}</td> @endif
                                        @if($visibleColumns['venta_mes']) <td class="px-4 py-3.5 text-center font-mono font-extrabold">{{ $filtered->sum('venta_mes') > 0 ? '$'.number_format($filtered->sum('venta_mes'), 2) : '0' }}</td> @endif
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Paginador (no-print) --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-4 text-xs text-gray-500 font-medium no-print">
                            <div class="flex items-center gap-2">
                                <select wire:model.live="filas_por_pagina" class="border-none bg-transparent rounded px-1.5 py-0.5 text-xs text-gray-600 focus:outline-none cursor-pointer font-semibold">
                                    <option value="10">10 Filas por Página</option>
                                    <option value="25">25 Filas por Página</option>
                                    <option value="50">50 Filas por Página</option>
                                    <option value="100">100 Filas por Página</option>
                                    <option value="1000">1000 Filas por Página</option>
                                </select>
                            </div>
                            <span class="border-l border-gray-200 h-4 hidden sm:block"></span>
                            <div class="flex items-center gap-1.5 font-mono font-semibold">
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>|<</button>
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled><</button>
                                <span class="px-1 text-gray-600 font-bold">
                                    @if(count($registrosFiltrados) > 0)
                                        1-{{ min(count($registrosFiltrados), $filas_por_pagina) }} of {{ count($registrosFiltrados) }}
                                    @else
                                        0-0 of 0
                                    @endif
                                </span>
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>></button>
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>>|</button>
                            </div>
                        </div>
                    </div>

                    {{-- Copyright en impresión --}}
                    <div class="hidden print:block text-center text-[10px] text-gray-400 mt-12 font-medium">
                        Copyright © JB VEMOBILE SA DE CV 2026.
                    </div>

                </div>
            @endif

            {{-- Panel de Gráficos (Card lateral doble en no-print) --}}
            @if($consultado && !empty($registrosFiltrados))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 no-print">
                    
                    {{-- Gráfico 1: Totales --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80">
                        <h3 class="font-bold text-sm text-gray-700 mb-4 select-none">Totales</h3>
                        <div class="relative h-72 w-full">
                            <canvas id="chartTopSales" wire:ignore></canvas>
                        </div>
                    </div>

                    {{-- Gráfico 2: Ventas --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80">
                        <h3 class="font-bold text-sm text-gray-700 mb-4 select-none">Ventas</h3>
                        <div class="relative h-72 w-full">
                            <canvas id="chartTopOrders" wire:ignore></canvas>
                        </div>
                    </div>

                </div>
            @endif

            {{-- Footer Copyright visible en pantalla --}}
            <div class="mt-12 text-center text-xs text-gray-400 font-medium no-print">
                Copyright © JB VEMOBILE SA DE CV 2026.
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('topClientsReports', () => ({
        chartBar: null,
        chartOrders: null,

        getChartData() {
            const rows = this.$wire.registrosFiltrados || [];
            return {
                labels: rows.map(r => r.cliente_codigo),
                sales: rows.map(r => parseFloat(r.total_sales)),
                orders: rows.map(r => parseInt(r.total_orders)),
            };
        },

        initCharts() {
            this.$nextTick(() => {
                this.buildCharts(this.getChartData());
            });

            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    this.$nextTick(() => {
                        const data = this.getChartData();
                        const canvasBar = document.getElementById('chartTopSales');
                        const canvasOrders = document.getElementById('chartTopOrders');

                        if (this.chartBar && this.chartBar.canvas !== canvasBar) {
                            this.chartBar.destroy();
                            this.chartBar = null;
                        }
                        if (this.chartOrders && this.chartOrders.canvas !== canvasOrders) {
                            this.chartOrders.destroy();
                            this.chartOrders = null;
                        }

                        if (!this.chartBar || !this.chartOrders) {
                            this.buildCharts(data);
                        } else {
                            this.updateCharts(data);
                        }
                    });
                });
            });
        },

        buildCharts(data) {
            const barCtx = document.getElementById('chartTopSales');
            const ordersCtx = document.getElementById('chartTopOrders');

            if (this.chartBar) this.chartBar.destroy();
            if (this.chartOrders) this.chartOrders.destroy();

            // Gráfico de Totales
            if (barCtx && data.sales.length > 0) {
                this.chartBar = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.sales,
                            backgroundColor: '#3b82f6', // Color azul estándar de la captura
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            barThickness: 32
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
                                        return 'Monto: $' + context.raw.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Ventas
            if (ordersCtx && data.orders.length > 0) {
                this.chartOrders = new Chart(ordersCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.orders,
                            backgroundColor: '#3b82f6', // Color azul estándar de la captura
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            barThickness: 32
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
                                        return 'Ventas: ' + context.raw;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        },

        updateCharts(data) {
            if (this.chartBar) {
                this.chartBar.data.labels = data.labels;
                this.chartBar.data.datasets[0].data = data.sales;
                this.chartBar.update();
            }
            if (this.chartOrders) {
                this.chartOrders.data.labels = data.labels;
                this.chartOrders.data.datasets[0].data = data.orders;
                this.chartOrders.update();
            }
        }
    }));
});
</script>
@endpush
