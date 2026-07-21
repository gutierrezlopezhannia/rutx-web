<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockProductos = [
    ['ranking' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Bebida Energética 500ml', 'linea' => 'Bebidas', 'unidades_vendidas' => 1850, 'monto_total' => 46250.0],
    ['ranking' => 2, 'codigo' => 'PROD-005', 'descripcion' => 'Refresco de Cola 600ml', 'linea' => 'Bebidas', 'unidades_vendidas' => 1620, 'monto_total' => 29160.0],
    ['ranking' => 3, 'codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'linea' => 'Bebidas', 'unidades_vendidas' => 1400, 'monto_total' => 16800.0],
    ['ranking' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Papas Fritas Clásicas 50g', 'linea' => 'Alimentos', 'unidades_vendidas' => 980, 'monto_total' => 14700.0],
    ['ranking' => 5, 'codigo' => 'PROD-003', 'descripcion' => 'Galletas de Chocolate 120g', 'linea' => 'Alimentos', 'unidades_vendidas' => 760, 'monto_total' => 14060.0],
    ['ranking' => 6, 'codigo' => 'PROD-007', 'descripcion' => 'Jugo de Naranja 500ml', 'linea' => 'Bebidas', 'unidades_vendidas' => 640, 'monto_total' => 12800.0],
    ['ranking' => 7, 'codigo' => 'PROD-009', 'descripcion' => 'Chicles Menta x10', 'linea' => 'Dulcería', 'unidades_vendidas' => 590, 'monto_total' => 2950.0],
    ['ranking' => 8, 'codigo' => 'PROD-006', 'descripcion' => 'Cacahuates Salados 100g', 'linea' => 'Alimentos', 'unidades_vendidas' => 410, 'monto_total' => 6150.0],
    ['ranking' => 9, 'codigo' => 'PROD-010', 'descripcion' => 'Paracetamol 500mg', 'linea' => 'Farmacia', 'unidades_vendidas' => 320, 'monto_total' => 4800.0],
];

$totalGeneral = array_sum(array_column($mockProductos, 'monto_total'));

state([
    'productos' => $mockProductos,
    'productosFiltrados' => $mockProductos,
    'filtro_zona' => 'todos',
    'filtro_ruta' => 'todos',
    'filtro_linea' => 'todos',
    'filtro_linea_familia' => 'todos',
    'filtro_movimiento' => 'Venta',
    'ordenar_por' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
    'agrupar_por' => 'Producto',
    'search' => '',
    'totalGeneral' => $totalGeneral,
]);

$aplicarFiltros = function () {
    $filtradas = collect($this->productos);

    if ($this->filtro_linea !== 'todos') {
        $filtradas = $filtradas->where('linea', $this->filtro_linea);
    }

    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $filtradas = $filtradas->filter(function ($item) use ($searchQuery) {
            return str_contains(strtolower($item['descripcion']), $searchQuery) || str_contains(strtolower($item['codigo']), $searchQuery);
        });
    }

    $this->productosFiltrados = $filtradas->values()->toArray();
};

$updatedFiltroLinea = function () {
    $this->aplicarFiltros();
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};

$consultar = function () {
    $this->aplicarFiltros();
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Producto</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Reporte de Productos</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">

                <h2 class="text-lg font-bold text-[#003859] mb-4">Reportes de Productos</h2>

                {{-- Filter Section --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    {{-- Zona --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Zona</label>
                        <select wire:model.live="filtro_zona"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">1Z - Zona 1</option>
                            <option value="Zona 2">2Z - Zona 2</option>
                        </select>
                    </div>

                    {{-- Ruta --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Ruta</label>
                        <select wire:model.live="filtro_ruta"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas</option>
                            <option value="Ruta 1">Ruta 1</option>
                            <option value="Ruta 2">Ruta 2</option>
                            <option value="Ruta 3">Ruta 3</option>
                        </select>
                    </div>

                    {{-- Línea --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Línea</label>
                        <select wire:model.live="filtro_linea"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas</option>
                            <option value="Alimentos">Alimentos</option>
                            <option value="Bebidas">Bebidas</option>
                            <option value="Dulcería">Dulcería</option>
                            <option value="Farmacia">Farmacia</option>
                        </select>
                    </div>

                    {{-- Línea Familia --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Línea Familia</label>
                        <select wire:model.live="filtro_linea_familia"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
                    {{-- Movimiento --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Movimiento</label>
                        <select wire:model.live="filtro_movimiento"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="Venta">Venta</option>
                            <option value="Preventa">Preventa</option>
                            <option value="Entrega">Entrega</option>
                            <option value="Prevencion-Entrega">Prevención - Entrega</option>
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Fecha Inicial</label>
                        <label
                            class="flex items-center border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 cursor-pointer"
                            @click.prevent="$el.querySelector('input').showPicker()">
                            <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input type="date" wire:model.live="fecha_inicio"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm [&::-webkit-calendar-picker-indicator]:hidden" />
                        </label>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Fecha Final</label>
                        <label
                            class="flex items-center border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 cursor-pointer"
                            @click.prevent="$el.querySelector('input').showPicker()">
                            <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <input type="date" wire:model.live="fecha_fin"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm [&::-webkit-calendar-picker-indicator]:hidden" />
                        </label>
                    </div>

                    {{-- Agrupar por --}}
                    <div class="relative">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Agrupar por</label>
                        <select wire:model.live="agrupar_por"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="Producto">Producto</option>
                            <option value="Línea">Línea</option>
                            <option value="Vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-center gap-3 mb-5">
                    {{-- Search --}}
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar por código o descripción..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400" />
                    </div>

                    {{-- Consultar --}}
                    <button wire:click="consultar"
                        class="px-6 py-2 bg-[#003859] text-white rounded-lg text-sm font-semibold hover:bg-[#004f7c] transition duration-150 cursor-pointer">
                        Consultar
                    </button>

                    {{-- Exportar --}}
                    <button
                        class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 flex items-center justify-center gap-2 cursor-pointer transition duration-150 bg-white">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar
                    </button>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Código</th>
                                <th class="px-6 py-4">Descripción</th>
                                <th class="px-6 py-4">Línea</th>
                                <th class="px-6 py-4 text-right">Unidades Vendidas</th>
                                <th class="px-6 py-4 text-right">Monto Total</th>
                                <th class="px-6 py-4 text-right">% del Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white text-xs sm:text-sm text-gray-700">
                            @forelse($this->productosFiltrados as $item)
                                <tr class="hover:bg-gray-50/30 transition duration-150">
                                    <td class="px-6 py-4 font-bold text-[#003859]">#{{ $item['ranking'] }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-400 font-medium">
                                        {{ $item['codigo'] }}</td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $item['descripcion'] }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">{{ $item['linea'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-700">
                                        {{ number_format($item['unidades_vendidas']) }} pz</td>
                                    <td class="px-6 py-4 text-right font-bold text-[#003859]">
                                        ${{ number_format($item['monto_total'], 2) }}</td>
                                    <td class="px-6 py-4 text-right text-gray-500 font-medium">
                                        {{ $totalGeneral > 0 ? number_format(($item['monto_total'] / $totalGeneral) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        No se encontraron productos.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
