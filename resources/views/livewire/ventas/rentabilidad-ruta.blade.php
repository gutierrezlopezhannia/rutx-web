<?php

use function Livewire\Volt\{state, layout, mount, updated};
use App\Models\RouteProfitability;

layout('layouts.app');

state([
    'filtro_zona' => '',
    'filtro_rutas' => [],
    'fecha_inicio' => '2026-07-21',
    'fecha_fin' => '2026-07-21',
    'consultado' => false,
    'registrosFiltrados' => [],
    'zonas' => [],
    'rutas' => []
]);

// Cargar listas únicas de zonas y rutas de la BD al iniciar
mount(function () {
    $this->zonas = RouteProfitability::select('zona_id')->distinct()->pluck('zona_id')->toArray();
    $this->rutas = RouteProfitability::select('ruta_id')->distinct()->pluck('ruta_id')->toArray();
});

// Cargar rutas asociadas a la zona seleccionada
updated(['filtro_zona' => function ($value) {
    if ($value) {
        $this->rutas = RouteProfitability::where('zona_id', $value)->select('ruta_id')->distinct()->pluck('ruta_id')->toArray();
    } else {
        $this->rutas = RouteProfitability::select('ruta_id')->distinct()->pluck('ruta_id')->toArray();
    }
    $this->filtro_rutas = [];
}]);

$consultar = function () {
    $query = RouteProfitability::query();

    if ($this->filtro_zona) {
        $query->where('zona_id', $this->filtro_zona);
    }

    if (!empty($this->filtro_rutas)) {
        $selected = array_filter($this->filtro_rutas, fn($r) => $r !== 'Todas');
        if (!empty($selected)) {
            $query->whereIn('ruta_id', $selected);
        }
    }

    if ($this->fecha_inicio) {
        $query->where('fecha', '>=', $this->fecha_inicio);
    }

    if ($this->fecha_fin) {
        $query->where('fecha', '<=', $this->fecha_fin);
    }

    $this->registrosFiltrados = $query->get()->toArray();
    $this->consultado = true;
};

