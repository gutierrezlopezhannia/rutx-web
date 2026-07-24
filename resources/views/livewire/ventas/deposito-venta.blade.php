<?php

use function Livewire\Volt\{state, layout, updated};

layout('layouts.app');

$mockLiquidaciones = [
    [
        'id' => 1,
        'ruta' => '4682 - RUTA02',
        'fecha' => '22-07-2026 00:00:00',
        'contado' => 28.00,
        'credito' => 0.00,
        'num_preventas' => 0,
        'total_preventa' => 0.00,
        'entrega_contado' => 0.00,
        'entrega_credito' => 0.00,
        'cobranza' => 0.00,
        'cobranza_efectivo' => 0.00,
        'gastos_operativos' => 0.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'total' => 28.00,
        'total_cierre' => 28.00,
        'total_efectivo' => 28.00,
        'diferencia' => 0.00
    ],
    [
        'id' => 2,
        'ruta' => '4683 - RUTA03',
        'fecha' => '21-07-2026 00:00:00',
        'contado' => 1540.50,
        'credito' => 320.00,
        'num_preventas' => 5,
        'total_preventa' => 4200.00,
        'entrega_contado' => 980.00,
        'entrega_credito' => 1100.00,
        'cobranza' => 850.00,
        'cobranza_efectivo' => 850.00,
        'gastos_operativos' => 120.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'total' => 3940.50,
        'total_cierre' => 3250.50,
        'total_efectivo' => 3276.00,
        'diferencia' => 25.50
    ],
    [
        'id' => 3,
        'ruta' => '4684 - RUTA04',
        'fecha' => '21-07-2026 00:00:00',
        'contado' => 3200.00,
        'credito' => 500.00,
        'num_preventas' => 8,
        'total_preventa' => 7800.00,
        'entrega_contado' => 2100.00,
        'entrega_credito' => 1500.00,
        'cobranza' => 1200.00,
        'cobranza_efectivo' => 1200.00,
        'gastos_operativos' => 200.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'total' => 7300.00,
        'total_cierre' => 6300.00,
        'total_efectivo' => 6300.00,
        'diferencia' => 0.00
    ],
    [
        'id' => 4,
        'ruta' => '4682 - RUTA02',
        'fecha' => '20-07-2026 00:00:00',
        'contado' => 890.00,
        'credito' => 0.00,
        'num_preventas' => 2,
        'total_preventa' => 1600.00,
        'entrega_contado' => 500.00,
        'entrega_credito' => 0.00,
        'cobranza' => 0.00,
        'cobranza_efectivo' => 0.00,
        'gastos_operativos' => 50.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'total' => 1390.00,
        'total_cierre' => 1340.00,
        'total_efectivo' => 1325.00,
        'diferencia' => -15.00
    ],
    [
        'id' => 5,
        'ruta' => '4685 - RUTA05',
        'fecha' => '20-07-2026 00:00:00',
        'contado' => 5100.00,
        'credito' => 750.00,
        'num_preventas' => 12,
        'total_preventa' => 12500.00,
        'entrega_contado' => 3200.00,
        'entrega_credito' => 2100.00,
        'cobranza' => 1800.00,
        'cobranza_efectivo' => 1800.00,
        'gastos_operativos' => 350.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'total' => 11150.00,
        'total_cierre' => 9750.00,
        'total_efectivo' => 9750.00,
        'diferencia' => 0.00
    ],
];

