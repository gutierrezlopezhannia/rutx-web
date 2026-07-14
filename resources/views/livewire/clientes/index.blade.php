<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockClientes = [
    ['id_cliente' => 'CLI-1001', 'razon_social' => 'Supermercados del Norte SA', 'rfc' => 'SUN980122ABC', 'clasificacion' => 'Tipo A', 'limite_credito' => 50000.00],
    ['id_cliente' => 'CLI-1002', 'razon_social' => 'Abarrotes La Esquina', 'rfc' => 'AEL880512DEF', 'clasificacion' => 'Tipo C', 'limite_credito' => 5000.00],
    ['id_cliente' => 'CLI-1003', 'razon_social' => 'Minisuper Centro', 'rfc' => 'MIC991201GHI', 'clasificacion' => 'Tipo B', 'limite_credito' => 15000.00],
    ['id_cliente' => 'CLI-1004', 'razon_social' => 'Tiendas Rápida 24/7', 'rfc' => 'TRD050630JKL', 'clasificacion' => 'Tipo A', 'limite_credito' => 100000.00],
    ['id_cliente' => 'CLI-1005', 'razon_social' => 'Comercializadora Sur', 'rfc' => 'CSU900315MNO', 'clasificacion' => 'Tipo B', 'limite_credito' => 20000.00],
    ['id_cliente' => 'CLI-1006', 'razon_social' => 'Distribuidora Monterrey', 'rfc' => 'DMO851104PQR', 'clasificacion' => 'Tipo A', 'limite_credito' => 75000.00],
    ['id_cliente' => 'CLI-1007', 'razon_social' => 'Farmacia y Minisuper San José', 'rfc' => 'FMS950412STU', 'clasificacion' => 'Tipo C', 'limite_credito' => 8000.00],
    ['id_cliente' => 'CLI-1008', 'razon_social' => 'Carnes y Vinos Premium', 'rfc' => 'CVP920718VWX', 'clasificacion' => 'Tipo A', 'limite_credito' => 120000.00],
    ['id_cliente' => 'CLI-1009', 'razon_social' => 'Abarrotes Don Lucho', 'rfc' => 'ADL900101YZA', 'clasificacion' => 'Tipo C', 'limite_credito' => 4500.00],
    ['id_cliente' => 'CLI-1010', 'razon_social' => 'Bodega Comercial de Occidente', 'rfc' => 'BCO981231BCD', 'clasificacion' => 'Tipo B', 'limite_credito' => 35000.00],
];

state([
    'clientes' => $mockClientes,
    'clientesFiltrados' => $mockClientes,
    'search' => '',
    'filtro_tipo' => 'todos',
    'notification' => '',
]);

$aplicarFiltros = function () {
    $filtrados = collect($this->clientes);

    // Filtrar por clasificación
    if ($this->filtro_tipo !== 'todos') {
        $filtrados = $filtrados->where('clasificacion', $this->filtro_tipo);
    }

    // Filtrar por búsqueda
    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $filtrados = $filtrados->filter(function ($cliente) use ($searchQuery) {
            return str_contains(strtolower($cliente['id_cliente']), $searchQuery) ||
                   str_contains(strtolower($cliente['razon_social']), $searchQuery) ||
                   str_contains(strtolower($cliente['rfc']), $searchQuery);
        });
    }

    $this->clientesFiltrados = $filtrados->values()->toArray();
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};

$updatedFiltroTipo = function () {
    $this->aplicarFiltros();
};

$exportarLista = function () {
    $this->notification = 'Lista de clientes exportada con éxito (formato CSV simulado).';
};

$cerrarNotificacion = function () {
    $this->notification = '';
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Cliente</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Clientes</span>
            </div>

            {{-- Notification Alert --}}
            @if(!empty($notification))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center justify-between shadow-sm animate-fade-in-down">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ $notification }}</span>
                    </div>
                    <button wire:click="cerrarNotificacion" class="text-green-600 hover:text-green-800 p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Upper Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#1f2937] tracking-tight">Catálogo de Clientes</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Clasificación comercial e información general</p>
                </div>
                <div>
                    <button wire:click="exportarLista" 
                        class="flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 border border-gray-300 rounded-lg text-sm transition-all duration-150 shadow-sm focus:outline-none cursor-pointer">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Exportar Lista
                    </button>
                </div>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">
                
                {{-- Filters area --}}
                <div class="p-5 border-b border-gray-100 bg-[#fafbfc]">
                    <div class="flex flex-col sm:flex-row gap-3">
                        {{-- Search Input --}}
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live="search" 
                                placeholder="Buscar por nombre, RFC o ID..." 
                                class="block w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150 shadow-inner"
                            />
                        </div>
                        {{-- Dropdown Select --}}
                        <div class="w-full sm:w-56">
                            <select 
                                wire:model.live="filtro_tipo" 
                                class="block w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150 cursor-pointer shadow-inner"
                            >
                                <option value="todos">Todos los tipos</option>
                                <option value="Tipo A">Tipo A</option>
                                <option value="Tipo B">Tipo B</option>
                                <option value="Tipo C">Tipo C</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Interactive Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                            <tr>
                                <th scope="col" class="px-6 py-4">ID Cliente</th>
                                <th scope="col" class="px-6 py-4">Razón Social</th>
                                <th scope="col" class="px-6 py-4">RFC</th>
                                <th scope="col" class="px-6 py-4">Clasificación</th>
                                <th scope="col" class="px-6 py-4 text-right">Límite Crédito</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($clientesFiltrados as $cliente)
                                <tr class="hover:bg-gray-50/70 transition-colors duration-100">
                                    {{-- ID Cliente --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 font-semibold text-[#004066]">
                                            <svg class="w-4.5 h-4.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>{{ $cliente['id_cliente'] }}</span>
                                        </div>
                                    </td>
                                    {{-- Razón Social --}}
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $cliente['razon_social'] }}
                                    </td>
                                    {{-- RFC --}}
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-500">
                                        {{ $cliente['rfc'] }}
                                    </td>
                                    {{-- Clasificación --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cliente['clasificacion'] === 'Tipo A')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e6f2ff] text-[#0066cc] border border-[#cce3ff] select-none">
                                                Tipo A
                                            </span>
                                        @elseif($cliente['clasificacion'] === 'Tipo B')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#f0f4f8] text-[#102a43] border border-[#d9e2ec] select-none">
                                                Tipo B
                                            </span>
                                        @elseif($cliente['clasificacion'] === 'Tipo C')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#fff3e0] text-[#e65100] border border-[#ffe0b2] select-none">
                                                Tipo C
                                            </span>
                                        @endif
                                    </td>
                                    {{-- Límite Crédito --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                                        ${{ number_format($cliente['limite_credito'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="text-sm">No se encontraron clientes que coincidan con la búsqueda.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer / Pagination representation --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-[#fafbfc] flex items-center justify-between text-xs text-gray-500 select-none">
                    <div>
                        Mostrando <span class="font-semibold text-gray-700">{{ count($clientesFiltrados) }}</span> de <span class="font-semibold text-gray-700">{{ count($clientes) }}</span> registros
                    </div>
                    <div class="flex gap-1">
                        <button disabled class="px-3 py-1.5 border border-gray-200 rounded bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed">Anterior</button>
                        <button disabled class="px-3 py-1.5 border border-gray-200 rounded bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">Siguiente</button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