$descargarCSV = function () {
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=reporte_rentabilidad_ruta.csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() {
        $file = fopen('php://output', 'w');
        fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
        
        fputcsv($file, [
            'Vendedor/Ruta',
            'Ventas Netas',
            'Ventas Contado',
            'Ventas Crédito',
            'Preventas',
            'Costo Compra Preventa',
            'Clientes Preventa',
            'Entregas',
            'Costo por Producto',
            'Devolución Contado',
            'Días',
            'Gastos Operativos',
            'Costo Venta',
            'Utilidad Ruta total',
            'Porcentaje'
        ]);

        foreach ($this->registrosFiltrados as $reg) {
            fputcsv($file, [
                $reg['vendedor'],
                number_format($reg['ventas_netas'], 2, '.', ''),
                number_format($reg['ventas_contado'], 2, '.', ''),
                number_format($reg['ventas_credito'], 2, '.', ''),
                number_format($reg['preventas'], 2, '.', ''),
                number_format($reg['costo_compra_preventa'], 2, '.', ''),
                $reg['clientes_preventa'] ?? '',
                number_format($reg['entregas'], 2, '.', ''),
                number_format($reg['costo_producto'], 2, '.', ''),
                number_format($reg['devolucion_contado'], 2, '.', ''),
                $reg['dias'],
                number_format($reg['gastos_operativos'], 2, '.', ''),
                number_format($reg['costo_venta'], 2, '.', ''),
                number_format($reg['utilidad_ruta'], 2, '.', ''),
                number_format($reg['porcentaje'], 2, '.', '') . '%'
            ]);
        }
        
        $totales = [
            'ventas_netas' => array_sum(array_column($this->registrosFiltrados, 'ventas_netas')),
            'ventas_contado' => array_sum(array_column($this->registrosFiltrados, 'ventas_contado')),
            'ventas_credito' => array_sum(array_column($this->registrosFiltrados, 'ventas_credito')),
            'preventas' => array_sum(array_column($this->registrosFiltrados, 'preventas')),
            'costo_compra_preventa' => array_sum(array_column($this->registrosFiltrados, 'costo_compra_preventa')),
            'clientes_preventa' => array_sum(array_column($this->registrosFiltrados, 'clientes_preventa')),
            'entregas' => array_sum(array_column($this->registrosFiltrados, 'entregas')),
            'costo_producto' => array_sum(array_column($this->registrosFiltrados, 'costo_producto')),
            'devolucion_contado' => array_sum(array_column($this->registrosFiltrados, 'devolucion_contado')),
            'dias' => array_sum(array_column($this->registrosFiltrados, 'dias')),
            'gastos_operativos' => array_sum(array_column($this->registrosFiltrados, 'gastos_operativos')),
            'costo_venta' => array_sum(array_column($this->registrosFiltrados, 'costo_venta')),
            'utilidad_ruta' => array_sum(array_column($this->registrosFiltrados, 'utilidad_ruta')),
        ];

        $totalPorcentaje = $totales['ventas_netas'] > 0 ? ($totales['utilidad_ruta'] / $totales['ventas_netas']) * 100 : 0;
        
        fputcsv($file, [
            'Total',
            number_format($totales['ventas_netas'], 2, '.', ''),
            number_format($totales['ventas_contado'], 2, '.', ''),
            number_format($totales['ventas_credito'], 2, '.', ''),
            number_format($totales['preventas'], 2, '.', ''),
            number_format($totales['costo_compra_preventa'], 2, '.', ''),
            $totales['clientes_preventa'],
            number_format($totales['entregas'], 2, '.', ''),
            number_format($totales['costo_producto'], 2, '.', ''),
            number_format($totales['devolucion_contado'], 2, '.', ''),
            $totales['dias'],
            number_format($totales['gastos_operativos'], 2, '.', ''),
            number_format($totales['costo_venta'], 2, '.', ''),
            number_format($totales['utilidad_ruta'], 2, '.', ''),
            number_format($totalPorcentaje, 2, '.', '') . '%'
        ]);

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
};
?>


<div x-data="{
    routeDropdownOpen: false,
    selectedRutas: @entangle('filtro_rutas'),
    zonaSelected: @entangle('filtro_zona'),
    allAvailable: @entangle('rutas'),
    get displayValue() {
        if (this.selectedRutas.length === 0) return 'Ruta';
        const realSelected = this.selectedRutas.filter(r => r !== 'Todas');
        if (this.selectedRutas.includes('Todas') || realSelected.length === this.allAvailable.length) {
            return `--- Ruta ---, +${this.allAvailable.length}`;
        }
        if (realSelected.length > 1) {
            return `--- Ruta ---, +${realSelected.length}`;
        }
        return realSelected.join(', ');
    },
    toggleAll() {
        if (this.selectedRutas.includes('Todas')) {
            this.selectedRutas = [...this.allAvailable, 'Todas'];
        } else {
            this.selectedRutas = [];
        }
    },
    toggleRuta(ruta) {
        if (this.selectedRutas.includes(ruta)) {
            this.selectedRutas = this.selectedRutas.filter(r => r !== ruta);
            this.selectedRutas = this.selectedRutas.filter(r => r !== 'Todas');
        } else {
            this.selectedRutas.push(ruta);
            const realSelected = this.selectedRutas.filter(r => r !== 'Todas');
            if (realSelected.length === this.allAvailable.length) {
                this.selectedRutas.push('Todas');
            }
        }
    },
    
    // Column Configurator variables
    colDropdownOpen: false,
    visibleCols: [
        'vendedor', 'ventas_netas', 'ventas_contado', 'ventas_credito', 'preventas', 
        'costo_compra_preventa', 'clientes_preventa', 'entregas', 'costo_producto', 
        'devolucion_contado', 'dias', 'gastos_operativos', 'costo_venta', 
        'utilidad_ruta', 'porcentaje'
    ],
    
    // Client-side pagination variables
    page: 1,
    perPage: 10
}">
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-semibold">Reporte Rentabilidad por Ruta</span>
            </div>

            {{-- Card de Filtros y Búsqueda --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-6 mb-6">
                
                {{-- Título con Icono de Información --}}
                <div class="flex items-center gap-2 mb-6">
                    <h2 class="text-base font-bold text-gray-800 tracking-tight">Rentabilidad por Ruta</h2>
                    <span class="inline-flex items-center text-gray-400 hover:text-gray-600 cursor-pointer" title="Reporte de rentabilidad detallada por ruta">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>

                {{-- Fila de Controles --}}
                <div @click.away="routeDropdownOpen = false"
                     class="flex flex-col lg:flex-row lg:items-end gap-6"
                >
                    
                    {{-- Selector de Zona --}}
                    <div class="flex flex-col w-full lg:w-48">
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="">Zona</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona }}">{{ $zona }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Selector de Ruta (Multiselect con Checkbox y opción Todas) --}}
                    <div class="flex flex-col w-full lg:w-48">
                        <div class="relative w-full">
                            {{-- Input Disparador (Estilo Select RUTX) --}}
                            <div @click="if (zonaSelected) { routeDropdownOpen = !routeDropdownOpen }"
                                 :class="zonaSelected ? 'cursor-pointer opacity-100' : 'cursor-not-allowed opacity-50'"
                                 class="border-0 border-b border-gray-300 pb-1.5 flex items-center justify-between"
                            >
                                <span class="text-sm font-semibold text-gray-700 truncate" x-text="displayValue"></span>
                                <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            {{-- Menú Desplegable --}}
                            <div x-show="routeDropdownOpen && zonaSelected"
                                 x-transition
                                 class="absolute left-0 mt-1 w-64 bg-white border border-gray-200 shadow-xl rounded-lg py-1.5 z-30 max-h-64 overflow-y-auto text-sm"
                                 style="display: none;"
                            >
                                {{-- Opción: Todas --}}
                                <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer select-none">
                                    <input type="checkbox"
                                           value="Todas"
                                           x-model="selectedRutas"
                                           @change="toggleAll()"
                                           class="w-4 h-4 text-[#003859] border-gray-300 rounded focus:ring-[#003859] cursor-pointer"
                                    />
                                    <span class="ml-3 text-gray-700 font-semibold">Todas</span>
                                </label>

                                <hr class="border-gray-150 my-1">

                                {{-- Lista de Rutas --}}
                                @foreach($rutas as $ruta)
                                    <label class="flex items-center px-4 py-2 hover:bg-gray-50 cursor-pointer select-none">
                                        <input type="checkbox"
                                               value="{{ $ruta }}"
                                               x-model="selectedRutas"
                                               @change="
                                                   if (selectedRutas.includes('{{ $ruta }}')) {
                                                       const realSelected = selectedRutas.filter(r => r !== 'Todas');
                                                       if (realSelected.length === allAvailable.length) {
                                                           selectedRutas.push('Todas');
                                                       }
                                                   } else {
                                                       selectedRutas = selectedRutas.filter(r => r !== 'Todas');
                                                   }
                                               "
                                               class="w-4 h-4 text-[#003859] border-gray-300 rounded focus:ring-[#003859] cursor-pointer"
                                        />
                                        <span class="ml-3 text-gray-700">{{ $ruta }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-full lg:w-40">
                        <label class="text-xs text-gray-400 font-medium mb-1">Fecha inicial</label>
                        <input type="date" wire:model.live="fecha_inicio" class="border-0 border-b border-gray-300 rounded-none p-0 pb-1.5 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold w-full" />
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full lg:w-40">
                        <label class="text-xs text-gray-400 font-medium mb-1">Fecha final</label>
                        <input type="date" wire:model.live="fecha_fin" class="border-0 border-b border-gray-300 rounded-none p-0 pb-1.5 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold w-full" />
                    </div>

                    {{-- Botón Consultar --}}
                    <div class="w-full lg:w-auto">
                        <button wire:click="consultar"
                                x-bind:disabled="!zonaSelected || selectedRutas.length === 0"
                                :class="zonaSelected && selectedRutas.length > 0 ? 'bg-[#003859] hover:bg-[#002d48] text-white cursor-pointer shadow-md' : 'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed'"
                                class="px-6 py-2 rounded-lg text-sm font-semibold transition duration-150 w-full lg:w-auto"
                        >
                            Consultar
                        </button>
                    </div>
                </div>

                {{-- Barra de Carga Horizontal --}}
                <div wire:loading wire:target="consultar" class="w-full h-1 bg-gray-100 overflow-hidden relative mt-4 rounded-full no-print">
                    <div class="h-full bg-[#003859] absolute top-0 animate-progress-loading" style="width: 30%;"></div>
                </div>

            </div>

            {{-- Tabla de Resultados (Solo se muestra cuando se ha consultado) --}}
            @if($consultado)
                {{-- Cabecera exclusiva para impresión --}}
                <div class="hidden print:block text-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 mb-2">Reporte de Rentabilidad por Ruta</h1>
                    <p class="text-xs text-gray-500">
                        <strong>Zona:</strong> {{ $filtro_zona }} &nbsp;|&nbsp; 
                        <strong>Periodo:</strong> {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                    
                    {{-- Barra de herramientas superior de la tabla --}}
                    <div class="flex justify-end items-center px-6 py-4 border-b border-gray-150 gap-2 bg-gray-50/50">
                        {{-- Icono de columnas --}}
                        <button @click="colDropdownOpen = !colDropdownOpen" class="p-2 text-gray-500 hover:text-[#003859] hover:bg-gray-100 rounded-lg transition duration-150 cursor-pointer" title="Configurar Columnas">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        {{-- Icono de descargar con dropdown --}}
                        <div x-data="{ exportOpen: false }" @click.away="exportOpen = false" class="relative no-print">
                            <button @click="exportOpen = !exportOpen" class="p-2 text-gray-500 hover:text-[#003859] hover:bg-gray-100 rounded-lg transition duration-150 cursor-pointer" title="Exportar Datos">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
                            
                            {{-- Dropdown de Exportación --}}
                            <div x-show="exportOpen"
                                 x-transition
                                 class="absolute right-0 mt-1 w-36 bg-white border border-gray-200 shadow-xl rounded-lg py-1 z-30 text-xs text-gray-700 font-semibold"
                                 style="display: none;"
                            >
                                <button wire:click="descargarCSV" @click="exportOpen = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Exportar CSV
                                </button>
                                <button @click="exportOpen = false; window.print();" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center gap-2 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h8z" />
                                    </svg>
                                    Exportar PDF
                                </button>
                            </div>
                        </div>

                        {{-- Icono de recargar --}}
                        <button wire:click="consultar" class="p-2 text-gray-500 hover:text-[#003859] hover:bg-gray-100 rounded-lg transition duration-150 cursor-pointer" title="Actualizar">
                            <svg wire:loading.class="animate-spin" wire:target="consultar" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 4v5h-5M4 20v-5h5M4 14.8A7 7 0 0 1 11.5 7h.5M20 9.2A7 7 0 0 1 12.5 17h-.5" />
                            </svg>
                        </button>
                    </div>

                    {{-- Contenedor de la Tabla --}}
                    <div class="overflow-x-auto">
                        <table class="print-table min-w-full divide-y divide-gray-200 text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 text-[10px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    <th :class="!visibleCols.includes('vendedor') ? 'hidden hidden-col' : ''" class="px-5 py-4 font-bold text-[#003859] text-left">Vendedor</th>
                                    <th :class="!visibleCols.includes('ventas_netas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Ventas netas</th>
                                    <th :class="!visibleCols.includes('ventas_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Ventas de contado</th>
                                    <th :class="!visibleCols.includes('ventas_credito') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Ventas de crédito</th>
                                    <th :class="!visibleCols.includes('preventas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Preventas</th>
                                    <th :class="!visibleCols.includes('costo_compra_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Costo compra preventa</th>
                                    <th :class="!visibleCols.includes('clientes_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Clientes preventa</th>
                                    <th :class="!visibleCols.includes('entregas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Entregas</th>
                                    <th :class="!visibleCols.includes('costo_producto') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Costo por Producto</th>
                                    <th :class="!visibleCols.includes('devolucion_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Devolución de contado</th>
                                    <th :class="!visibleCols.includes('dias') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Dias</th>
                                    <th :class="!visibleCols.includes('gastos_operativos') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Gastos Operativos</th>
                                    <th :class="!visibleCols.includes('costo_venta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Costo Venta</th>
                                    <th :class="!visibleCols.includes('utilidad_ruta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Utilidad Ruta total</th>
                                    <th :class="!visibleCols.includes('porcentaje') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold text-gray-700">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 bg-white text-xs text-gray-700">
                                @forelse($registrosFiltrados as $reg)
                                    <tr :class="{ 'screen-hidden': !($loop->index >= (page - 1) * perPage && $loop->index < page * perPage) }"
                                        class="hover:bg-gray-50/50 transition duration-150 odd:bg-white even:bg-gray-50/20 @if($loop->iteration % 10 === 0 && !$loop->last) print-page-break @endif"
                                    >
                                        <td :class="!visibleCols.includes('vendedor') ? 'hidden hidden-col' : ''" class="px-5 py-4 font-semibold text-gray-800 border-r border-gray-100">{{ $reg['vendedor'] }}</td>
                                        <td :class="!visibleCols.includes('ventas_netas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-semibold text-gray-900">${{ number_format($reg['ventas_netas'], 2) }}</td>
                                        <td :class="!visibleCols.includes('ventas_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['ventas_contado'], 2) }}</td>
                                        <td :class="!visibleCols.includes('ventas_credito') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['ventas_credito'], 2) }}</td>
                                        <td :class="!visibleCols.includes('preventas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['preventas'], 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_compra_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['costo_compra_preventa'], 2) }}</td>
                                        <td :class="!visibleCols.includes('clientes_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-medium text-gray-500">
                                            {{ is_null($reg['clientes_preventa']) ? '—' : $reg['clientes_preventa'] }}
                                        </td>
                                        <td :class="!visibleCols.includes('entregas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['entregas'], 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_producto') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['costo_producto'], 2) }}</td>
                                        <td :class="!visibleCols.includes('devolucion_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['devolucion_contado'], 2) }}</td>
                                        <td :class="!visibleCols.includes('dias') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-medium text-gray-800">{{ $reg['dias'] }}</td>
                                        <td :class="!visibleCols.includes('gastos_operativos') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['gastos_operativos'], 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_venta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right text-gray-600">${{ number_format($reg['costo_venta'], 2) }}</td>
                                        <td :class="!visibleCols.includes('utilidad_ruta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-semibold text-gray-900">${{ number_format($reg['utilidad_ruta'], 2) }}</td>
                                        <td :class="!visibleCols.includes('porcentaje') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-medium text-gray-800">{{ number_format($reg['porcentaje'], 2) }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td :colspan="visibleCols.length" class="px-6 py-12 text-center text-gray-400 font-medium bg-gray-50/10">
                                            No se encontraron registros de rentabilidad para la búsqueda especificada.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($registrosFiltrados) > 0)
                                <tfoot>
                                    <tr class="bg-[#003859]/5 border-t border-b border-gray-300 font-bold text-xs text-[#003859]">
                                        <td :class="!visibleCols.includes('vendedor') ? 'hidden hidden-col' : ''" class="px-5 py-4 font-bold border-r border-gray-200/50">Total</td>
                                        <td :class="!visibleCols.includes('ventas_netas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('ventas_netas'), 2) }}</td>
                                        <td :class="!visibleCols.includes('ventas_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('ventas_contado'), 2) }}</td>
                                        <td :class="!visibleCols.includes('ventas_credito') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('ventas_credito'), 2) }}</td>
                                        <td :class="!visibleCols.includes('preventas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('preventas'), 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_compra_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('costo_compra_preventa'), 2) }}</td>
                                        <td :class="!visibleCols.includes('clientes_preventa') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">{{ collect($registrosFiltrados)->sum('clientes_preventa') }}</td>
                                        <td :class="!visibleCols.includes('entregas') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('entregas'), 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_producto') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('costo_producto'), 2) }}</td>
                                        <td :class="!visibleCols.includes('devolucion_contado') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('devolucion_contado'), 2) }}</td>
                                        <td :class="!visibleCols.includes('dias') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">{{ collect($registrosFiltrados)->sum('dias') }}</td>
                                        <td :class="!visibleCols.includes('gastos_operativos') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('gastos_operativos'), 2) }}</td>
                                        <td :class="!visibleCols.includes('costo_venta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('costo_venta'), 2) }}</td>
                                        <td :class="!visibleCols.includes('utilidad_ruta') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">${{ number_format(collect($registrosFiltrados)->sum('utilidad_ruta'), 2) }}</td>
                                        <td :class="!visibleCols.includes('porcentaje') ? 'hidden hidden-col' : ''" class="px-4 py-4 text-right font-bold">
                                            @php
                                                $totalVentas = collect($registrosFiltrados)->sum('ventas_netas');
                                                $totalUtilidad = collect($registrosFiltrados)->sum('utilidad_ruta');
                                                $totalPorcentaje = $totalVentas > 0 ? ($totalUtilidad / $totalVentas) * 100 : 0;
                                            @endphp
                                            {{ number_format($totalPorcentaje, 2) }}%
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    {{-- Paginación Reactiva --}}
                    <div class="flex justify-end items-center px-6 py-4 border-t border-gray-150 bg-gray-50/30 text-xs text-gray-500 gap-6 no-print">
                        <div class="flex items-center gap-1">
                            <select x-model.number="perPage" @change="page = 1" class="bg-transparent border-0 focus:ring-0 cursor-pointer text-gray-500 font-semibold py-0.5 pl-0 pr-6 text-xs w-20">
                                <option value="5">5 rows</option>
                                <option value="10">10 rows</option>
                                <option value="20">20 rows</option>
                                <option value="50">50 rows</option>
                                <option value="100">100 rows</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-medium" x-text="((page - 1) * perPage + 1) + '-' + Math.min(page * perPage, {{ count($registrosFiltrados) }}) + ' of ' + {{ count($registrosFiltrados) }}"></span>
                            <div class="flex items-center gap-1.5">
                                <button @click="if (page > 1) page--" 
                                        :disabled="page === 1"
                                        :class="page === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-150 text-gray-600 cursor-pointer'"
                                        class="p-1 rounded text-gray-400 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="if (page < Math.ceil({{ count($registrosFiltrados) }} / perPage)) page++"
                                        :disabled="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage)"
                                        :class="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-150 text-gray-600 cursor-pointer'"
                                        class="p-1 rounded text-gray-400 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            @endif



        </div>
    </div>

    <style>
        @keyframes progress-loading {
            0% { left: -30%; }
            100% { left: 100%; }
        }
        .animate-progress-loading {
            animation: progress-loading 1.2s infinite linear;
        }

        .screen-hidden {
            display: none !important;
        }

        @media print {
            @page {
                size: landscape;
                margin: 0.8cm;
            }

            /* High specificity rules to force-hide components */
            body nav,
            body div[class*="bg-[#003859]"],
            body div[class*="border-[#002b45]"],
            body .no-print, 
            body header,
            body .bg-white.rounded-lg.shadow-sm, 
            body .flex.items-center.text-xs.text-gray-500.mb-6, 
            body .flex.justify-end.items-center.px-6.py-4,
            body div[class*="flex"][class*="justify-end"][class*="items-center"],
            body div[class*="border-t"][class*="border-gray-150"]
            {
                display: none !important;
            }

            /* Clean resets for parent containers without breaking content flow */
            body, 
            html, 
            .h-screen,
            div.h-screen.flex,
            div.flex.flex-1.overflow-hidden,
            div.flex-1.flex.flex-col,
            div[class*="min-w-0"],
            div[class*="overflow-y-auto"],
            main,
            .py-4,
            .max-w-\[1400px\] {
                height: auto !important;
                overflow: visible !important;
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                display: block !important;
                box-shadow: none !important;
                border: none !important;
            }

            .bg-white.rounded-xl.shadow-md {
                box-shadow: none !important;
                border: none !important;
                background: white !important;
                overflow: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            table.print-table {
                display: table !important;
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 15px !important;
                table-layout: fixed !important; /* Force columns to distribute evenly */
            }
            
            table.print-table tr,
            table.print-table tr.screen-hidden {
                display: table-row !important;
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }

            /* Hide columns marked as hidden-col in print mode */
            table.print-table th.hidden-col, 
            table.print-table td.hidden-col {
                display: none !important;
            }

            table.print-table th, 
            table.print-table td {
                display: table-cell !important;
                border: 1px solid #cbd5e1 !important; /* Borde gris suave idéntico a PDF */
                padding: 5px 3px !important;
                font-size: 6.5px !important;
                line-height: 1.25 !important;
                color: black !important;
                background: transparent !important;
                white-space: normal !important;
                word-break: break-word !important;
                text-align: center !important; /* Centrar todas las columnas por defecto */
            }
            
            table.print-table th {
                font-weight: bold !important;
                text-transform: uppercase !important;
                background-color: #2e75b6 !important; /* Cabecera Azul idéntica a la imagen */
                color: white !important; /* Texto blanco */
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.print-table th:first-child, 
            table.print-table td:first-child {
                text-align: left !important;
                font-weight: bold !important;
                width: 110px !important; /* Ancho fijo para el Vendedor */
                max-width: 110px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            table.print-table tfoot tr {
                background-color: #e2e8f0 !important;
                font-weight: bold !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table.print-table tfoot td {
                color: #003859 !important;
                font-weight: bold !important;
                text-align: center !important;
            }

            table.print-table tfoot td:first-child {
                text-align: left !important;
            }
        }
    </style>

    {{-- Drawer de Columnas --}}
    <div x-show="colDropdownOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl border-l border-gray-200 z-50 flex flex-col no-print"
         style="display: none;"
    >
        {{-- Header del Drawer --}}
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-sm font-bold text-gray-800">Agregar o quitar columnas</h3>
            <button @click="colDropdownOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Contenido del Drawer --}}
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-2">
            @php
                $columnasDisponibles = [
                    ['key' => 'vendedor', 'label' => 'Vendedor'],
                    ['key' => 'ventas_netas', 'label' => 'Ventas netas'],
                    ['key' => 'ventas_contado', 'label' => 'Ventas de contado'],
                    ['key' => 'ventas_credito', 'label' => 'Ventas de crédito'],
                    ['key' => 'preventas', 'label' => 'Preventas'],
                    ['key' => 'costo_compra_preventa', 'label' => 'Costo compra preventa'],
                    ['key' => 'clientes_preventa', 'label' => 'Clientes preventa'],
                    ['key' => 'entregas', 'label' => 'Entregas'],
                    ['key' => 'costo_producto', 'label' => 'Costo por Producto'],
                    ['key' => 'devolucion_contado', 'label' => 'Devolución de contado'],
                    ['key' => 'dias', 'label' => 'Días'],
                    ['key' => 'gastos_operativos', 'label' => 'Gastos Operativos'],
                    ['key' => 'costo_venta', 'label' => 'Costo Venta'],
                    ['key' => 'utilidad_ruta', 'label' => 'Utilidad Ruta total'],
                    ['key' => 'porcentaje', 'label' => 'Porcentaje'],
                ];
            @endphp

            @foreach($columnasDisponibles as $col)
                <label class="flex items-center gap-3 py-2 cursor-pointer select-none group">
                    <input type="checkbox"
                           value="{{ $col['key'] }}"
                           x-model="visibleCols"
                           class="w-4 h-4 text-[#003859] border-gray-300 rounded focus:ring-[#003859] cursor-pointer"
                    />
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ $col['label'] }}</span>
                </label>
            @endforeach
        </div>
    </div>
    
    {{-- Overlay cuando el Drawer está abierto --}}
    <div x-show="colDropdownOpen" 
         @click="colDropdownOpen = false"
         class="fixed inset-0 bg-black/20 z-40 no-print" 
         x-transition
         style="display: none;">
    </div>
</div>
