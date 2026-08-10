<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockRutas = [['clave' => '3983', 'nombre' => 'RUTA01'], ['clave' => '4682', 'nombre' => 'RUTA02'], ['clave' => '4683', 'nombre' => 'RUTA03'], ['clave' => '4684', 'nombre' => 'RUTA04']];

$mockEntregas = [
    ['folio' => 'PA0000001', 'cliente_clave' => 'C001', 'cliente_nombre' => 'CLIENTE NORTE SA DE CV', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'fecha_entrega' => '2026-08-05', 'estatus' => 'Pendiente'],
    ['folio' => 'PA0000002', 'cliente_clave' => 'C002', 'cliente_nombre' => 'DISTRIBUIDORA LOPEZ', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'fecha_entrega' => '2026-08-05', 'estatus' => 'Agendado'],
    ['folio' => 'PA0000003', 'cliente_clave' => 'C003', 'cliente_nombre' => 'COMERCIAL MENDEZ', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'fecha_entrega' => '2026-08-05', 'estatus' => 'Entregado'],
    ['folio' => 'PA0000004', 'cliente_clave' => 'C004', 'cliente_nombre' => 'TIENDA LA ESPERANZA', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'fecha_entrega' => '2026-08-05', 'estatus' => 'Pendiente'],
    ['folio' => 'PA0000005', 'cliente_clave' => 'C005', 'cliente_nombre' => 'ABARROTES EL TIGRE', 'ruta' => '4682 - RUTA02', 'zona' => '1Z - Zona 1', 'fecha_entrega' => '2026-08-05', 'estatus' => 'Agendado'],
];

// Preventas pendientes disponibles para agendar
$mockPreventasPendientes = [
    ['folio' => 'PV0000010', 'cliente_clave' => 'C010', 'cliente_nombre' => 'SUPER TIENDA EL SOL', 'importe' => 1850.00],
    ['folio' => 'PV0000011', 'cliente_clave' => 'C011', 'cliente_nombre' => 'ABARROTES DON PEDRO', 'importe' => 3200.50],
    ['folio' => 'PV0000012', 'cliente_clave' => 'C012', 'cliente_nombre' => 'MISCELÁNEA LA FE',    'importe' => 760.00],
    ['folio' => 'PV0000013', 'cliente_clave' => 'C013', 'cliente_nombre' => 'DISTRIBUIDORA REYES', 'importe' => 5100.00],
];

state([
    'zona'              => '1Z - Zona 1',
    'ruta'              => '',
    'fecha'             => date('Y-m-d'),
    'search'            => '',
    'consultado'        => false,
    'entregas'          => [],
    'entregasFiltradas' => [],
    'rutas'             => $mockRutas,
    'allEntregas'       => $mockEntregas,

    // Columnas visibles
    'col_folio'          => true,
    'col_cliente_clave'  => true,
    'col_cliente_nombre' => true,
    'col_ruta'           => true,
    'col_zona'           => true,
    'col_fecha_entrega'  => true,
    'col_estatus'        => true,

    // Modal Agendar Preventas
    'showAgendarModal'      => false,
    'preventasPendientes'   => $mockPreventasPendientes,
    'agFolioSeleccionado'   => '',
    'agFechaEntrega'        => date('Y-m-d'),
    'agRuta'                => '',
]);

$consultar = function () {
    $resultado = collect($this->allEntregas);

    if (!empty($this->ruta)) {
        $rutaSeleccionada = collect($this->rutas)->firstWhere('clave', $this->ruta);
        $rutaLabel = $rutaSeleccionada ? $this->ruta . ' - ' . $rutaSeleccionada['nombre'] : '';
        $resultado = $resultado->where('ruta', $rutaLabel);
    }

    $this->entregas = $resultado->values()->toArray();
    $this->entregasFiltradas = $this->entregas;
    $this->search = '';
    $this->consultado = true;
};

$updatedSearch = function () {
    if (empty($this->search)) {
        $this->entregasFiltradas = $this->entregas;
        return;
    }
    $q = strtolower(trim($this->search));
    $this->entregasFiltradas = collect($this->entregas)->filter(fn($e) => str_contains(strtolower($e['folio']), $q) || str_contains(strtolower($e['cliente_nombre']), $q) || str_contains(strtolower($e['cliente_clave']), $q))->values()->toArray();
};

$clearSearch = function () {
    $this->search = '';
    $this->entregasFiltradas = $this->entregas;
};

$abrirAgendarModal = function () {
    $this->agFolioSeleccionado = '';
    $this->agFechaEntrega      = date('Y-m-d');
    $this->agRuta              = $this->ruta;
    $this->showAgendarModal    = true;
};

