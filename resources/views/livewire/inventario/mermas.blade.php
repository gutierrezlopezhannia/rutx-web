<?php

use function Livewire\Volt\{state, layout};
use App\Models\Seller;

layout('layouts.app');

state([
    // Catálogos desde BD
    'vendedores' => fn() => Seller::all()->toArray(),
    
    // Filtros
    'fecha_inicio' => date('Y-m-d', strtotime('-7 days')),
    'fecha_fin' => date('Y-m-d'),
    'filtro_vendedor' => 'todos',
    'filtro_tipo' => 'todos', // todos, merma, devolucion
    'search' => '',
    
    // Reporte consolidado mock
    'reporte_original' => [
        ['fecha' => '2026-07-22', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Devolución', 'cliente' => 'CLIENTE PRUEBA 01', 'codigo' => 'PROD-006', 'producto' => 'Jugo de Naranja 1L', 'cantidad' => 3, 'motivo' => 'Exceso de pedido solicitado'],
        ['fecha' => '2026-07-22', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Devolución', 'cliente' => 'CLIENTE PRUEBA 03', 'codigo' => 'PROD-002', 'producto' => 'Agua Purificada 1L', 'cantidad' => 5, 'motivo' => 'Mal estado del envase'],
        ['fecha' => '2026-07-22', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Merma', 'cliente' => 'N/A', 'codigo' => 'PROD-003', 'producto' => 'Galletas de Chocolate 120g', 'cantidad' => 2, 'motivo' => 'Empaque dañado en trayecto'],
        ['fecha' => '2026-07-21', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Merma', 'cliente' => 'N/A', 'codigo' => 'PROD-004', 'producto' => 'Papas Fritas Clásicas 50g', 'cantidad' => 1, 'motivo' => 'Producto caducado'],
        ['fecha' => '2026-07-20', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Devolución', 'cliente' => 'CLIENTE PRUEBA 02', 'codigo' => 'PROD-005', 'producto' => 'Refresco de Cola 600ml', 'cantidad' => 6, 'motivo' => 'Pedido equivocado'],
        ['fecha' => '2026-07-19', 'vendedor_id' => '999001 - VENDEDOR PRUEBA', 'vendedor' => 'VENDEDOR PRUEBA', 'tipo' => 'Merma', 'cliente' => 'N/A', 'codigo' => 'PROD-001', 'producto' => 'Bebida Energética 500ml', 'cantidad' => 4, 'motivo' => 'Lata golpeada'],
    ],
    'reporte_filtrado' => [],
    
    // Mensajes
    'notification' => '',
]);

// Inicialización
$init = function() {
    $this->aplicarFiltros();
};

// Lógica de filtros
$aplicarFiltros = function() {
    $filtrado = collect($this->reporte_original);
    
    // Filtrar por vendedor
    if ($this->filtro_vendedor !== 'todos') {
        $filtrado = $filtrado->where('vendedor_id', $this->filtro_vendedor);
    }
    
    // Filtrar por tipo
    if ($this->filtro_tipo !== 'todos') {
        $filtrado = $filtrado->where('tipo', $this->filtro_tipo);
    }
    
    // Filtrar por fechas
    if ($this->fecha_inicio) {
        $filtrado = $filtrado->where('fecha', '>=', $this->fecha_inicio);
    }
    if ($this->fecha_fin) {
        $filtrado = $filtrado->where('fecha', '<=', $this->fecha_fin);
    }
    
    // Búsqueda de texto
    if (!empty($this->search)) {
        $query = strtolower(trim($this->search));
        $filtrado = $filtrado->filter(function($row) use ($query) {
            return str_contains(strtolower($row['producto']), $query) ||
                str_contains(strtolower($row['codigo']), $query) ||
                str_contains(strtolower($row['motivo']), $query) ||
                str_contains(strtolower($row['cliente']), $query);
        });
    }
    
    $this->reporte_filtrado = $filtrado->values()->toArray();
};

// Reactividad a cambios
$updatedFiltroVendedor = fn() => $this->aplicarFiltros();
$updatedFiltroTipo = fn() => $this->aplicarFiltros();
$updatedFechaInicio = fn() => $this->aplicarFiltros();
$updatedFechaFin = fn() => $this->aplicarFiltros();
$updatedSearch = fn() => $this->aplicarFiltros();

// Exportar CSV
$exportarCSV = function() {
    $this->notification = 'Descarga de reporte en formato CSV iniciada correctamente.';
};

?>

<div class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4">
    <div class="w-full px-6 flex flex-col flex-1">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1">
            <span>Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span>Inventario</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Reporte de Mermas y Devoluciones</span>
        </div>

        {{-- Header & Notificación --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Reporte General de Mermas y Devoluciones</h1>
            
            {{-- Toast Notification --}}
            @if ($notification)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('notification', '') }, 3500)"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border border-green-200 text-sm shadow-sm bg-green-50 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300 transition-all duration-150 animate-fade-in">
                    <span>{{ $notification }}</span>
                </div>
            @endif
        </div>

        {{-- Filtros --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6 px-1">
            {{-- Vendedor --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">VENDEDOR / RUTA</label>
                <select wire:model.live="filtro_vendedor" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="todos">Todos los Vendedores</option>
                    @foreach ($vendedores as $v)
                        <option value="{{ $v['id'] }}">{{ $v['name'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Tipo --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">TIPO DE INCIDENCIA</label>
                <select wire:model.live="filtro_tipo" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="todos">Todos los Tipos</option>
                    <option value="Merma">Solo Mermas</option>
                    <option value="Devolución">Solo Devoluciones</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Fecha Inicio --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">FECHA INICIO</label>
                <input type="date" wire:model.live="fecha_inicio" class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>

            {{-- Fecha Fin --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">FECHA FIN</label>
                <input type="date" wire:model.live="fecha_fin" class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>

            {{-- Buscador Texto --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">BUSCAR TEXTO</label>
                <input type="text" wire:model.live="search" placeholder="Código, motivo o cliente..."
                    class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>
        </div>

        {{-- Card de Resultados --}}
        <div class="flex-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-5 flex flex-col mb-6">
            
            {{-- Acciones del Reporte --}}
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Registros encontrados: <span class="font-bold text-gray-700 dark:text-gray-300">{{ count($reporte_filtrado) }}</span></span>
                
                <button wire:click="exportarCSV" class="flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-[#003859] dark:text-gray-400 dark:hover:text-blue-400 transition cursor-pointer">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar CSV
                </button>
            </div>

            {{-- Tabla Reporte --}}
            <div class="flex-1 overflow-y-auto max-h-[400px] border border-gray-150 dark:border-gray-800 rounded-lg">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-800 select-none">
                            <th class="p-3">Fecha</th>
                            <th class="p-3">Ruta / Vendedor</th>
                            <th class="p-3">Tipo</th>
                            <th class="p-3">Cliente</th>
                            <th class="p-3">Producto</th>
                            <th class="p-3 text-center">Cantidad</th>
                            <th class="p-3">Motivo / Incidencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($reporte_filtrado as $row)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="p-3 text-gray-500 dark:text-gray-400">{{ $row['fecha'] }}</td>
                                <td class="p-3 font-semibold text-gray-800 dark:text-white">{{ $row['vendedor'] }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row['tipo'] === 'Merma' ? 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400 border border-red-100 dark:border-red-900/30' : '' }}
                                        {{ $row['tipo'] === 'Devolución' ? 'bg-yellow-50 text-yellow-750 dark:bg-yellow-950/20 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-900/30' : '' }}">
                                        {{ $row['tipo'] }}
                                    </span>
                                </td>
                                <td class="p-3 text-gray-700 dark:text-gray-300 font-medium">{{ $row['cliente'] }}</td>
                                <td class="p-3">
                                    <div class="font-semibold text-gray-700 dark:text-gray-300">{{ $row['codigo'] }}</div>
                                    <div class="text-[10px] text-gray-400 truncate max-w-[150px]">{{ $row['producto'] }}</div>
                                </td>
                                <td class="p-3 text-center font-bold text-gray-800 dark:text-gray-200">{{ $row['cantidad'] }} pzs</td>
                                <td class="p-3">
                                    <span class="text-gray-600 dark:text-gray-300 text-[11px]">{{ $row['motivo'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-6 text-center text-gray-400 dark:text-gray-500">No se encontraron registros de incidencias para los filtros aplicados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Totales del Reporte --}}
            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center text-xs text-gray-500 dark:text-gray-400 font-medium">
                <div>Mostrando mermas y devoluciones de productos.</div>
                <div>Total de piezas mermadas/devueltas en rango: <span class="font-bold text-[#003859] dark:text-blue-400 text-sm ml-1">{{ array_sum(array_column($reporte_filtrado, 'cantidad')) }} pzs</span></div>
            </div>

        </div>

    </div>
</div>
