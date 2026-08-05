<?php

use function Livewire\Volt\{state, layout, mount};
use App\Models\Zone;

layout('layouts.app');

state([
    // Filters and Data catalogs
    'zonas' => [],
    'selected_zona' => '',
    'selected_ruta' => '',
    'selected_linea' => '',
    'ordenar_por' => 'Orden de línea descendente',
    'search' => '',
    
    // UI states
    'consultado' => false,
    'registrosFiltrados' => [],
    'filas_por_pagina' => 100,

    // Column Visibilities
    'col_clave' => true,
    'col_descripcion' => true,
    'col_existencia_almacen' => true,
    'col_existencia_movil' => true,
    'col_cantidad' => true,

    // Static product list
    'productos' => [
        ['clave' => 'AL-200', 'descripcion' => 'Galletas de Chocolate 120g', 'linea' => 'ALIMENTOS', 'existencia_almacen' => 850, 'existencia_movil' => 45, 'cantidad' => 60],
        ['clave' => 'AL-201', 'descripcion' => 'Pan Integral 680g', 'linea' => 'ALIMENTOS', 'existencia_almacen' => 420, 'existencia_movil' => 20, 'cantidad' => 30],
        ['clave' => 'AL-202', 'descripcion' => 'Mermelada de Fresa 270g', 'linea' => 'ALIMENTOS', 'existencia_almacen' => 610, 'existencia_movil' => 15, 'cantidad' => 20],
        ['clave' => 'BEB-100', 'descripcion' => 'Bebida Energética 500ml', 'linea' => 'BEBIDAS', 'existencia_almacen' => 1500, 'existencia_movil' => 100, 'cantidad' => 120],
        ['clave' => 'BEB-101', 'descripcion' => 'Agua Purificada 1L', 'linea' => 'BEBIDAS', 'existencia_almacen' => 3200, 'existencia_movil' => 250, 'cantidad' => 300],
        ['clave' => 'BOT-300', 'descripcion' => 'Papas Fritas Clásicas 50g', 'linea' => 'BOTANAS', 'existencia_almacen' => 2100, 'existencia_movil' => 180, 'cantidad' => 200],
        ['clave' => 'BOT-301', 'descripcion' => 'Cacahuate Japonés 100g', 'linea' => 'BOTANAS', 'existencia_almacen' => 5000, 'existencia_movil' => 320, 'cantidad' => 400],
        ['clave' => 'ABA-400', 'descripcion' => 'Frijoles Refritos 400g', 'linea' => 'ABARROTES', 'existencia_almacen' => 1200, 'existencia_movil' => 90, 'cantidad' => 100],
    ],

    // Available categories
    'lineas' => ['ALIMENTOS', 'BEBIDAS', 'BOTANAS', 'ABARROTES'],
    'ordenar_opciones' => [
        'Orden de línea descendente',
        'Orden de línea ascendente',
        'Clave',
        'Descripción'
    ],
]);

mount(function() {
    try {
        $dbZones = Zone::where('id', '!=', '99-PRUEBA')->orderBy('id', 'asc')->get()->toArray();
        if (!empty($dbZones)) {
            $this->zonas = $dbZones;
        } else {
            $this->zonas = [
                ['id' => '1Z', 'name' => '1Z - Zona 1'],
                ['id' => '2Z', 'name' => '2Z - Zona 2'],
                ['id' => '3Z', 'name' => '3Z - Zona 3'],
            ];
        }
    } catch (\Exception $e) {
        $this->zonas = [
            ['id' => '1Z', 'name' => '1Z - Zona 1'],
            ['id' => '2Z', 'name' => '2Z - Zona 2'],
            ['id' => '3Z', 'name' => '3Z - Zona 3'],
        ];
    }

    $this->selected_zona = $this->zonas[0]['id'] ?? '1Z';
    
    // Resolve route
    $rutas = $this->obtenerRutasDeZona();
    $this->selected_ruta = $rutas[0]['id'] ?? '3983';
    
    $this->registrosFiltrados = [];
    $this->consultado = false;
});

// Helper functions
$obtenerRutasDeZona = function() {
    $z = strtolower($this->selected_zona);
    if (str_contains($z, '1z') || $z === '1' || str_contains($z, '99-prueba')) {
        return [
            ['id' => '3983', 'name' => '3983 - RUTA01'],
            ['id' => '4682', 'name' => '4682 - RUTA02'],
            ['id' => '4683', 'name' => '4683 - RUTA03'],
        ];
    } elseif (str_contains($z, '2z') || $z === '2') {
        return [
            ['id' => '4684', 'name' => '4684 - RUTA04'],
            ['id' => '4685', 'name' => '4685 - RUTA05'],
        ];
    } elseif (str_contains($z, '3z') || $z === '3') {
        return [
            ['id' => '4686', 'name' => '4686 - RUTA06'],
        ];
    }
    return [
        ['id' => '3983', 'name' => '3983 - RUTA01'],
    ];
};

