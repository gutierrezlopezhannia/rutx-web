<?php

use function Livewire\Volt\{state, layout};
use Carbon\Carbon;

layout('layouts.app');

// ─── Datos mock (se sustituirán con query a Firebird) ─────────────────────────
$mockReportesGlobales = [
    [
        'vendedor' => '3983 - RUTA01', 'zona' => '1Z - Zona 1', 'ruta' => '3983 - RUTA01',
        'fecha' => '2026-07-15',
        'credito' => 606.00, 'contado' => 4485.00, 'cobrado' => 110.00,
        'cobranza_efectivo' => 0.00, 'gasto' => 0.00,
        'devolucion_contado' => 24.00, 'devolucion_credito' => 0.00,
        'unidades_devueltas' => 3, 'productos' => 382,
        'preventa' => 15028.00, 'producto_preventa' => 102,
        'entrega_contado' => 0.00, 'entrega_credito' => 0.00,
        'no_venta' => 0, 'cambio' => 0.00,
        'total_neto' => 5067.00, 'deposito' => 4500.00,
        'faltante' => 567.00, 'sobrante' => 0.00,
        'piezas' => 385,
    ],
    [
        'vendedor' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'ruta' => '4682 - RUTA02',
        'fecha' => '2026-07-15',
        'credito' => 0.00, 'contado' => 14.00, 'cobrado' => 0.00,
        'cobranza_efectivo' => 0.00, 'gasto' => 0.00,
        'devolucion_contado' => 0.00, 'devolucion_credito' => 0.00,
        'unidades_devueltas' => 0, 'productos' => 1,
        'preventa' => 0.00, 'producto_preventa' => 0,
        'entrega_contado' => 0.00, 'entrega_credito' => 0.00,
        'no_venta' => 1, 'cambio' => 0.00,
        'total_neto' => 14.00, 'deposito' => 14.00,
        'faltante' => 0.00, 'sobrante' => 0.00,
        'piezas' => 1,
    ],
    [
        'vendedor' => '5021 - RUTA03', 'zona' => '2Z - Zona 2', 'ruta' => '5021 - RUTA03',
        'fecha' => '2026-07-15',
        'credito' => 1200.00, 'contado' => 3200.00, 'cobrado' => 850.00,
        'cobranza_efectivo' => 400.00, 'gasto' => 120.00,
        'devolucion_contado' => 0.00, 'devolucion_credito' => 50.00,
        'unidades_devueltas' => 2, 'productos' => 245,
        'preventa' => 9800.00, 'producto_preventa' => 78,
        'entrega_contado' => 500.00, 'entrega_credito' => 700.00,
        'no_venta' => 2, 'cambio' => 15.00,
        'total_neto' => 4265.00, 'deposito' => 4000.00,
        'faltante' => 265.00, 'sobrante' => 0.00,
        'piezas' => 247,
    ],
    [
        'vendedor' => '6104 - RUTA04', 'zona' => '2Z - Zona 2', 'ruta' => '6104 - RUTA04',
        'fecha' => '2026-07-14',
        'credito' => 2500.00, 'contado' => 6800.00, 'cobrado' => 2100.00,
        'cobranza_efectivo' => 900.00, 'gasto' => 85.00,
        'devolucion_contado' => 130.00, 'devolucion_credito' => 0.00,
        'unidades_devueltas' => 5, 'productos' => 510,
        'preventa' => 22000.00, 'producto_preventa' => 195,
        'entrega_contado' => 1500.00, 'entrega_credito' => 1000.00,
        'no_venta' => 3, 'cambio' => 40.00,
        'total_neto' => 9175.00, 'deposito' => 9000.00,
        'faltante' => 175.00, 'sobrante' => 0.00,
        'piezas' => 515,
    ],
    [
        'vendedor' => '7290 - RUTA05', 'zona' => '3Z - Zona 3', 'ruta' => '7290 - RUTA05',
        'fecha' => '2026-07-14',
        'credito' => 800.00, 'contado' => 2100.00, 'cobrado' => 450.00,
        'cobranza_efectivo' => 200.00, 'gasto' => 60.00,
        'devolucion_contado' => 0.00, 'devolucion_credito' => 25.00,
        'unidades_devueltas' => 1, 'productos' => 130,
        'preventa' => 5500.00, 'producto_preventa' => 48,
        'entrega_contado' => 0.00, 'entrega_credito' => 300.00,
        'no_venta' => 0, 'cambio' => 5.00,
        'total_neto' => 2835.00, 'deposito' => 2800.00,
        'faltante' => 35.00, 'sobrante' => 0.00,
        'piezas' => 131,
    ],
];

