<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockUnidades = [['clave' => 'U001', 'nombre' => 'CAMIONETA-01', 'ruta' => '3983 - RUTA01', 'zona' => '1Z - Zona 1', 'tipo_vehiculo' => 'Camioneta', 'kilometraje' => 12500, 'diferencia' => 320, 'estatus' => 'Activo'], ['clave' => 'U002', 'nombre' => 'CAMION-01', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'tipo_vehiculo' => 'Camion', 'kilometraje' => 45800, 'diferencia' => 150, 'estatus' => 'Activo'], ['clave' => 'U003', 'nombre' => 'MOTO-01', 'ruta' => '4683 - RUTA03', 'zona' => '2Z - Zona 2', 'tipo_vehiculo' => 'Motocicleta', 'kilometraje' => 8200, 'diferencia' => 90, 'estatus' => 'Activo'], ['clave' => 'U004', 'nombre' => 'CARRO-01', 'ruta' => '4684 - RUTA04', 'zona' => '2Z - Zona 2', 'tipo_vehiculo' => 'Carro', 'kilometraje' => 31000, 'diferencia' => 0, 'estatus' => 'Activo']];

state([
    'unidades' => $mockUnidades,
    'unidadesFiltradas' => $mockUnidades,
    'search' => '',
    'filtro_estatus' => 'todos',
    'filtro_zona' => 'todos',

    // Columnas visibles
    'col_acciones' => true,
    'col_clave' => true,
    'col_nombre' => true,
    'col_ruta' => true,
    'col_zona' => true,
    'col_tipo_vehiculo' => true,
    'col_kilometraje' => true,
    'col_diferencia' => true,

    // Modal Agregar / Editar
    'showFormModal' => false,
    'modoEditar' => false,
    'editIndex' => null,
    'formClave' => '',
    'formNombre' => '',
    'formZona' => '1Z - Zona 1',
    'formTipoVehiculo' => '',
    'formKilometraje' => '',

    // Modal Historial KM
    'showKmModal' => false,
    'kmUnidadNombre' => '',
]);

$aplicarFiltros = function () {
    $filtradas = collect($this->unidades);

    if ($this->filtro_estatus !== 'todos') {
        $filtradas = $filtradas->where('estatus', $this->filtro_estatus);
    }

    if ($this->filtro_zona !== 'todos') {
        $filtradas = $filtradas->where('zona', $this->filtro_zona);
    }

    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtradas = $filtradas->filter(fn($u) => str_contains(strtolower($u['clave']), $q) || str_contains(strtolower($u['nombre']), $q) || str_contains(strtolower($u['tipo_vehiculo']), $q));
    }

    $this->unidadesFiltradas = $filtradas->values()->toArray();
};

$updatedSearch = fn() => $this->aplicarFiltros();
$updatedFiltroEstatus = fn() => $this->aplicarFiltros();
$updatedFiltroZona = fn() => $this->aplicarFiltros();

$actualizar = function () {
    $this->aplicarFiltros();
};

$clearSearch = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

// Abrir modal para AGREGAR
$abrirAgregar = function () {
    $this->modoEditar = false;
    $this->editIndex = null;
    $this->formClave = '';
    $this->formNombre = '';
    $this->formZona = '1Z - Zona 1';
    $this->formTipoVehiculo = '';
    $this->formKilometraje = '';
    $this->showFormModal = true;
};

// Abrir modal para EDITAR
$abrirEditar = function ($index) {
    $u = $this->unidadesFiltradas[$index];
    $this->modoEditar = true;
    $this->editIndex = $index;
    $this->formClave = $u['clave'];
    $this->formNombre = $u['nombre'];
    $this->formZona = $u['zona'];
    $this->formTipoVehiculo = $u['tipo_vehiculo'];
    $this->formKilometraje = $u['kilometraje'];
    $this->showFormModal = true;
};

$cerrarForm = function () {
    $this->showFormModal = false;
};

$guardar = function () {
    $datos = [
        'clave' => $this->formClave,
        'nombre' => $this->formNombre,
        'ruta' => '',
        'zona' => $this->formZona,
        'tipo_vehiculo' => $this->formTipoVehiculo,
        'kilometraje' => (int) $this->formKilometraje,
        'diferencia' => 0,
        'estatus' => 'Activo',
    ];

    if ($this->modoEditar && $this->editIndex !== null) {
        $this->unidades[$this->editIndex] = $datos;
    } else {
        $this->unidades[] = $datos;
    }

    $this->aplicarFiltros();
    $this->showFormModal = false;
};

$eliminar = function ($index) {
    array_splice($this->unidades, $index, 1);
    $this->aplicarFiltros();
};

$abrirKm = function ($index) {
    $u = $this->unidadesFiltradas[$index];
    $this->kmUnidadNombre = $u['clave'] . ' ' . $u['nombre'];
    $this->showKmModal = true;
};