state([
    'registros' => $mockLiquidaciones,
    'registrosFiltrados' => [],
    'filtro_zona' => '1Z',
    'filtro_ruta' => '',
    'fecha_inicio' => '2026-07-20',
    'fecha_fin' => '2026-07-23',
    'pagina_actual' => 1,
    'por_pagina' => 100,
    // Modal Nuevo Cierre
    'showModal' => false,
    'nueva_ruta' => '3983 - RUTA01',
    'nueva_fecha' => '',
    'nueva_total_productos' => 0,
    'nueva_preventas' => 0,
    'nueva_total_preventa' => 0.00,
    'nueva_entrega_contado' => 0.00,
    'nueva_entrega_credito' => 0.00,
    'nueva_contado' => 0.00,
    'nueva_credito' => 0.00,
    'nueva_cobrado' => 0.00,
    'nueva_cobrado_efectivo' => 0.00,
    'nueva_devolucion' => 0.00,
    'nueva_devolucion_contado' => 0.00,
    'nueva_devolucion_credito' => 0.00,
    'nueva_gasto_operativo' => 0.00,
    'nueva_total_cierre' => 0.00,
    'nueva_total_efectivo' => '',
    'nueva_faltante' => 0.00,
    'nueva_sobrante' => 0.00,
    'nueva_comentario' => '',
    // Filtro inline
    'showFilter' => false,
    'filter_column' => '#',
    'filter_operator' => 'contains',
    'filter_value' => '',
    // Columnas visibles
    'columnasVisibles' => [
        'id' => true,
        'ruta' => true,
        'fecha' => true,
        'contado' => true,
        'credito' => true,
        'num_preventas' => true,
        'total_preventa' => true,
        'entrega_contado' => true,
        'entrega_credito' => true,
        'cobranza' => true,
        'cobranza_efectivo' => true,
        'gastos_operativos' => true,
        'devolucion_contado' => true,
        'devolucion_credito' => true,
        'total' => true,
        'total_cierre' => true,
    ],
]);

$obtenerRutas = function () {
    $rutasPorZona = [
        '1Z' => ['3983 - RUTA01', '4682 - RUTA02', '4683 - RUTA03'],
        '2Z' => ['4684 - RUTA04', '4685 - RUTA05'],
    ];
    return $rutasPorZona[$this->filtro_zona] ?? [];
};

$recalcularDiferencia = function () {
    $efectivo = floatval($this->nueva_total_efectivo ?: 0);
    $cierre = floatval($this->nueva_total_cierre ?: 0);

    if ($efectivo > $cierre) {
        $this->nueva_sobrante = $efectivo - $cierre;
        $this->nueva_faltante = 0.00;
    } elseif ($efectivo < $cierre) {
        $this->nueva_faltante = $cierre - $efectivo;
        $this->nueva_sobrante = 0.00;
    } else {
        $this->nueva_faltante = 0.00;
        $this->nueva_sobrante = 0.00;
    }
};