$aplicarFiltros = function() {
    if (!$this->consultado) {
        $this->registrosFiltrados = [];
        return;
    }

    $data = collect($this->productos);

    // 1. Line filter
    if (!empty($this->selected_linea)) {
        $data = $data->where('linea', $this->selected_linea);
    }

    // 2. Search query filter
    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $data = $data->filter(fn($p) => 
            str_contains(strtolower($p['clave']), $q) || 
            str_contains(strtolower($p['descripcion']), $q)
        );
    }

    // 3. Sorting logic
    if ($this->ordenar_por === 'Clave') {
        $data = $data->sortBy('clave');
    } elseif ($this->ordenar_por === 'Descripción') {
        $data = $data->sortBy('descripcion');
    } elseif ($this->ordenar_por === 'Orden de línea ascendente') {
        $data = $data->sortBy('linea');
    } else {
        // Orden de línea descendente
        $data = $data->sortByDesc('linea');
    }

    $this->registrosFiltrados = $data->values()->toArray();
};

$updatedSelectedZona = function() {
    $rutas = $this->obtenerRutasDeZona();
    $this->selected_ruta = $rutas[0]['id'] ?? '';
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSelectedRuta = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSelectedLinea = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedOrdenarPor = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSearch = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$clearLinea = function() {
    $this->selected_linea = '';
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$consultar = function() {
    $this->consultado = true;
    $this->aplicarFiltros();
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
                <span class="text-[#003859] dark:text-orange-400 font-bold">Carga iMóvil Grid</span>
            </div>

            {{-- Main Filter Panel --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-6">
                <h2 class="text-base font-bold text-[#1f2937] dark:text-white mb-5 select-none">Cargar a Inventario Móvil</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                    
                    {{-- Zona Select --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Zona</label>
                        <select wire:model.live="selected_zona" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            @foreach($zonas as $z)
                                <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ruta Select --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Ruta</label>
                        <select wire:model.live="selected_ruta" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            @foreach($this->obtenerRutasDeZona() as $r)
                                <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Linea Select with Reset Tag Pill --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Línea</label>
                        @if ($selected_linea)
                            <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 py-1.5 w-full h-8 cursor-pointer select-none text-gray-750 dark:text-gray-200 font-semibold text-sm" wire:click="clearLinea">
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-250 text-xs px-2.5 py-0.5 rounded border border-gray-200 dark:border-gray-650 flex items-center gap-1 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                    {{ $selected_linea }}
                                    <span class="text-gray-400 font-bold">×</span>
                                </span>
                                <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        @else
                            <select wire:model.live="selected_linea" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full h-8">
                                <option value="">Todas</option>
                                @foreach($lineas as $l)
                                    <option value="{{ $l }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Ordenar Por Select --}}
                    <div class="flex flex-col w-full">
                        <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Ordenar por</label>
                        <select wire:model.live="ordenar_por" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-orange-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                            @foreach($ordenar_opciones as $op)
                                <option value="{{ $op }}">{{ $op }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Consultar Button --}}
                    <div>
                        <button wire:click="consultar" class="w-full bg-[#003859] hover:bg-[#002b45] text-white py-2 px-6 rounded text-sm font-semibold transition duration-150 cursor-pointer shadow-md active:scale-98">
                            Consultar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Grid Table Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 overflow-hidden">
                
                {{-- Table Controls Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-750 bg-gray-50/50 dark:bg-gray-900/10">
                    
                    {{-- Left side: Column selector --}}
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-xs font-semibold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 transition duration-150 cursor-pointer shadow-sm">
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                            Columnas
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute left-0 mt-2 w-56 rounded shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-750 z-20 py-2" style="display: none;">
                            <div class="px-4 py-1 text-xs font-bold text-gray-400 dark:text-gray-550 uppercase tracking-wider border-b border-gray-100 dark:border-gray-750 mb-2">Mostrar Columnas</div>
                            <div class="space-y-2 px-4 py-1">
                                <label class="flex items-center gap-2 text-xs text-gray-750 dark:text-gray-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="col_clave" class="rounded border-gray-300 dark:border-gray-600 text-[#003859] focus:ring-[#003859] w-3.5 h-3.5" />
                                    Clave
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-750 dark:text-gray-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="col_descripcion" class="rounded border-gray-300 dark:border-gray-600 text-[#003859] focus:ring-[#003859] w-3.5 h-3.5" />
                                    Descripción
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-750 dark:text-gray-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="col_existencia_almacen" class="rounded border-gray-300 dark:border-gray-600 text-[#003859] focus:ring-[#003859] w-3.5 h-3.5" />
                                    Existencia en Almacén
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-750 dark:text-gray-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="col_existencia_movil" class="rounded border-gray-300 dark:border-gray-600 text-[#003859] focus:ring-[#003859] w-3.5 h-3.5" />
                                    Existencia en Inventario Móvil
                                </label>
                                <label class="flex items-center gap-2 text-xs text-gray-750 dark:text-gray-200 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="col_cantidad" class="rounded border-gray-300 dark:border-gray-600 text-[#003859] focus:ring-[#003859] w-3.5 h-3.5" />
                                    Cantidad
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Right side: Search --}}
                    <div class="relative flex items-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 px-2.5 py-1.5 shadow-sm min-w-[220px]">
                        <svg class="w-3.5 h-3.5 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live="search" placeholder="Buscar..." class="border-none bg-transparent outline-none text-xs p-0 text-gray-750 dark:text-gray-200 focus:ring-0 placeholder-gray-400 dark:placeholder-gray-500 w-full" />
                    </div>
                </div>

                {{-- Table Area --}}
                <div class="overflow-x-auto w-full">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-750 table-fixed">
                        
                        {{-- Table Headers --}}
                        <thead class="bg-gray-50/70 dark:bg-gray-900/20 text-gray-400 dark:text-gray-550 text-[11px] font-bold uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="w-12 text-center py-3 select-none">#</th>
                                
                                @if($col_clave)
                                    <th scope="col" class="w-32 text-left py-3 px-4 select-none">Clave</th>
                                @endif
                                
                                @if($col_descripcion)
                                    <th scope="col" class="text-left py-3 px-4 select-none">Descripción</th>
                                @endif
                                
                                @if($col_existencia_almacen)
                                    <th scope="col" class="w-48 text-center py-3 px-4 select-none">Existencia en Almacén</th>
                                @endif
                                
                                @if($col_existencia_movil)
                                    <th scope="col" class="w-56 text-center py-3 px-4 select-none">Existencia en Inventario Móvil</th>
                                @endif
                                
                                @if($col_cantidad)
                                    <th scope="col" class="w-36 text-right py-3 px-6 select-none">Cantidad</th>
                                @endif
                            </tr>
                        </thead>

                        {{-- Table Body --}}
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-750">
                            @forelse($registrosFiltrados as $index => $row)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/5 transition-colors duration-100">
                                    <td class="text-center py-3.5 text-xs text-gray-400 dark:text-gray-500 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    
                                    @if($col_clave)
                                        <td class="py-3.5 px-4 text-xs font-mono font-bold text-gray-700 dark:text-gray-300">
                                            {{ $row['clave'] }}
                                        </td>
                                    @endif

                                    @if($col_descripcion)
                                        <td class="py-3.5 px-4 text-sm text-gray-800 dark:text-gray-250 truncate">
                                            {{ $row['descripcion'] }}
                                        </td>
                                    @endif

                                    @if($col_existencia_almacen)
                                        <td class="py-3.5 px-4 text-center text-sm font-semibold text-gray-600 dark:text-gray-400">
                                            {{ number_format($row['existencia_almacen']) }}
                                        </td>
                                    @endif

                                    @if($col_existencia_movil)
                                        <td class="py-3.5 px-4 text-center text-sm font-semibold text-gray-600 dark:text-gray-400">
                                            {{ number_format($row['existencia_movil']) }}
                                        </td>
                                    @endif

                                    @if($col_cantidad)
                                        <td class="py-3.5 px-6 text-right text-sm font-bold text-[#003859] dark:text-orange-400 pr-6">
                                            {{ number_format($row['cantidad']) }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-sm font-semibold text-gray-400 dark:text-gray-500 select-none">Sin Registros</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer / Pagination Controls --}}
                <div class="flex flex-col sm:flex-row items-center justify-end gap-6 px-6 py-4 border-t border-gray-100 dark:border-gray-750 bg-gray-50/30 dark:bg-gray-900/5 text-xs text-gray-500 dark:text-gray-450 select-none">
                    
                    {{-- Row count selector --}}
                    <div class="flex items-center gap-2">
                        <span>Filas por Página</span>
                        <select wire:model.live="filas_por_pagina" class="border-0 border-b border-gray-200 dark:border-gray-650 bg-transparent text-xs font-semibold py-0.5 px-1 pr-6 cursor-pointer focus:outline-none focus:ring-0 text-gray-700 dark:text-gray-300">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>

                    {{-- Item Indicator --}}
                    <div>
                        @if ($consultado && count($registrosFiltrados) > 0)
                            1-{{ count($registrosFiltrados) }} of {{ count($registrosFiltrados) }}
                        @else
                            0-0 of 0
                        @endif
                    </div>

                    {{-- Navigation Chevron Buttons --}}
                    <div class="flex items-center gap-1">
                        {{-- First Page --}}
                        <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-400 dark:text-gray-550 cursor-not-allowed" disabled>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414zm-6 0a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        
                        {{-- Previous Page --}}
                        <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-400 dark:text-gray-550 cursor-not-allowed" disabled>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        {{-- Next Page --}}
                        <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-400 dark:text-gray-550 cursor-not-allowed" disabled>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        {{-- Last Page --}}
                        <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-400 dark:text-gray-550 cursor-not-allowed" disabled>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414zm6 0a1 1 0 011.414 0l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L14.586 10l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Copyright footer matching current designs --}}
            <div class="mt-8 text-center text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-widest select-none">
                Copyright © JB VEMOBILE SA DE CV 2026.
            </div>

        </div>
    </div>
</div>
