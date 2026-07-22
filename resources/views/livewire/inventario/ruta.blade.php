<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state([
    // Listado de rutas con información de carga
    'rutas_carga' => [
        [
            'id' => 'RE01',
            'ruta_nombre' => 'Ruta Centro',
            'vendedor' => 'VENDEDOR PRUEBA',
            'estatus' => 'En Tránsito',
            'porcentaje_carga' => 64,
            'unidades_cargadas' => 450,
            'capacidad_maxima' => 700,
            'detalles' => [
                ['codigo' => 'PROD-001', 'descripcion' => 'Bebida Energética 500ml', 'cargado' => 150, 'vendido' => 88, 'restante' => 62],
                ['codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'cargado' => 200, 'vendido' => 124, 'restante' => 76],
                ['codigo' => 'PROD-005', 'descripcion' => 'Refresco de Cola 600ml', 'cargado' => 100, 'vendido' => 54, 'restante' => 46],
            ]
        ],
        [
            'id' => 'RE02',
            'ruta_nombre' => 'Ruta Norte',
            'vendedor' => 'Ana María Gutiérrez',
            'estatus' => 'Conciliado',
            'porcentaje_carga' => 0,
            'unidades_cargadas' => 0,
            'capacidad_maxima' => 800,
            'detalles' => []
        ],
        [
            'id' => 'RE03',
            'ruta_nombre' => 'Ruta Sur',
            'vendedor' => 'Carlos López Estrada',
            'estatus' => 'Pendiente Cierre',
            'porcentaje_carga' => 48,
            'unidades_cargadas' => 380,
            'capacidad_maxima' => 800,
            'detalles' => [
                ['codigo' => 'PROD-003', 'descripcion' => 'Galletas de Chocolate 120g', 'cargado' => 120, 'vendido' => 90, 'restante' => 30],
                ['codigo' => 'PROD-004', 'descripcion' => 'Papas Fritas Clásicas 50g', 'cargado' => 180, 'vendido' => 140, 'restante' => 40],
                ['codigo' => 'PROD-007', 'descripcion' => 'Cacahuate Japonés 100g', 'cargado' => 80, 'vendido' => 50, 'restante' => 30],
            ]
        ],
    ],
    
    // Modal
    'show_detail_modal' => false,
    'selected_ruta' => null,
]);

// Ver detalle
$verDetalle = function($rutaId) {
    $this->selected_ruta = collect($this->rutas_carga)->firstWhere('id', $rutaId);
    $this->show_detail_modal = true;
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
            <span class="text-[#003859] dark:text-blue-400 font-bold">Inventario por Ruta</span>
        </div>

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Inventario en Unidades de Reparto</h1>
            <p class="text-xs text-gray-450 dark:text-gray-400 mt-1">Supervisión en tiempo real de la mercancía cargada, vendida y remanente a bordo de cada vehículo.</p>
        </div>

        {{-- Grid de Tarjetas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6 px-1">
            @foreach ($rutas_carga as $r)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/80 rounded-xl p-5 shadow-sm flex flex-col hover:shadow-md transition-shadow">
                    
                    {{-- Header de Tarjeta --}}
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 dark:text-white">{{ $r['ruta_nombre'] }}</h3>
                            <div class="text-[11px] text-gray-400 dark:text-gray-550 mt-0.5">{{ $r['vendedor'] }}</div>
                        </div>
                        
                        {{-- Status Badge --}}
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                            {{ $r['estatus'] === 'En Tránsito' ? 'bg-blue-50 text-blue-700 border-blue-150 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30' : '' }}
                            {{ $r['estatus'] === 'Conciliado' ? 'bg-green-50 text-green-700 border-green-150 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30' : '' }}
                            {{ $r['estatus'] === 'Pendiente Cierre' ? 'bg-orange-50 text-orange-700 border-orange-150 dark:bg-orange-950/20 dark:text-orange-400 dark:border-orange-900/30' : '' }}">
                            {{ $r['estatus'] }}
                        </span>
                    </div>

                    {{-- Indicador de Carga --}}
                    <div class="mb-5 mt-2">
                        <div class="flex justify-between items-center text-[11px] mb-1.5">
                            <span class="text-gray-400">Capacidad de Carga</span>
                            <span class="font-bold text-gray-750 dark:text-gray-300">{{ $r['unidades_cargadas'] }} / {{ $r['capacidad_maxima'] }} pzs</span>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-100 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300
                                {{ $r['porcentaje_carga'] > 75 ? 'bg-red-500' : ($r['porcentaje_carga'] > 40 ? 'bg-blue-500' : 'bg-green-500') }}"
                                style="width: {{ $r['porcentaje_carga'] }}%">
                            </div>
                        </div>
                        <div class="text-[10px] text-right text-gray-400 mt-1 font-medium">{{ $r['porcentaje_carga'] }}% cargado</div>
                    </div>

                    {{-- Detalle Abreviado --}}
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-6 flex-1 space-y-1.5 bg-gray-50 dark:bg-gray-900/40 p-3 rounded-lg border border-gray-100 dark:border-gray-800">
                        @if (count($r['detalles']) > 0)
                            <div class="font-bold text-[10px] text-gray-400 uppercase tracking-wider mb-1">Muestra de Productos:</div>
                            @foreach (collect($r['detalles'])->take(2) as $det)
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="truncate max-w-[150px] font-medium">{{ $det['descripcion'] }}</span>
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">A bordo: {{ $det['restante'] }} pzs</span>
                                </div>
                            @endforeach
                            @if (count($r['detalles']) > 2)
                                <div class="text-[10px] text-gray-400 italic font-medium">+ {{ count($r['detalles']) - 2 }} artículos más</div>
                            @endif
                        @else
                            <div class="text-center py-2 text-[11px] text-gray-400 italic">No hay productos cargados en esta unidad.</div>
                        @endif
                    </div>

                    {{-- Botón Detalles --}}
                    <button wire:click="verDetalle('{{ $r['id'] }}')"
                        class="w-full py-2 bg-gray-100 hover:bg-[#003859] hover:text-white dark:bg-gray-700 dark:hover:bg-blue-600 dark:text-gray-300 rounded-lg text-xs font-bold transition-all duration-150 cursor-pointer">
                        Ver Detalle de Carga
                    </button>

                </div>
            @endforeach
        </div>

    </div>

    {{-- Modal Detalle de Carga --}}
    @if ($show_detail_modal && $selected_ruta)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl animate-scale-up">
                    
                    {{-- Header Modal --}}
                    <div class="bg-white dark:bg-gray-800 px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                                Detalle de Inventario a Bordo
                            </h3>
                            <div class="text-[11px] text-gray-400 dark:text-gray-400 mt-0.5">{{ $selected_ruta['ruta_nombre'] }} — {{ $selected_ruta['vendedor'] }}</div>
                        </div>
                        
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                            {{ $selected_ruta['estatus'] === 'En Tránsito' ? 'bg-blue-50 text-blue-700 border-blue-150 dark:bg-blue-950/20 dark:text-blue-400' : '' }}
                            {{ $selected_ruta['estatus'] === 'Conciliado' ? 'bg-green-50 text-green-700 border-green-150 dark:bg-green-950/20 dark:text-green-400' : '' }}
                            {{ $selected_ruta['estatus'] === 'Pendiente Cierre' ? 'bg-orange-50 text-orange-700 border-orange-150 dark:bg-orange-950/20 dark:text-orange-400' : '' }}">
                            {{ $selected_ruta['estatus'] }}
                        </span>
                    </div>

                    {{-- Body Modal --}}
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 text-xs">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden max-h-[300px] overflow-y-auto">
                            <table class="w-full text-left border-collapse text-[11px]">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-700">
                                        <th class="p-2.5">Código</th>
                                        <th class="p-2.5">Descripción</th>
                                        <th class="p-2.5 text-center">Cargado</th>
                                        <th class="p-2.5 text-center">Vendido</th>
                                        <th class="p-2.5 text-center">A Bordo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                    @forelse ($selected_ruta['detalles'] as $det)
                                        <tr>
                                            <td class="p-2.5 font-semibold text-gray-700 dark:text-gray-300">{{ $det['codigo'] }}</td>
                                            <td class="p-2.5 text-gray-550 dark:text-gray-400 truncate max-w-[200px]">{{ $det['descripcion'] }}</td>
                                            <td class="p-2.5 text-center text-gray-500 dark:text-gray-400">{{ $det['cargado'] }} pzs</td>
                                            <td class="p-2.5 text-center text-green-600 dark:text-green-400 font-medium">{{ $det['vendido'] }} pzs</td>
                                            <td class="p-2.5 text-center font-bold text-gray-850 dark:text-white">{{ $det['restante'] }} pzs</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-gray-400 dark:text-gray-500 select-none">No hay registros de inventario a bordo.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="bg-gray-50 dark:bg-gray-800/80 px-6 py-4 flex justify-between items-center border-t border-gray-100 dark:border-gray-700 rounded-b-xl">
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                            Total a bordo: <span class="font-bold text-gray-750 dark:text-white ml-0.5">{{ array_sum(array_column($selected_ruta['detalles'], 'restante')) }} pzs</span>
                        </div>
                        <button wire:click="$set('show_detail_modal', false)"
                            class="px-4 py-2 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                            Cerrar Detalle
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