$actualizarDatosCierre = function () {
    $ruta = $this->nueva_ruta;

    $detalles = [
        '3983 - RUTA01' => [
            'total_productos' => 45,
            'preventas' => 3,
            'total_preventa' => 2500.00,
            'entrega_contado' => 1200.00,
            'entrega_credito' => 800.00,
            'contado' => 1500.00,
            'credito' => 600.00,
            'cobrado' => 900.00,
            'cobrado_efectivo' => 700.00,
            'devolucion' => 150.00,
            'devolucion_contado' => 50.00,
            'devolucion_credito' => 100.00,
            'gasto_operativo' => 120.00,
        ],
        '4682 - RUTA02' => [
            'total_productos' => 28,
            'preventas' => 2,
            'total_preventa' => 1600.00,
            'entrega_contado' => 500.00,
            'entrega_credito' => 400.00,
            'contado' => 890.00,
            'credito' => 300.00,
            'cobrado' => 0.00,
            'cobrado_efectivo' => 0.00,
            'devolucion' => 50.00,
            'devolucion_contado' => 20.00,
            'devolucion_credito' => 30.00,
            'gasto_operativo' => 50.00,
        ],
        '4683 - RUTA03' => [
            'total_productos' => 60,
            'preventas' => 5,
            'total_preventa' => 4200.00,
            'entrega_contado' => 980.00,
            'entrega_credito' => 1100.00,
            'contado' => 1540.50,
            'credito' => 320.00,
            'cobrado' => 850.00,
            'cobrado_efectivo' => 850.00,
            'devolucion' => 0.00,
            'devolucion_contado' => 0.00,
            'devolucion_credito' => 0.00,
            'gasto_operativo' => 120.00,
        ],
        '4684 - RUTA04' => [
            'total_productos' => 85,
            'preventas' => 8,
            'total_preventa' => 7800.00,
            'entrega_contado' => 2100.00,
            'entrega_credito' => 1500.00,
            'contado' => 3200.00,
            'credito' => 500.00,
            'cobrado' => 1200.00,
            'cobrado_efectivo' => 1200.00,
            'devolucion' => 0.00,
            'devolucion_contado' => 0.00,
            'devolucion_credito' => 0.00,
            'gasto_operativo' => 200.00,
        ],
        '4685 - RUTA05' => [
            'total_productos' => 120,
            'preventas' => 12,
            'total_preventa' => 12500.00,
            'entrega_contado' => 3200.00,
            'entrega_credito' => 2100.00,
            'contado' => 5100.00,
            'credito' => 750.00,
            'cobrado' => 1800.00,
            'cobrado_efectivo' => 1800.00,
            'devolucion' => 0.00,
            'devolucion_contado' => 0.00,
            'devolucion_credito' => 0.00,
            'gasto_operativo' => 350.00,
        ]
    ];

    $d = $detalles[$ruta] ?? [
        'total_productos' => 0,
        'preventas' => 0,
        'total_preventa' => 0.00,
        'entrega_contado' => 0.00,
        'entrega_credito' => 0.00,
        'contado' => 0.00,
        'credito' => 0.00,
        'cobrado' => 0.00,
        'cobrado_efectivo' => 0.00,
        'devolucion' => 0.00,
        'devolucion_contado' => 0.00,
        'devolucion_credito' => 0.00,
        'gasto_operativo' => 0.00,
    ];

    $this->nueva_total_productos = $d['total_productos'];
    $this->nueva_preventas = $d['preventas'];
    $this->nueva_total_preventa = $d['total_preventa'];
    $this->nueva_entrega_contado = $d['entrega_contado'];
    $this->nueva_entrega_credito = $d['entrega_credito'];
    $this->nueva_contado = $d['contado'];
    $this->nueva_credito = $d['credito'];
    $this->nueva_cobrado = $d['cobrado'];
    $this->nueva_cobrado_efectivo = $d['cobrado_efectivo'];
    $this->nueva_devolucion = $d['devolucion'];
    $this->nueva_devolucion_contado = $d['devolucion_contado'];
    $this->nueva_devolucion_credito = $d['devolucion_credito'];
    $this->nueva_gasto_operativo = $d['gasto_operativo'];

    $this->nueva_total_cierre = $this->nueva_contado + $this->nueva_entrega_contado + $this->nueva_cobrado_efectivo - $this->nueva_gasto_operativo - $this->nueva_devolucion_contado;

    $this->recalcularDiferencia();
};

updated([
    'filtro_zona' => function ($value) {
        $this->filtro_ruta = '';
        $this->registrosFiltrados = [];
    },
    'filtro_ruta' => function ($value) {
        $this->consultar();
    },
    'nueva_ruta' => function ($value) {
        $this->actualizarDatosCierre();
    },
    'nueva_total_efectivo' => function ($value) {
        $this->recalcularDiferencia();
    }
]);

$abrirModal = function () {
    $this->showModal = true;
    $this->nueva_fecha = now()->format('Y-m-d');
    $this->nueva_ruta = $this->filtro_ruta ?: '3983 - RUTA01';
    $this->nueva_total_efectivo = '';
    $this->nueva_comentario = '';
    $this->actualizarDatosCierre();
};

$cerrarModal = function () {
    $this->showModal = false;
};