state([
    'registros'          => $mockReportesGlobales,
    'registrosFiltrados' => [],   // Vacío al cargar; se llena al presionar Consultar

    // Filtros
    'filtro_zona'  => 'todos',
    'filtro_ruta'  => 'todos',
    'fecha_inicio' => '2026-07-14',
    'fecha_fin'    => '2026-07-15',

    // Columnas visibles
    'visibleColumns' => [
        'credito'             => true,
        'contado'             => true,
        'cobrado'             => true,
        'cobranza_efectivo'   => true,
        'gasto'               => true,
        'devolucion_contado'  => true,
        'devolucion_credito'  => true,
        'unidades_devueltas'  => true,
        'productos'           => true,
        'preventa'            => true,
        'producto_preventa'   => true,
        'entrega_contado'     => true,
        'entrega_credito'     => true,
        'no_venta'            => true,
        'cambio'              => true,
        'total_neto'          => true,
        'deposito'            => true,
        'faltante'            => true,
        'sobrante'            => true,
    ],
]);

$aplicarFiltros = function () {
    $filtrados = collect($this->registros);

    if ($this->filtro_zona !== 'todos') {
        $filtrados = $filtrados->where('zona', $this->filtro_zona);
    }

    if ($this->filtro_ruta !== 'todos') {
        $filtrados = $filtrados->where('ruta', $this->filtro_ruta);
    }

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

    $this->registrosFiltrados = $filtrados->values()->toArray();
};

$consultar = function () {
    $this->aplicarFiltros();
};

// Los filtros solo se aplican al presionar el botón Consultar
?>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endpush
@endonce

