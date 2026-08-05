<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockLogs = [['clave_cliente' => 'C001', 'nombre_cliente' => 'CLIENTE NORTE SA DE CV', 'ruta' => '4682 - RUTA02', 'fecha' => '2026-08-04'], ['clave_cliente' => 'C002', 'nombre_cliente' => 'DISTRIBUIDORA LOPEZ', 'ruta' => '4682 - RUTA02', 'fecha' => '2026-08-04'], ['clave_cliente' => 'C003', 'nombre_cliente' => 'COMERCIAL MENDEZ', 'ruta' => '4682 - RUTA02', 'fecha' => '2026-08-04'], ['clave_cliente' => 'C004', 'nombre_cliente' => 'TIENDA LA ESPERANZA', 'ruta' => '4682 - RUTA02', 'fecha' => '2026-08-04'], ['clave_cliente' => 'C005', 'nombre_cliente' => 'ABARROTES EL TIGRE', 'ruta' => '4682 - RUTA02', 'fecha' => '2026-08-04']];

$mockRutas = [['clave' => '3983', 'nombre' => 'RUTA01'], ['clave' => '4682', 'nombre' => 'RUTA02'], ['clave' => '4683', 'nombre' => 'RUTA03'], ['clave' => '4684', 'nombre' => 'RUTA04']];

state([
    'zona' => '1Z - Zona 1',
    'ruta' => '',
    'fecha' => date('Y-m-d'),
    'search' => '',
    'consultado' => false,
    'logs' => [],
    'logsFiltrados' => [],
    'rutas' => $mockRutas,
    'allLogs' => $mockLogs,

    // Columnas visibles
    'col_clave_cliente' => true,
    'col_nombre_cliente' => true,
    'col_ruta' => true,
    'col_fecha' => true,
]);

$consultar = function () {
    // Simula la consulta: filtra por ruta si se seleccionó
    $resultado = collect($this->allLogs);

    if (!empty($this->ruta)) {
        $rutaSeleccionada = collect($this->rutas)->firstWhere('clave', $this->ruta);
        $rutaLabel = $rutaSeleccionada ? $this->ruta . ' - ' . $rutaSeleccionada['nombre'] : '';
        $resultado = $resultado->where('ruta', $rutaLabel);
    }

    $this->logs = $resultado->values()->toArray();
    $this->logsFiltrados = $this->logs;
    $this->search = '';
    $this->consultado = true;
};

$updatedSearch = function () {
    if (empty($this->search)) {
        $this->logsFiltrados = $this->logs;
        return;
    }
    $q = strtolower(trim($this->search));
    $this->logsFiltrados = collect($this->logs)->filter(fn($l) => str_contains(strtolower($l['clave_cliente']), $q) || str_contains(strtolower($l['nombre_cliente']), $q))->values()->toArray();
};

$clearSearch = function () {
    $this->search = '';
    $this->logsFiltrados = $this->logs;
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
                <span class="text-[#003859] font-bold">Clientes sincronizados por fecha</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">

                {{-- Header --}}
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <h1 class="text-xl font-bold text-[#003859]">Clientes sincronizados por fecha</h1>
                    <p class="text-xs text-gray-500 mt-1">Listado de clientes que fueron sincronizados a la ruta en una
                        fecha específica</p>

                    {{-- Filtros de Consulta --}}
                    <div class="flex flex-wrap items-end gap-4 mt-4">

                        {{-- Zona --}}
                        <div class="flex flex-col gap-1 min-w-[160px]">
                            <label class="text-xs font-semibold text-gray-500">Zona</label>
                            <div class="relative">
                                <select wire:model.live="zona"
                                    class="appearance-none border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
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
                        </div>

                        {{-- Ruta --}}
                        <div class="flex flex-col gap-1 min-w-[200px]">
                            <label class="text-xs font-semibold text-gray-500">Ruta</label>
                            <div class="relative">
                                <select wire:model.live="ruta"
                                    class="appearance-none border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                                    <option value="">Ruta</option>
                                    @foreach ($rutas as $r)
                                        <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Fecha --}}
                        <div class="flex flex-col gap-1 min-w-[170px]">
                            <label class="text-xs font-semibold text-gray-500">Fecha</label>
                            <input type="date" wire:model="fecha"
                                class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer" />
                        </div>

                        {{-- Botón Consultar --}}
                        <div class="flex items-end">
                            <button wire:click="consultar"
                                class="px-5 py-2 bg-[#003859] hover:bg-[#004f7c] text-white text-sm font-semibold rounded-lg transition shadow-sm">
                                Consultar
                            </button>
                        </div>

                    </div>
                </div>

                {{-- Barra secundaria: Buscar + Columnas + Exportar (solo si ya se consultó) --}}
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
                                class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-48">
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Columnas</p>
                                @foreach ([['col_clave_cliente', 'Clave del Cliente'], ['col_nombre_cliente', 'Nombre del Cliente'], ['col_ruta', 'Ruta'], ['col_fecha', 'Fecha']] as [$field, $label])
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

                    {{-- Tabla de Resultados --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-left">
                            <thead
                                class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                                <tr>
                                    @if ($col_clave_cliente)
                                        <th scope="col" class="px-6 py-4">Clave del Cliente</th>
                                    @endif
                                    @if ($col_nombre_cliente)
                                        <th scope="col" class="px-6 py-4">Nombre del Cliente</th>
                                    @endif
                                    @if ($col_ruta)
                                        <th scope="col" class="px-6 py-4">Ruta</th>
                                    @endif
                                    @if ($col_fecha)
                                        <th scope="col" class="px-6 py-4">Fecha</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                                @forelse($logsFiltrados as $log)
                                    <tr class="hover:bg-gray-50/70 transition-colors duration-100">
                                        @if ($col_clave_cliente)
                                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                                {{ $log['clave_cliente'] }}</td>
                                        @endif
                                        @if ($col_nombre_cliente)
                                            <td class="px-6 py-4 font-medium text-gray-800">
                                                {{ $log['nombre_cliente'] }}</td>
                                        @endif
                                        @if ($col_ruta)
                                            <td class="px-6 py-4 text-gray-600">{{ $log['ruta'] }}</td>
                                        @endif
                                        @if ($col_fecha)
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ \Carbon\Carbon::parse($log['fecha'])->format('d/m/Y') }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
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
                        <div>Mostrando <span class="font-semibold text-gray-700">{{ count($logsFiltrados) }}</span> de
                            <span class="font-semibold text-gray-700">{{ count($logs) }}</span> registros
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
</div>
