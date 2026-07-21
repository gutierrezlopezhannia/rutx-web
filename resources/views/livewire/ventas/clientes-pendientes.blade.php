<?php

use function Livewire\Volt\{state, layout};
use Database\Factories\ClientesPendientesFactory;

layout('layouts.app');

$factory = new ClientesPendientesFactory();
$mockClientesPendientes = $factory->make(15);

state([
    'clientesPendientes' => $mockClientesPendientes,
    'clientesFiltrados'  => $mockClientesPendientes,

    'filtro_zona'        => '1Z - Zona 1',
    'filtro_ruta'        => '3983 - RUTA01',
    'filtro_tipo_agenda' => 'Agenda General',
    'filtro_estatus'     => 'Activo',
    'filtro_borrado'     => 'No',

    'search' => '',
    'fecha'  => now()->format('Y-m-d'),

    // Columnas (para el dropdown de Ver Columnas)
    'col_cliente'   => true,
    'col_direccion' => true,
    'col_estatus'   => true,
    'col_borrado'   => true,
]);

$aplicarFiltros = function () {
    $filtrados = collect($this->clientesPendientes);

    if ($this->filtro_zona !== 'todos') {
        $filtrados = $filtrados->where('zona', $this->filtro_zona);
    }

    if ($this->filtro_ruta !== 'todos') {
        $filtrados = $filtrados->where('ruta', $this->filtro_ruta);
    }

    if ($this->filtro_tipo_agenda !== 'todos') {
        $filtrados = $filtrados->where('tipo_agenda', $this->filtro_tipo_agenda);
    }

    if ($this->filtro_estatus !== 'todos') {
        $filtrados = $filtrados->where('estatus', $this->filtro_estatus);
    }

    if ($this->filtro_borrado !== 'todos') {
        $filtrados = $filtrados->where('borrado', $this->filtro_borrado);
    }

    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtrados = $filtrados->filter(fn($item) => 
            str_contains(strtolower($item['cliente']), $q) || 
            str_contains(strtolower($item['direccion']), $q)
        );
    }

    if (!empty($this->fecha)) {
        try {
            $f = \Carbon\Carbon::parse($this->fecha)->startOfDay();
            $filtrados = $filtrados->filter(fn($item) => \Carbon\Carbon::parse($item['fecha'])->startOfDay()->eq($f));
        } catch (\Exception $e) {
        }
    }

    $this->clientesFiltrados = $filtrados->values()->toArray();
};

$updatedFiltroZona         = function () { $this->aplicarFiltros(); };
$updatedFiltroRuta         = function () { $this->aplicarFiltros(); };
$updatedFiltroTipoAgenda   = function () { $this->aplicarFiltros(); };
$updatedFiltroEstatus      = function () { $this->aplicarFiltros(); };
$updatedFiltroBorrado      = function () { $this->aplicarFiltros(); };
$updatedFecha              = function () { $this->aplicarFiltros(); };
$updatedSearch             = function () { $this->aplicarFiltros(); };
$actualizar                = function () { $this->aplicarFiltros(); };