$resetearFormulario = function () {
    $this->nueva_ruta = '3983 - RUTA01';
    $this->nueva_fecha = now()->format('Y-m-d');
    $this->nueva_total_productos = 0;
    $this->nueva_preventas = 0;
    $this->nueva_total_preventa = 0.00;
    $this->nueva_entrega_contado = 0.00;
    $this->nueva_entrega_credito = 0.00;
    $this->nueva_contado = 0.00;
    $this->nueva_credito = 0.00;
    $this->nueva_cobrado = 0.00;
    $this->nueva_cobrado_efectivo = 0.00;
    $this->nueva_devolucion = 0.00;
    $this->nueva_devolucion_contado = 0.00;
    $this->nueva_devolucion_credito = 0.00;
    $this->nueva_gasto_operativo = 0.00;
    $this->nueva_total_cierre = 0.00;
    $this->nueva_total_efectivo = '';
    $this->nueva_faltante = 0.00;
    $this->nueva_sobrante = 0.00;
    $this->nueva_comentario = '';
};

$guardarCierre = function () {
    $nuevo = [
        'id' => count($this->registros) + 1,
        'ruta' => $this->nueva_ruta,
        'fecha' => \Carbon\Carbon::parse($this->nueva_fecha)->format('d-m-Y 00:00:00'),
        'contado' => $this->nueva_contado,
        'credito' => $this->nueva_credito,
        'num_preventas' => $this->nueva_preventas,
        'total_preventa' => $this->nueva_total_preventa,
        'entrega_contado' => $this->nueva_entrega_contado,
        'entrega_credito' => $this->nueva_entrega_credito,
        'cobranza' => $this->nueva_cobrado,
        'cobranza_efectivo' => $this->nueva_cobrado_efectivo,
        'gastos_operativos' => $this->nueva_gasto_operativo,
        'devolucion_contado' => $this->nueva_devolucion_contado,
        'devolucion_credito' => $this->nueva_devolucion_credito,
        'total' => $this->nueva_contado + $this->nueva_credito + $this->nueva_entrega_contado + $this->nueva_entrega_credito,
        'total_cierre' => $this->nueva_total_cierre,
        'total_efectivo' => floatval($this->nueva_total_efectivo ?: 0),
        'diferencia' => $this->nueva_sobrante - $this->nueva_faltante,
    ];
    $this->registros[] = $nuevo;
    $this->consultar();
    $this->showModal = false;
    $this->resetearFormulario();
};

$toggleFilter = function () {
    $this->showFilter = !$this->showFilter;
};