<div>
    <div class="py-4" x-data="globalReports()" x-init="initCharts()">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-4 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Reportes Globales</span>
            </div>

            {{-- Main Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 mb-6">

                {{-- Title --}}
                <h2 class="text-lg font-bold text-[#1f2937] mb-4">Reportes Globales</h2>

                {{-- Filtros --}}
                <div class="flex flex-wrap items-end gap-4 mb-4">

                    {{-- Zona --}}
                    <div class="flex flex-col w-44">
                        <label class="text-xs text-gray-500 font-medium mb-1">Zona</label>
                        <select wire:model="filtro_zona"
                            class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer">
                            <option value="todos">Todas</option>
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                    </div>

                    {{-- Ruta --}}
                    <div class="flex flex-col w-44">
                        <label class="text-xs text-gray-500 font-medium mb-1">Ruta</label>
                        <select wire:model="filtro_ruta"
                            class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer">
                            <option value="todos">Todas</option>
                            <option value="3983 - RUTA01">3983 - RUTA01</option>
                            <option value="4682 - RUTA02">4682 - RUTA02</option>
                            <option value="5021 - RUTA03">5021 - RUTA03</option>
                            <option value="6104 - RUTA04">6104 - RUTA04</option>
                            <option value="7290 - RUTA05">7290 - RUTA05</option>
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-40">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha Inicial</label>
                        <div class="flex items-center border-0 border-b border-gray-300 py-1">
                            <input type="date" wire:model="fecha_inicio"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-40">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha final</label>
                        <div class="flex items-center border-0 border-b border-gray-300 py-1">
                            <input type="date" wire:model="fecha_fin"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Botón Consultar --}}
                    <button wire:click="consultar"
                        class="px-6 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded-lg text-sm font-semibold transition duration-150 cursor-pointer">
                        Consultar
                    </button>
                </div>

                {{-- Action Icons --}}
                <div class="flex justify-end space-x-1 mb-3 text-gray-400">
                    {{-- Dropdown Columnas --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false"
                            class="p-2 hover:bg-gray-100 rounded-full transition" title="Columnas">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 5h16v2H4zm0 6h16v2H4zm0 6h16v2H4z" />
                            </svg>
                        </button>
                        <div x-show="open" style="display:none;"
                            class="absolute right-0 mt-2 w-60 bg-white border border-gray-200 shadow-xl rounded-lg z-50 p-2 max-h-96 overflow-y-auto">
                            <div class="text-xs font-semibold text-gray-600 mb-2 px-2">Mostrar / Ocultar columnas</div>
                            @foreach([
                                'credito'             => 'Crédito',
                                'contado'             => 'Contado',
                                'cobrado'             => 'Cobrado',
                                'cobranza_efectivo'   => 'Cobranza en efectivo',
                                'gasto'               => 'Gasto',
                                'devolucion_contado'  => 'Devolución de contado',
                                'devolucion_credito'  => 'Devolución de crédito',
                                'unidades_devueltas'  => 'Unidades devueltas',
                                'productos'           => 'Productos',
                                'preventa'            => 'Preventa',
                                'producto_preventa'   => 'Producto Preventa',
                                'entrega_contado'     => 'Entrega de contado',
                                'entrega_credito'     => 'Entrega de crédito',
                                'no_venta'            => 'No Venta',
                                'cambio'              => 'Cambio',
                                'total_neto'          => 'Total neto',
                                'deposito'            => 'Depósito',
                                'faltante'            => 'Faltante',
                                'sobrante'            => 'Sobrante',
                            ] as $key => $label)
                                <label class="flex items-center space-x-3 px-2 py-1.5 hover:bg-gray-50 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="visibleColumns.{{ $key }}"
                                        class="text-[#003859] rounded border-gray-300 focus:ring-[#003859] w-4 h-4" />
                                    <span class="text-sm text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button class="p-2 hover:bg-gray-100 rounded-full transition" title="Exportar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    <button wire:click="consultar" class="p-2 hover:bg-gray-100 rounded-full transition" title="Actualizar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                {{-- Tabla --}}
                @if(!empty($registrosFiltrados))
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="w-full text-xs text-left whitespace-nowrap">
                        <thead class="bg-gray-50/70 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 sticky left-0 bg-gray-50 shadow-[1px_0_0_rgba(229,231,235,1)] z-10">
                                    Vendedor
                                </th>
                                @if($visibleColumns['credito'])            <th class="px-4 py-3 text-right">Crédito</th> @endif
                                @if($visibleColumns['contado'])            <th class="px-4 py-3 text-right">Contado</th> @endif
                                @if($visibleColumns['cobrado'])            <th class="px-4 py-3 text-right">Cobrado</th> @endif
                                @if($visibleColumns['cobranza_efectivo'])  <th class="px-4 py-3 text-right">Cobranza en efectivo</th> @endif
                                @if($visibleColumns['gasto'])              <th class="px-4 py-3 text-right">Gasto</th> @endif
                                @if($visibleColumns['devolucion_contado']) <th class="px-4 py-3 text-right">Devolución de contado</th> @endif
                                @if($visibleColumns['devolucion_credito']) <th class="px-4 py-3 text-right">Devolución de crédito</th> @endif
                                @if($visibleColumns['unidades_devueltas']) <th class="px-4 py-3 text-right">Unidades devueltas</th> @endif
                                @if($visibleColumns['productos'])          <th class="px-4 py-3 text-right">Productos</th> @endif
                                @if($visibleColumns['preventa'])           <th class="px-4 py-3 text-right">Preventa</th> @endif
                                @if($visibleColumns['producto_preventa'])  <th class="px-4 py-3 text-right">Producto Preventa</th> @endif
                                @if($visibleColumns['entrega_contado'])    <th class="px-4 py-3 text-right">Entrega de contado</th> @endif
                                @if($visibleColumns['entrega_credito'])    <th class="px-4 py-3 text-right">Entrega de crédito</th> @endif
                                @if($visibleColumns['no_venta'])           <th class="px-4 py-3 text-right">No Venta</th> @endif
                                @if($visibleColumns['cambio'])             <th class="px-4 py-3 text-right">Cambio</th> @endif
                                @if($visibleColumns['total_neto'])         <th class="px-4 py-3 text-right">Total neto</th> @endif
                                @if($visibleColumns['deposito'])           <th class="px-4 py-3 text-right">Depósito</th> @endif
                                @if($visibleColumns['faltante'])           <th class="px-4 py-3 text-right">Faltante</th> @endif
                                @if($visibleColumns['sobrante'])           <th class="px-4 py-3 text-right">Sobrante</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                            @forelse($registrosFiltrados as $row)
                                <tr class="hover:bg-gray-50/40 transition duration-150">
                                    <td class="px-4 py-3 sticky left-0 bg-white hover:bg-gray-50/40 shadow-[1px_0_0_rgba(229,231,235,1)] z-10 text-[#003859] font-semibold">
                                        {{ $row['vendedor'] }}
                                    </td>
                                    @if($visibleColumns['credito'])            <td class="px-4 py-3 text-right">${{ number_format($row['credito'], 2) }}</td> @endif
                                    @if($visibleColumns['contado'])            <td class="px-4 py-3 text-right">${{ number_format($row['contado'], 2) }}</td> @endif
                                    @if($visibleColumns['cobrado'])            <td class="px-4 py-3 text-right">${{ number_format($row['cobrado'], 2) }}</td> @endif
                                    @if($visibleColumns['cobranza_efectivo'])  <td class="px-4 py-3 text-right">${{ number_format($row['cobranza_efectivo'], 2) }}</td> @endif
                                    @if($visibleColumns['gasto'])              <td class="px-4 py-3 text-right">${{ number_format($row['gasto'], 2) }}</td> @endif
                                    @if($visibleColumns['devolucion_contado']) <td class="px-4 py-3 text-right">${{ number_format($row['devolucion_contado'], 2) }}</td> @endif
                                    @if($visibleColumns['devolucion_credito']) <td class="px-4 py-3 text-right">${{ number_format($row['devolucion_credito'], 2) }}</td> @endif
                                    @if($visibleColumns['unidades_devueltas']) <td class="px-4 py-3 text-right">{{ $row['unidades_devueltas'] }}</td> @endif
                                    @if($visibleColumns['productos'])          <td class="px-4 py-3 text-right">{{ $row['productos'] }}</td> @endif
                                    @if($visibleColumns['preventa'])           <td class="px-4 py-3 text-right">${{ number_format($row['preventa'], 2) }}</td> @endif
                                    @if($visibleColumns['producto_preventa'])  <td class="px-4 py-3 text-right">{{ $row['producto_preventa'] }}</td> @endif
                                    @if($visibleColumns['entrega_contado'])    <td class="px-4 py-3 text-right">${{ number_format($row['entrega_contado'], 2) }}</td> @endif
                                    @if($visibleColumns['entrega_credito'])    <td class="px-4 py-3 text-right">${{ number_format($row['entrega_credito'], 2) }}</td> @endif
                                    @if($visibleColumns['no_venta'])           <td class="px-4 py-3 text-right">{{ $row['no_venta'] }}</td> @endif
                                    @if($visibleColumns['cambio'])             <td class="px-4 py-3 text-right">${{ number_format($row['cambio'], 2) }}</td> @endif
                                    @if($visibleColumns['total_neto'])         <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($row['total_neto'], 2) }}</td> @endif
                                    @if($visibleColumns['deposito'])           <td class="px-4 py-3 text-right">${{ number_format($row['deposito'], 2) }}</td> @endif
                                    @if($visibleColumns['faltante'])           <td class="px-4 py-3 text-right">{{ $row['faltante'] > 0 ? '$'.number_format($row['faltante'], 2) : '—' }}</td> @endif
                                    @if($visibleColumns['sobrante'])           <td class="px-4 py-3 text-right">{{ $row['sobrante'] > 0 ? '$'.number_format($row['sobrante'], 2) : '—' }}</td> @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        No hay Registros para mostrar
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        {{-- Fila de totales --}}
                        @php
                            $filtered = collect($registrosFiltrados);
                        @endphp
                        @if($filtered->count() > 0)
                        <tfoot class="bg-gray-50 font-bold border-t-2 border-gray-300 text-[#1f2937]">
                            <tr>
                                <td class="px-4 py-3 sticky left-0 bg-gray-50 shadow-[1px_0_0_rgba(229,231,235,1)] z-10">Total neto</td>
                                @if($visibleColumns['credito'])            <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('credito'), 2) }}</td> @endif
                                @if($visibleColumns['contado'])            <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('contado'), 2) }}</td> @endif
                                @if($visibleColumns['cobrado'])            <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('cobrado'), 2) }}</td> @endif
                                @if($visibleColumns['cobranza_efectivo'])  <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('cobranza_efectivo'), 2) }}</td> @endif
                                @if($visibleColumns['gasto'])              <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('gasto'), 2) }}</td> @endif
                                @if($visibleColumns['devolucion_contado']) <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('devolucion_contado'), 2) }}</td> @endif
                                @if($visibleColumns['devolucion_credito']) <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('devolucion_credito'), 2) }}</td> @endif
                                @if($visibleColumns['unidades_devueltas']) <td class="px-4 py-3 text-right">{{ $filtered->sum('unidades_devueltas') }}</td> @endif
                                @if($visibleColumns['productos'])          <td class="px-4 py-3 text-right">{{ $filtered->sum('productos') }}</td> @endif
                                @if($visibleColumns['preventa'])           <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('preventa'), 2) }}</td> @endif
                                @if($visibleColumns['producto_preventa'])  <td class="px-4 py-3 text-right">{{ $filtered->sum('producto_preventa') }}</td> @endif
                                @if($visibleColumns['entrega_contado'])    <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('entrega_contado'), 2) }}</td> @endif
                                @if($visibleColumns['entrega_credito'])    <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('entrega_credito'), 2) }}</td> @endif
                                @if($visibleColumns['no_venta'])           <td class="px-4 py-3 text-right">{{ $filtered->sum('no_venta') }}</td> @endif
                                @if($visibleColumns['cambio'])             <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('cambio'), 2) }}</td> @endif
                                @if($visibleColumns['total_neto'])         <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('total_neto'), 2) }}</td> @endif
                                @if($visibleColumns['deposito'])           <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('deposito'), 2) }}</td> @endif
                                @if($visibleColumns['faltante'])           <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('faltante'), 2) }}</td> @endif
                                @if($visibleColumns['sobrante'])           <td class="px-4 py-3 text-right">${{ number_format($filtered->sum('sobrante'), 2) }}</td> @endif
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @endif
            </div>

            {{-- Gráficos: solo se muestran cuando hay datos --}}
            @if(!empty($registrosFiltrados))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200/80">
                    <h3 class="font-semibold text-sm text-gray-700 mb-3">Totales</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="chartTotales" wire:ignore></canvas>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200/80">
                    <h3 class="font-semibold text-sm text-gray-700 mb-3">Piezas</h3>
                    <div class="relative h-64 w-full">
                        <canvas id="chartPiezas" wire:ignore></canvas>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('globalReports', () => ({
        chartT: null,
        chartP: null,

        getChartData() {
            // Lee los datos directamente de Livewire
            const rows = this.$wire.registrosFiltrados || [];
            return {
                labels:  rows.map(r => r.vendedor),
                totales: rows.map(r => parseFloat(r.total_neto)),
                piezas:  rows.map(r => parseInt(r.piezas)),
            };
        },

        initCharts() {
            // Intentar construir al inicio
            this.buildCharts(this.getChartData());

            // Escuchar cuando Livewire actualice el DOM (ej. al presionar Consultar)
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    this.$nextTick(() => {
                        const data = this.getChartData();
                        const canvasT = document.getElementById('chartTotales');
                        
                        // Si el canvas en el DOM es diferente al que tiene la gráfica (Livewire lo recreó), destruimos la instancia
                        if (this.chartT && this.chartT.canvas !== canvasT) {
                            this.chartT.destroy();
                            this.chartT = null;
                        }
                        if (this.chartP && this.chartP.canvas !== document.getElementById('chartPiezas')) {
                            this.chartP.destroy();
                            this.chartP = null;
                        }

                        // Si no existían los gráficos (porque no había canvas o se recrearon), construirlos
                        if (!this.chartT || !this.chartP) {
                            this.buildCharts(data);
                        } else {
                            this.updateCharts(data);
                        }
                    });
                });
            });
        },

        buildCharts(data) {
            const cfg = (canvasId, label, values, color) => {
                const ctx = document.getElementById(canvasId);
                if (!ctx) return null;
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{ label, data: values, backgroundColor: color }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            };

            if (this.chartT) this.chartT.destroy();
            if (this.chartP) this.chartP.destroy();

            this.chartT = cfg('chartTotales', 'Total neto ($)', data.totales, '#003859');
            this.chartP = cfg('chartPiezas',  'Piezas',          data.piezas,  '#003859');
        },

        updateCharts(data) {
            if (this.chartT) {
                this.chartT.data.labels = data.labels;
                this.chartT.data.datasets[0].data = data.totales;
                this.chartT.update();
            }
            if (this.chartP) {
                this.chartP.data.labels = data.labels;
                this.chartP.data.datasets[0].data = data.piezas;
                this.chartP.update();
            }
        }
    }));
});
</script>
@endpush