$cerrarAgendarModal = function () {
    $this->showAgendarModal = false;
};

$guardarPreventa = function () {
    if (empty($this->agFolioSeleccionado) || empty($this->agFechaEntrega)) {
        return;
    }
    $preventa = collect($this->preventasPendientes)->firstWhere('folio', $this->agFolioSeleccionado);
    if (!$preventa) return;

    $rutaSeleccionada = collect($this->rutas)->firstWhere('clave', $this->agRuta);
    $rutaLabel = $rutaSeleccionada ? $this->agRuta . ' - ' . $rutaSeleccionada['nombre'] : '';

    $this->allEntregas[] = [
        'folio'          => $preventa['folio'],
        'cliente_clave'  => $preventa['cliente_clave'],
        'cliente_nombre' => $preventa['cliente_nombre'],
        'ruta'           => $rutaLabel,
        'zona'           => $this->zona,
        'fecha_entrega'  => $this->agFechaEntrega,
        'estatus'        => 'Agendado',
    ];

    if ($this->consultado) {
        $this->entregas          = $this->allEntregas;
        $this->entregasFiltradas = $this->allEntregas;
    }

    $this->showAgendarModal = false;
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
                <span class="text-[#003859] font-bold">Agenda de Entregas</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">

                {{-- Header --}}
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <h1 class="text-xl font-bold text-[#003859]">Agenda de Entrega por Preventa</h1>

                    {{-- Filtros de Consulta --}}
                    <div class="flex items-end gap-3 mt-4 overflow-x-auto pb-1">

                        {{-- Zona --}}
                        <div class="flex flex-col gap-1 shrink-0 w-40">
                            <label class="text-xs font-semibold text-gray-500">Zona</label>
                            <div class="relative">
                                <select wire:model.live="zona"
                                    class="appearance-none border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                                    <option value="1Z - Zona 1">1Z - Zona 1</option>
                                    <option value="2Z - Zona 2">2Z - Zona 2</option>
                                    <option value="3Z - Zona 3">3Z - Zona 3</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Ruta --}}
                        <div class="flex flex-col gap-1 shrink-0 w-48">
                            <label class="text-xs font-semibold text-gray-500">Ruta</label>
                            <div class="relative">
                                <select wire:model.live="ruta"
                                    class="appearance-none border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                                    <option value="">Ruta</option>
                                    @foreach ($rutas as $r)
                                        <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="flex flex-col gap-1 shrink-0">
                            <label class="text-xs font-semibold text-gray-500">Fecha</label>
                            <input type="date" wire:model="fecha"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer" />
                        </div>

                        {{-- Botones: Consultar + Agendar Preventas --}}
                        <div class="flex items-end gap-2 shrink-0">
                            <button wire:click="consultar"
                                class="px-5 py-2 bg-[#003859] hover:bg-[#004f7c] text-white text-sm font-semibold rounded-lg transition shadow-sm whitespace-nowrap">
                                Consultar
                            </button>
                            <button wire:click="abrirAgendarModal"
                                class="flex items-center gap-2 px-4 py-2 border border-gray-300 hover:border-[#003859] hover:text-[#003859] text-gray-600 text-sm font-semibold rounded-lg transition whitespace-nowrap">
                                Agendar Preventas
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Barra secundaria + Tabla (solo si ya se consultó) --}}
                @if ($consultado)
                    <div class="px-5 py-3 flex items-center justify-end gap-3" x-data="{ showColumnas: false, showExportar: false }">

                        {{-- Buscar --}}
                        <div class="relative w-full max-w-xs">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" wire:model.live="search" placeholder="Buscar ..."
                                class="w-full pl-9 pr-9 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-medium placeholder-gray-400" />
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
                                @foreach ([['col_folio', 'Folio'], ['col_cliente_clave', 'Clave del Cliente'], ['col_cliente_nombre', 'Nombre del Cliente'], ['col_ruta', 'Ruta'], ['col_zona', 'Zona'], ['col_fecha_entrega', 'Fecha de Entrega'], ['col_estatus', 'Estatus']] as [$field, $label])
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
                                <button
                                    class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar
                                    a CSV</button>
                                <button
                                    class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar
                                    a PDF</button>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead
                                class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                                <tr>
                                    @if ($col_folio)
                                        <th scope="col" class="px-6 py-4">Folio</th>
                                    @endif
                                    @if ($col_cliente_clave)
                                        <th scope="col" class="px-6 py-4">Clave Cliente</th>
                                    @endif
                                    @if ($col_cliente_nombre)
                                        <th scope="col" class="px-6 py-4">Nombre del Cliente</th>
                                    @endif
                                    @if ($col_ruta)
                                        <th scope="col" class="px-6 py-4">Ruta</th>
                                    @endif
                                    @if ($col_zona)
                                        <th scope="col" class="px-6 py-4">Zona</th>
                                    @endif
                                    @if ($col_fecha_entrega)
                                        <th scope="col" class="px-6 py-4">Fecha de Entrega</th>
                                    @endif
                                    @if ($col_estatus)
                                        <th scope="col" class="px-6 py-4">Estatus</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                                @forelse($entregasFiltradas as $entrega)
                                    <tr class="hover:bg-gray-50/70 transition-colors duration-100">
                                        @if ($col_folio)
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-[#003859]">
                                                {{ $entrega['folio'] }}</td>
                                        @endif
                                        @if ($col_cliente_clave)
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                                {{ $entrega['cliente_clave'] }}</td>
                                        @endif
                                        @if ($col_cliente_nombre)
                                            <td class="px-6 py-4 font-medium text-gray-800">
                                                {{ $entrega['cliente_nombre'] }}</td>
                                        @endif
                                        @if ($col_ruta)
                                            <td class="px-6 py-4 text-gray-600">{{ $entrega['ruta'] }}</td>
                                        @endif
                                        @if ($col_zona)
                                            <td class="px-6 py-4 text-gray-600">{{ $entrega['zona'] }}</td>
                                        @endif
                                        @if ($col_fecha_entrega)
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ \Carbon\Carbon::parse($entrega['fecha_entrega'])->format('d/m/Y') }}
                                            </td>
                                        @endif
                                        @if ($col_estatus)
                                            <td class="px-6 py-4">
                                                @php
                                                    $cfg = match ($entrega['estatus']) {
                                                        'Agendado' => 'bg-blue-100 text-blue-700',
                                                        'Entregado' => 'bg-green-100 text-green-700',
                                                        default => 'bg-yellow-100 text-yellow-700',
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg }}">
                                                    {{ $entrega['estatus'] }}
                                                </span>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-8 h-8 text-gray-300" fill="none"
                                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span class="text-sm">No hay registros para mostrar</span>
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
                        <div>Mostrando <span
                                class="font-semibold text-gray-700">{{ count($entregasFiltradas) }}</span> de
                            <span class="font-semibold text-gray-700">{{ count($entregas) }}</span> registros
                        </div>
                        <div class="flex gap-1">
                            <button disabled
                                class="px-3 py-1.5 border border-gray-200 rounded bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed">Anterior</button>
                            <button disabled
                                class="px-3 py-1.5 border border-gray-200 rounded bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">Siguiente</button>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL: Agendar Preventa ═══════════════ --}}
    @if ($showAgendarModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarAgendarModal">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 mx-4">

                {{-- Título --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-[#003859]">Agendar Preventa</h2>
                    <button wire:click="cerrarAgendarModal" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4">

                    {{-- Folio de Preventa --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Folio de Preventa</label>
                        <div class="relative">
                            <select wire:model.live="agFolioSeleccionado"
                                class="appearance-none w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer">
                                <option value="">Seleccionar folio...</option>
                                @foreach ($preventasPendientes as $p)
                                    <option value="{{ $p['folio'] }}">{{ $p['folio'] }} — {{ $p['cliente_nombre'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Info del Cliente (se muestra al seleccionar folio) --}}
                    @if (!empty($agFolioSeleccionado))
                        @php
                            $prevSeleccionada = collect($preventasPendientes)->firstWhere('folio', $agFolioSeleccionado);
                        @endphp
                        @if ($prevSeleccionada)
                            <div class="bg-[#f8fafc] border border-gray-200 rounded-lg p-3 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#003859] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">{{ $prevSeleccionada['cliente_nombre'] }}</p>
                                    <p class="text-xs text-gray-500">Clave: {{ $prevSeleccionada['cliente_clave'] }} &nbsp;|&nbsp; Importe: ${{ number_format($prevSeleccionada['importe'], 2) }}</p>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Fecha de Entrega --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Fecha de Entrega</label>
                        <input type="date" wire:model="agFechaEntrega"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer" />
                    </div>

                    {{-- Ruta --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Ruta</label>
                        <div class="relative">
                            <select wire:model="agRuta"
                                class="appearance-none w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer">
                                <option value="">Seleccionar ruta...</option>
                                @foreach ($rutas as $r)
                                    <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button wire:click="cerrarAgendarModal"
                        class="px-5 py-2 text-sm font-semibold text-[#003859] hover:bg-gray-50 rounded-lg transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="guardarPreventa"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#003859] hover:bg-[#004f7c] rounded-lg transition shadow-sm cursor-pointer">
                        Agendar
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