$cerrarKm = function () {
    $this->showKmModal = false;
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Ruta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Unidades de Reparto</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">

                {{-- Title --}}
                <div class="px-5 pt-5 pb-3">
                    <h1 class="text-xl font-bold text-[#003859]">Unidades de Reparto</h1>
                </div>

                {{-- Toolbar --}}
                <div class="px-5 pb-4 flex flex-col lg:flex-row lg:items-center gap-3" x-data="{ showColumnas: false, showExportar: false }">

                    {{-- Filtro Estatus --}}
                    <div class="relative w-full lg:w-40">
                        <select wire:model.live="filtro_estatus"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Activos</option>
                            <option value="Borrados">Borrados</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtro Zona --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="filtro_zona"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas las Zonas</option>
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1"></div>

                    {{-- Buscar --}}
                    <div class="relative w-full lg:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar ..."
                            class="w-full pl-9 pr-9 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400" />
                        @if (!empty($search))
                            <button wire:click="clearSearch"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Ver Columnas --}}
                    <div class="relative">
                        <button @click="showColumnas = !showColumnas; showExportar = false" title="Ver Columnas"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z" />
                            </svg>
                        </button>
                        <div x-show="showColumnas" @click.outside="showColumnas = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-52">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Columnas</p>
                            @foreach ([['col_acciones', 'Acciones'], ['col_clave', 'Clave'], ['col_nombre', 'Nombre'], ['col_ruta', 'Ruta'], ['col_zona', 'Zona'], ['col_tipo_vehiculo', 'Tipo de Vehículo'], ['col_kilometraje', 'Kilometraje'], ['col_diferencia', 'Diferencia']] as [$field, $label])
                                <label
                                    class="flex items-center gap-2 py-1 px-1 rounded cursor-pointer hover:bg-gray-50 text-sm text-gray-700">
                                    <input type="checkbox" wire:model.live="{{ $field }}"
                                        class="w-4 h-4 text-[#003859] rounded border-gray-300 focus:ring-[#003859]" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Exportar --}}
                    <div class="relative">
                        <button @click="showExportar = !showExportar; showColumnas = false" title="Exportar"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                        <div x-show="showExportar" @click.outside="showExportar = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg py-1 w-40">
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a
                                CSV</button>
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a
                                PDF</button>
                        </div>
                    </div>

                    {{-- Actualizar --}}
                    <button wire:click="actualizar" title="Actualizar"
                        class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>

                    {{-- Agregar --}}
                    <button wire:click="abrirAgregar" title="Agregar"
                        class="p-2 border border-[#003859] bg-[#003859] rounded-lg text-white hover:bg-[#004f7c] transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead
                            class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                            <tr>
                                @if ($col_acciones)
                                    <th scope="col" class="px-6 py-4">Acciones</th>
                                @endif
                                @if ($col_clave)
                                    <th scope="col" class="px-6 py-4">Clave</th>
                                @endif
                                @if ($col_nombre)
                                    <th scope="col" class="px-6 py-4">Nombre</th>
                                @endif
                                @if ($col_ruta)
                                    <th scope="col" class="px-6 py-4">Ruta</th>
                                @endif
                                @if ($col_zona)
                                    <th scope="col" class="px-6 py-4">Zona</th>
                                @endif
                                @if ($col_tipo_vehiculo)
                                    <th scope="col" class="px-6 py-4">Tipo de Vehículo</th>
                                @endif
                                @if ($col_kilometraje)
                                    <th scope="col" class="px-6 py-4">Kilometraje</th>
                                @endif
                                @if ($col_diferencia)
                                    <th scope="col" class="px-6 py-4">Diferencia</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($unidadesFiltradas as $idx => $unidad)
                                <tr class="hover:bg-gray-50/70 transition-colors duration-100">
                                    @if ($col_acciones)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                {{-- Editar --}}
                                                <button wire:click="abrirEditar({{ $idx }})"
                                                    class="p-1.5 text-gray-400 hover:text-[#003859] hover:bg-gray-100 rounded transition"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                {{-- Eliminar --}}
                                                <button wire:click="eliminar({{ $idx }})"
                                                    wire:confirm="¿Estás seguro de eliminar esta unidad?"
                                                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                                {{-- Historial Kilometraje --}}
                                                <button wire:click="abrirKm({{ $idx }})"
                                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded transition"
                                                    title="Historial de Kilometraje">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                    @if ($col_clave)
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            {{ $unidad['clave'] }}</td>
                                    @endif
                                    @if ($col_nombre)
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $unidad['nombre'] }}</td>
                                    @endif
                                    @if ($col_ruta)
                                        <td class="px-6 py-4 text-gray-600">{{ $unidad['ruta'] ?: '—' }}</td>
                                    @endif
                                    @if ($col_zona)
                                        <td class="px-6 py-4 text-gray-600">{{ $unidad['zona'] }}</td>
                                    @endif
                                    @if ($col_tipo_vehiculo)
                                        <td class="px-6 py-4">
                                            @php
                                                $iconos = [
                                                    'Camioneta' => [
                                                        'color' => 'bg-blue-100 text-blue-700',
                                                        'icon' =>
                                                            'M3 13l1-6h16l1 6M3 13h18M3 13l-1 4h20l-1-4M8 17a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z',
                                                    ],
                                                    'Camion' => [
                                                        'color' => 'bg-orange-100 text-orange-700',
                                                        'icon' =>
                                                            'M1 3h15v13H1zM16 8h4l3 3v5h-7V8zM5 19a2 2 0 100-4 2 2 0 000 4zm14 0a2 2 0 100-4 2 2 0 000 4z',
                                                    ],
                                                    'Motocicleta' => [
                                                        'color' => 'bg-green-100 text-green-700',
                                                        'icon' =>
                                                            'M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z',
                                                    ],
                                                    'Carro' => [
                                                        'color' => 'bg-purple-100 text-purple-700',
                                                        'icon' =>
                                                            'M3 13l1-6h16l1 6M3 13h18M3 13l-1 4h20l-1-4M8 17a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z',
                                                    ],
                                                ];
                                                $cfg = $iconos[$unidad['tipo_vehiculo']] ?? [
                                                    'color' => 'bg-gray-100 text-gray-600',
                                                    'icon' => '',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg['color'] }}">
                                                {{ $unidad['tipo_vehiculo'] }}
                                            </span>
                                        </td>
                                    @endif
                                    @if ($col_kilometraje)
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ number_format($unidad['kilometraje']) }} km
                                        </td>
                                    @endif
                                    @if ($col_diferencia)
                                        <td class="px-6 py-4">
                                            @if ($unidad['diferencia'] > 0)
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                    </svg>
                                                    +{{ number_format($unidad['diferencia']) }} km
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400 font-medium">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="text-sm">No se encontraron unidades de reparto.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 bg-[#fafbfc] flex items-center justify-between text-xs text-gray-500 select-none">
                    <div>Mostrando <span class="font-semibold text-gray-700">{{ count($unidadesFiltradas) }}</span> de
                        <span class="font-semibold text-gray-700">{{ count($unidades) }}</span> registros
                    </div>
                    <div class="flex gap-1">
                        <button disabled
                            class="px-3 py-1.5 border border-gray-200 rounded bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed">Anterior</button>
                        <button disabled
                            class="px-3 py-1.5 border border-gray-200 rounded bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">Siguiente</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL: Agregar / Editar Unidad ═══════════════ --}}
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarForm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 mx-4">
                <h2 class="text-lg font-bold text-[#003859] mb-5">
                    {{ $modoEditar ? 'Editar Unidad de Reparto' : 'Nueva Unidad de Reparto' }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Clave --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Clave</label>
                        <input type="text" wire:model="formClave"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]"
                            placeholder="Ej. U001" />
                    </div>
                    {{-- Nombre --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Nombre</label>
                        <input type="text" wire:model="formNombre"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]"
                            placeholder="Ej. CAMIONETA-01" />
                    </div>
                    {{-- Zona --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Zona</label>
                        <select wire:model="formZona"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                    </div>
                    {{-- Tipo de Vehículo --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Tipo de Vehículo</label>
                        <select wire:model="formTipoVehiculo"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="">Seleccionar...</option>
                            <option value="Camioneta">Camioneta</option>
                            <option value="Camion">Camion</option>
                            <option value="Motocicleta">Motocicleta</option>
                            <option value="Carro">Carro</option>
                        </select>
                    </div>
                    {{-- Kilometraje --}}
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Kilometraje</label>
                        <input type="number" wire:model="formKilometraje"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]"
                            placeholder="Ej. 12500" min="0" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button wire:click="cerrarForm"
                        class="px-5 py-2 text-sm font-semibold text-[#003859] hover:bg-gray-50 rounded-lg transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="guardar"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#003859] hover:bg-[#004f7c] rounded-lg transition shadow-sm cursor-pointer">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════ MODAL: Historial de Kilometraje ═══════════════ --}}
    @if ($showKmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarKm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 mx-4">
                <div class="flex items-center gap-3 mb-5">
                    <button wire:click="cerrarKm" class="text-gray-400 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-bold text-[#003859]">Kilometrajes — {{ $kmUnidadNombre }}</h2>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Ruta</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Kilometraje inicial</th>
                                <th class="px-4 py-3">Fecha Km inicial</th>
                                <th class="px-4 py-3">Kilometraje final</th>
                                <th class="px-4 py-3">Fecha Km final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 font-medium">
                                    Sin Registros
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-4 mt-4 text-xs text-gray-500">
                    <span>0–0 of 0</span>
                </div>
            </div>
        </div>
    @endif
</div>