$consultar = function () {
    if (empty($this->filtro_ruta)) {
        $this->registrosFiltrados = [];
        return;
    }

    $filtradas = collect($this->registros);
    $filtradas = $filtradas->where('ruta', $this->filtro_ruta);

    if ($this->fecha_inicio && $this->fecha_fin) {
        $inicio = \Carbon\Carbon::parse($this->fecha_inicio)->startOfDay();
        $fin = \Carbon\Carbon::parse($this->fecha_fin)->endOfDay();

        $filtradas = $filtradas->filter(function ($item) use ($inicio, $fin) {
            $itemFecha = \Carbon\Carbon::parse($item['fecha']);
            return $itemFecha->between($inicio, $fin);
        });
    }

    if (!empty($this->filter_value)) {
        $val = strtolower($this->filter_value);
        $col = $this->filter_column;
        $op = $this->filter_operator;

        $filtradas = $filtradas->filter(function ($item) use ($col, $op, $val) {
            $fieldMap = [
                '#' => 'id',
                'Ruta' => 'ruta',
                'Fecha' => 'fecha',
                'Contado' => 'contado',
                'Crédito' => 'credito',
            ];
            $field = $fieldMap[$col] ?? $col;
            $itemVal = strtolower((string) ($item[$field] ?? ''));
            return match ($op) {
                'contains' => str_contains($itemVal, $val),
                'equals' => $itemVal === $val,
                'starts_with' => str_starts_with($itemVal, $val),
                default => true,
            };
        });
    }

    $this->registrosFiltrados = $filtradas->values()->toArray();
    $this->pagina_actual = 1;
    $this->showFilter = false;
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1600px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Depósito Venta</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-6">

                {{-- Title --}}
                <h2 class="text-lg font-bold text-[#1f2937] mb-5">Depósito Venta</h2>

                {{-- Filter Section --}}
                <div class="flex flex-col lg:flex-row lg:items-end gap-5 mb-4">
                    <div class="flex flex-col w-full lg:w-52">
                        <label class="text-xs text-gray-500 font-medium mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="1Z">1Z - Zona 1</option>
                            <option value="2Z">2Z - Zona 2</option>
                        </select>
                    </div>

                    <div class="flex flex-col w-full lg:w-52">
                        <label class="text-xs text-gray-500 font-medium mb-1">Ruta</label>
                        <select wire:model.live="filtro_ruta" class="border-0 border-b border-gray-300 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="">Ruta</option>
                            @foreach($this->obtenerRutas() as $ruta)
                                <option value="{{ $ruta }}">{{ $ruta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col w-full lg:w-48">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha Inicial</label>
                        <input type="date" wire:model.live="fecha_inicio" class="border-0 border-b border-gray-300 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold" />
                    </div>

                    <div class="flex flex-col w-full lg:w-48">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha Final</label>
                        <input type="date" wire:model.live="fecha_fin" class="border-0 border-b border-gray-300 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold" />
                    </div>

                    <div class="flex items-end lg:ml-2">
                        <button wire:click="consultar" 
                                @disabled(empty($filtro_ruta))
                                class="text-sm font-bold px-6 py-2 rounded-md transition duration-150 {{ empty($filtro_ruta) ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-[#004f7c] hover:bg-[#003859] text-white' }}">
                            Consultar
                        </button>
                    </div>
                </div>

                @if(!empty($filtro_ruta))
                {{-- Table Toolbar --}}
                <div class="flex items-center justify-between mb-3 relative">
                    <div class="flex items-center gap-4 text-xs text-gray-500 font-medium">
                        {{-- Columnas --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-1 hover:text-[#003859] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                                Columnas
                            </button>
                            
                            <div x-show="open" style="display:none;" class="absolute top-full left-0 mt-1 w-64 bg-white border border-gray-200 shadow-xl rounded-lg z-50 p-2 max-h-96 overflow-y-auto">
                                <div class="text-[10px] font-semibold text-gray-500 mb-2 px-2 uppercase tracking-wider">Mostrar / Ocultar columnas</div>
                                @foreach([
                                    'id' => '#',
                                    'ruta' => 'Ruta',
                                    'fecha' => 'Fecha',
                                    'contado' => 'Contado',
                                    'credito' => 'Crédito',
                                    'num_preventas' => 'Número de Preventas',
                                    'total_preventa' => 'Total de Preventa',
                                    'entrega_contado' => 'Entrega contado',
                                    'entrega_credito' => 'Entrega crédito',
                                    'cobranza' => 'Cobranza',
                                    'cobranza_efectivo' => 'Cobranza en efectivo',
                                    'gastos_operativos' => 'Gastos Operativos',
                                    'devolucion_contado' => 'Devolución de contado',
                                    'devolucion_credito' => 'Devolución de crédito',
                                    'total' => 'Total',
                                    'total_cierre' => 'Total Cierre',
                                ] as $key => $label)
                                    <label class="flex items-center space-x-3 px-2 py-1.5 hover:bg-gray-50 cursor-pointer rounded transition">
                                        <input type="checkbox" wire:model.live="columnasVisibles.{{ $key }}" class="text-[#003859] rounded border-gray-300 focus:ring-[#003859] w-4 h-4" />
                                        <span class="text-xs text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Filtro --}}
                        <div class="relative">
                            <button wire:click="toggleFilter" class="flex items-center gap-1 hover:text-[#003859] transition {{ $showFilter ? 'text-[#003859]' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filtro
                            </button>

                            @if($showFilter)
                            <div class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-3 z-50 w-80">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] text-gray-500 uppercase font-bold">Hide filters</span>
                                    <button wire:click="toggleFilter" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <div>
                                        <label class="text-[10px] text-gray-500 font-medium">Column</label>
                                        <select wire:model="filter_column" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mt-1 focus:outline-none">
                                            <option value="#">#</option>
                                            <option value="Ruta">Ruta</option>
                                            <option value="Fecha">Fecha</option>
                                            <option value="Contado">Contado</option>
                                            <option value="Crédito">Crédito</option>
                                            <option value="Total Cierre">Total Cierre</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-gray-500 font-medium">Operator</label>
                                        <select wire:model="filter_operator" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mt-1 focus:outline-none">
                                            <option value="contains">contains</option>
                                            <option value="equals">equals</option>
                                            <option value="starts_with">starts with</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-gray-500 font-medium">Value</label>
                                        <input type="text" wire:model="filter_value" placeholder="Filter value" class="w-full border border-gray-300 rounded px-2 py-1 text-xs mt-1 focus:outline-none" />
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <button wire:click="consultar" class="bg-[#004f7c] hover:bg-[#003859] text-white text-xs font-bold px-4 py-1.5 rounded transition">
                                        Aplicar
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nuevo Cierre Button --}}
                    <button wire:click="abrirModal" class="bg-[#004f7c] hover:bg-[#003859] text-white text-xs font-bold px-4 py-2 rounded-md transition duration-150">
                        Nuevo Cierre
                    </button>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                @if($columnasVisibles['id']) <th class="px-4 py-3 text-center">#</th> @endif
                                @if($columnasVisibles['ruta']) <th class="px-4 py-3">Ruta</th> @endif
                                @if($columnasVisibles['fecha']) <th class="px-4 py-3">Fecha</th> @endif
                                @if($columnasVisibles['contado']) <th class="px-4 py-3 text-right">Contado</th> @endif
                                @if($columnasVisibles['credito']) <th class="px-4 py-3 text-right">Crédito</th> @endif
                                @if($columnasVisibles['num_preventas']) <th class="px-4 py-3 text-center">Número de Preventas</th> @endif
                                @if($columnasVisibles['total_preventa']) <th class="px-4 py-3 text-right">Total de Preventa</th> @endif
                                @if($columnasVisibles['entrega_contado']) <th class="px-4 py-3 text-right">Entrega contado</th> @endif
                                @if($columnasVisibles['entrega_credito']) <th class="px-4 py-3 text-right">Entrega crédito</th> @endif
                                @if($columnasVisibles['cobranza']) <th class="px-4 py-3 text-right">Cobranza</th> @endif
                                @if($columnasVisibles['cobranza_efectivo']) <th class="px-4 py-3 text-right">Cobranza en efectivo</th> @endif
                                @if($columnasVisibles['gastos_operativos']) <th class="px-4 py-3 text-right">Gastos Operativos</th> @endif
                                @if($columnasVisibles['devolucion_contado']) <th class="px-4 py-3 text-right">Devolución de contado</th> @endif
                                @if($columnasVisibles['devolucion_credito']) <th class="px-4 py-3 text-right">Devolución de crédito</th> @endif
                                @if($columnasVisibles['total']) <th class="px-4 py-3 text-right">Total</th> @endif
                                @if($columnasVisibles['total_cierre']) <th class="px-4 py-3 text-right">Total Cierre</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white text-xs sm:text-sm text-gray-700">
                            @forelse($this->registrosFiltrados as $registro)
                            <tr class="hover:bg-gray-50/30 transition duration-150">
                                @if($columnasVisibles['id']) <td class="px-4 py-3 text-center text-gray-500">{{ $registro['id'] }}</td> @endif
                                @if($columnasVisibles['ruta']) <td class="px-4 py-3 font-semibold text-[#003859]">{{ $registro['ruta'] }}</td> @endif
                                @if($columnasVisibles['fecha']) <td class="px-4 py-3 text-gray-500 font-medium">{{ \Carbon\Carbon::parse($registro['fecha'])->format('d/m/Y') }}</td> @endif
                                @if($columnasVisibles['contado']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['contado'], 2) }}</td> @endif
                                @if($columnasVisibles['credito']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['credito'], 2) }}</td> @endif
                                @if($columnasVisibles['num_preventas']) <td class="px-4 py-3 text-center text-gray-500">{{ $registro['num_preventas'] }}</td> @endif
                                @if($columnasVisibles['total_preventa']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['total_preventa'], 2) }}</td> @endif
                                @if($columnasVisibles['entrega_contado']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['entrega_contado'], 2) }}</td> @endif
                                @if($columnasVisibles['entrega_credito']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['entrega_credito'], 2) }}</td> @endif
                                @if($columnasVisibles['cobranza']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['cobranza'], 2) }}</td> @endif
                                @if($columnasVisibles['cobranza_efectivo']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['cobranza_efectivo'], 2) }}</td> @endif
                                @if($columnasVisibles['gastos_operativos']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['gastos_operativos'], 2) }}</td> @endif
                                @if($columnasVisibles['devolucion_contado']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['devolucion_contado'] ?? 0, 2) }}</td> @endif
                                @if($columnasVisibles['devolucion_credito']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['devolucion_credito'] ?? 0, 2) }}</td> @endif
                                @if($columnasVisibles['total']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['total'] ?? 0, 2) }}</td> @endif
                                @if($columnasVisibles['total_cierre']) <td class="px-4 py-3 text-right font-bold text-[#1f2937]">${{ number_format($registro['total_cierre'], 2) }}</td> @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count(array_filter($columnasVisibles)) }}" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    Sin Registros
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="flex items-center justify-between mt-4 px-1">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span>Filas por Página</span>
                        <select class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none">
                            <option>100</option>
                            <option>50</option>
                            <option>25</option>
                        </select>
                    </div>
                    <div class="text-xs text-gray-500 font-medium">
                        1–{{ count($this->registrosFiltrados) }} de {{ count($this->registrosFiltrados) }}
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1 text-gray-400 hover:text-gray-700" title="Primera página">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        </button>
                        <button class="p-1 text-gray-400 hover:text-gray-700" title="Página anterior">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="p-1 text-gray-400 hover:text-gray-700" title="Página siguiente">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <button class="p-1 text-gray-400 hover:text-gray-700" title="Última página">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Nuevo Cierre --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="cerrarModal"></div>

        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-white px-5 py-3 border-b border-gray-200 flex items-center gap-3 z-10">
                <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </button>
                <h3 class="text-base font-bold text-[#1f2937]">Nuevo Cierre</h3>
            </div>

            {{-- Body --}}
            <div class="px-5 py-4">
                {{-- Ruta --}}
                <div class="mb-3">
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Ruta</label>
                    <select wire:model.live="nueva_ruta" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#003859] bg-white">
                        <option value="3983 - RUTA01">3983 - RUTA01</option>
                        <option value="4682 - RUTA02">4682 - RUTA02</option>
                        <option value="4683 - RUTA03">4683 - RUTA03</option>
                        <option value="4684 - RUTA04">4684 - RUTA04</option>
                        <option value="4685 - RUTA05">4685 - RUTA05</option>
                    </select>
                </div>

                {{-- Fecha --}}
                <div class="mb-3">
                    <label class="text-xs text-gray-500 font-medium mb-1 block">Fecha</label>
                    <input type="date" wire:model.live="nueva_fecha" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#003859]" />
                </div>

                {{-- Total de Productos Solicitados --}}
                <div class="bg-gray-100 rounded px-3 py-2 mb-1">
                    <span class="text-xs text-gray-600 font-bold">Total de Productos</span>
                </div>
                <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                    <span>Solicitados</span>
                    <span class="text-gray-900">{{ $nueva_total_productos }}</span>
                </div>

                {{-- Fields List --}}
                <div class="space-y-0 divide-y divide-gray-100">
                    {{-- Preventas --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Preventas</span>
                        <span class="text-gray-900">{{ $nueva_preventas }}</span>
                    </div>

                    {{-- Total Preventa --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Total Preventa</span>
                        <span class="text-gray-900">${{ number_format($nueva_total_preventa, 2) }}</span>
                    </div>

                    {{-- Entrega contado --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Entrega contado</span>
                        <span class="text-gray-900">${{ number_format($nueva_entrega_contado, 2) }}</span>
                    </div>

                    {{-- Entrega crédito --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Entrega crédito</span>
                        <span class="text-gray-900">${{ number_format($nueva_entrega_credito, 2) }}</span>
                    </div>

                    {{-- Contado --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Contado</span>
                        <span class="text-gray-900">${{ number_format($nueva_contado, 2) }}</span>
                    </div>

                    {{-- Crédito --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Crédito</span>
                        <span class="text-gray-900">${{ number_format($nueva_credito, 2) }}</span>
                    </div>

                    {{-- Cobrado --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Cobrado</span>
                        <span class="text-gray-900">${{ number_format($nueva_cobrado, 2) }}</span>
                    </div>

                    {{-- Cobrado efectivo --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Cobrado efectivo</span>
                        <span class="text-gray-900">${{ number_format($nueva_cobrado_efectivo, 2) }}</span>
                    </div>

                    {{-- Devolución --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Devolución</span>
                        <span class="text-gray-900">-${{ number_format($nueva_devolucion, 2) }}</span>
                    </div>

                    {{-- Devolución de contado --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Devolución de contado</span>
                        <span class="text-gray-900">${{ number_format($nueva_devolucion_contado, 2) }}</span>
                    </div>

                    {{-- Devolución de crédito --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Devolución de crédito</span>
                        <span class="text-gray-900">${{ number_format($nueva_devolucion_credito, 2) }}</span>
                    </div>

                    {{-- Gasto Operativo --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Gasto Operativo</span>
                        <span class="text-gray-900">${{ number_format($nueva_gasto_operativo, 2) }}</span>
                    </div>

                    {{-- Total Cierre --}}
                    <div class="bg-gray-100 rounded px-3 py-2 flex items-center justify-between my-1">
                        <span class="text-xs text-gray-600 font-bold">Total Cierre</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($nueva_total_cierre, 2) }}</span>
                    </div>

                    {{-- Total efectivo --}}
                    <div class="flex items-center justify-between py-2">
                        <label class="text-xs text-gray-600 font-medium">Total efectivo</label>
                        <div class="flex items-center border border-gray-300 rounded w-32 focus-within:border-[#003859] focus-within:ring-1 focus-within:ring-[#003859]">
                            <span class="text-gray-500 px-2 text-sm">$</span>
                            <input type="number" wire:model.live="nueva_total_efectivo" class="w-full border-0 px-2 py-1 text-sm text-right focus:outline-none focus:ring-0" step="0.01" />
                        </div>
                    </div>

                    {{-- Faltante --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Faltante</span>
                        <span class="text-red-500 font-semibold">${{ number_format($nueva_faltante, 2) }}</span>
                    </div>

                    {{-- Sobrante --}}
                    <div class="flex items-center justify-between py-2 text-xs font-medium text-gray-600">
                        <span>Sobrante</span>
                        <span class="text-emerald-600 font-semibold">${{ number_format($nueva_sobrante, 2) }}</span>
                    </div>

                    {{-- Comentario --}}
                    <div class="pt-3">
                        <label class="text-xs text-gray-600 font-medium mb-1 block">Comentario</label>
                        <input type="text" wire:model="nueva_comentario" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-[#003859]" />
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-white px-5 py-3 border-t border-gray-200 flex items-center justify-end gap-3">
                <button wire:click="cerrarModal" class="text-[#004f7c] hover:text-[#003859] text-sm font-bold transition">
                    Cerrar
                </button>
                <button wire:click="guardarCierre" class="bg-[#004f7c] hover:bg-[#003859] text-white text-sm font-bold px-5 py-2 rounded transition duration-150">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