$clearSearch = function () {
    $this->search = '';
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

            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">
                
                {{-- Título --}}
                <div class="flex items-center gap-2 mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Clientes Pendientes</h2>
                    <button class="text-gray-400 hover:text-gray-600 transition flex items-center justify-center w-5 h-5 rounded-full bg-gray-200" title="Información">
                        <span class="text-xs font-bold font-serif">i</span>
                    </button>
                </div>

                {{-- Filtros (6 en una línea) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap items-center gap-3 mb-4">
                    {{-- Zona --}}
                    <div class="relative w-full lg:w-40">
                        <select wire:model.live="filtro_zona"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Zona</option>
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Ruta --}}
                    <div class="relative w-full lg:w-44">
                        <select wire:model.live="filtro_ruta"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Ruta</option>
                            <option value="3983 - RUTA01">3983 - RUTA01</option>
                            <option value="3984 - RUTA02">3984 - RUTA02</option>
                            <option value="3985 - RUTA03">3985 - RUTA03</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Fecha --}}
                    <div class="relative w-full lg:w-44">
                        <label class="flex items-center border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 w-full cursor-pointer"
                            @click.prevent="$el.querySelector('input').showPicker()">
                            <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" wire:model.live="fecha"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-xs sm:text-sm [&::-webkit-calendar-picker-indicator]:hidden"/>
                        </label>
                    </div>

                    {{-- Tipo de Agenda --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="filtro_tipo_agenda"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Tipo Agenda</option>
                            <option value="Agenda General">Agenda General</option>
                            <option value="Agenda de Entrega">Agenda de Entrega</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Estatus --}}
                    <div class="relative w-full lg:w-36">
                        <select wire:model.live="filtro_estatus"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Estatus</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    {{-- Borrado --}}
                    <div class="relative w-full lg:w-32">
                        <select wire:model.live="filtro_borrado"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Borrado</option>
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Boton Consultar --}}
                <div class="mb-5">
                    <button wire:click="aplicarFiltros"
                        class="px-5 py-2 bg-[#003859] text-white text-sm font-semibold rounded-lg hover:bg-[#004f7c] transition shadow-sm">
                        Consultar
                    </button>
                </div>

                {{-- Sub-toolbar right aligned (Buscar, Columnas, Exportar, Actualizar) --}}
                <div class="flex justify-end items-center gap-3 mb-4" x-data="{ showColumnas: false, showExportar: false }">
                    
                    {{-- Buscar --}}
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar..."
                            class="w-full pl-9 pr-9 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400"/>
                        
                        @if(!empty($search))
                            <button wire:click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        @endif
                    </div>

                    {{-- Columnas --}}
                    <div class="relative">
                        <button @click="showColumnas = !showColumnas; showExportar = false" title="Ver Columnas"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z"/>
                            </svg>
                        </button>
                        <div x-show="showColumnas" @click.outside="showColumnas = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-48">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Columnas</p>
                            @foreach([
                                ['col_cliente', 'Cliente'],
                                ['col_direccion', 'Dirección'],
                                ['col_estatus', 'Estatus'],
                                ['col_borrado', 'Borrado']
                            ] as [$field, $label])
                                <label class="flex items-center gap-2 py-1 px-1 rounded cursor-pointer hover:bg-gray-50 text-sm text-gray-700">
                                    <input type="checkbox" wire:model.live="{{ $field }}"
                                        class="w-4 h-4 text-[#003859] rounded border-gray-300 focus:ring-[#003859]"/>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Exportar --}}
                    <div class="relative">
                        <button @click="showExportar = !showExportar; showColumnas = false" title="Exportar"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                        <div x-show="showExportar" @click.outside="showExportar = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg py-1 w-40">
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a CSV</button>
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a PDF</button>
                        </div>
                    </div>

                    {{-- Actualizar --}}
                    <button wire:click="actualizar" title="Actualizar" 
                        class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                {{-- Tabla clara basada en la captura --}}
                <div class="overflow-x-auto border border-gray-200/80 rounded-lg shadow-sm">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-700 font-bold tracking-wide">
                                @if($col_cliente)   <th class="px-6 py-4">Cliente</th> @endif
                                @if($col_direccion) <th class="px-6 py-4">Dirección</th> @endif
                                @if($col_estatus)   <th class="px-6 py-4 text-center">Estatus</th> @endif
                                @if($col_borrado)   <th class="px-6 py-4 text-center">Borrado</th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                            @forelse($clientesFiltrados as $item)
                                <tr class="hover:bg-gray-50/40 transition duration-150">
                                    @if($col_cliente)   <td class="px-6 py-4 font-semibold text-gray-900">{{ $item['cliente'] }}</td> @endif
                                    @if($col_direccion) <td class="px-6 py-4">{{ $item['direccion'] }}</td> @endif
                                    @if($col_estatus)   
                                        <td class="px-6 py-4 text-center">
                                            @if($item['estatus'] === 'Activo')
                                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                    {{ $item['estatus'] }}
                                                </span>
                                            @else
                                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                    {{ $item['estatus'] }}
                                                </span>
                                            @endif
                                        </td> 
                                    @endif
                                    @if($col_borrado)   
                                        <td class="px-6 py-4 text-center text-gray-500 font-medium">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                                {{ $item['borrado'] }}
                                            </span>
                                        </td> 
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-medium bg-white">
                                        No se encontraron registros para mostrar.
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
