<?php

use function Livewire\Volt\{state, layout};
use Database\Factories\ClientesPendientesFactory;

layout('layouts.app');

$factory = new ClientesPendientesFactory();
$mockClientesPendientes = $factory->make(15);

state([
    'clientesPendientes' => $mockClientesPendientes,
    'clientesFiltrados' => $mockClientesPendientes,
    'filtro_ruta' => 'todos',
    'filtro_vendedor' => 'todos',
    'search' => '',
    'fecha_inicio' => '',
    'fecha_fin' => '',
]);

$aplicarFiltros = function () {
    $filtrados = collect($this->clientesPendientes);

    if ($this->filtro_ruta !== 'todos') {
        $filtrados = $filtrados->where('ruta', $this->filtro_ruta);
    }

    if ($this->filtro_vendedor !== 'todos') {
        $filtrados = $filtrados->where('vendedor', $this->filtro_vendedor);
    }

    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtrados = $filtrados->filter(fn($item) => str_contains(strtolower($item['cliente']), $q) || str_contains(strtolower($item['vendedor']), $q));
    }

    if (!empty($this->fecha_inicio)) {
        try {
            $inicio = \Carbon\Carbon::parse($this->fecha_inicio)->startOfDay();
            $filtrados = $filtrados->filter(fn($item) => \Carbon\Carbon::parse($item['fecha']) >= $inicio);
        } catch (\Exception $e) {
        }
    }

    if (!empty($this->fecha_fin)) {
        try {
            $fin = \Carbon\Carbon::parse($this->fecha_fin)->endOfDay();
            $filtrados = $filtrados->filter(fn($item) => \Carbon\Carbon::parse($item['fecha']) <= $fin);
        } catch (\Exception $e) {
        }
    }

    $this->clientesFiltrados = $filtrados->values()->toArray();
};

$updatedFiltroRuta = function () {
    $this->aplicarFiltros();
};
$updatedFiltroVendedor = function () {
    $this->aplicarFiltros();
};
$updatedSearch = function () {
    $this->aplicarFiltros();
};
$updatedFechaInicio = function () {
    $this->aplicarFiltros();
};
$updatedFechaFin = function () {
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
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Clientes Pendientes</span>
            </div>

            {{-- Main Container --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">

                {{-- Filtros --}}
                <div class="flex flex-col lg:flex-row lg:items-center gap-3 mb-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap items-center gap-3 flex-1">

                        {{-- Fechas --}}
                        <div class="flex items-center gap-2 w-full sm:col-span-2 lg:w-auto">
                            <label
                                class="flex items-center border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 flex-1 lg:w-32 cursor-pointer"
                                @click.prevent="$el.querySelector('input').showPicker()">
                                <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" wire:model.live="fecha_inicio"
                                    class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-xs sm:text-sm [&::-webkit-calendar-picker-indicator]:hidden" />
                            </label>

                            <span class="text-gray-400 font-bold shrink-0">-</span>

                            <label
                                class="flex items-center border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 flex-1 lg:w-32 cursor-pointer"
                                @click.prevent="$el.querySelector('input').showPicker()">
                                <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" wire:model.live="fecha_fin"
                                    class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-400 font-semibold text-xs sm:text-sm [&::-webkit-calendar-picker-indicator]:hidden" />
                            </label>
                        </div>

                        {{-- Ruta --}}
                        <div class="relative w-full lg:w-32">
                            <select wire:model.live="filtro_ruta"
                                class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                                <option value="todos">Ruta</option>
                                <option value="Ruta 1">Ruta 1</option>
                                <option value="Ruta 2">Ruta 2</option>
                                <option value="Ruta 3">Ruta 3</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Vendedor --}}
                        <div class="relative w-full lg:w-40">
                            <select wire:model.live="filtro_vendedor"
                                class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                                <option value="todos">Vendedor</option>
                                <option value="Ana María">Ana María</option>
                                <option value="Carlos Díaz">Carlos Díaz</option>
                                <option value="Jorge Pérez">Jorge Pérez</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Buscar --}}
                        <div class="relative w-full sm:col-span-2 lg:flex-1 lg:min-w-[200px]">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" wire:model.live="search" placeholder="Buscar..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400" />
                        </div>
                    </div>

                    {{-- Exportar --}}
                    <div class="w-full lg:w-auto shrink-0 flex">
                        <button
                            class="w-full lg:w-auto px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-50 flex items-center justify-center gap-2 cursor-pointer transition duration-150 bg-white">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar
                        </button>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Ruta</th>
                                <th class="px-6 py-4">Vendedor</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4">Orden</th>
                                <th class="px-6 py-4">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white text-xs sm:text-sm text-gray-700">
                            @forelse($this->clientesFiltrados as $item)
                                <tr class="hover:bg-gray-50/30 transition duration-150">
                                    <td class="px-6 py-4 text-[#003859] font-medium">{{ $item['ruta'] }}</td>
                                    <td class="px-6 py-4 text-gray-900 font-medium">{{ $item['vendedor'] }}</td>
                                    <td class="px-6 py-4 text-[#003859] font-medium">{{ $item['cliente'] }}</td>
                                    <td class="px-6 py-4 text-gray-700 font-medium">{{ $item['orden'] }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                            {{ $item['estado'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                                        No se encontraron clientes pendientes.
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
