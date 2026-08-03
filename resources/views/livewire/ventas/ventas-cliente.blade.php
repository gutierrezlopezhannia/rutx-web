<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

// Filtrado inicial basado en los valores predeterminados de base de datos
$iniciales = \App\Models\Invoice::with(['customer', 'seller', 'zone'])
    ->where('zona_id', '99-PRUEBA')
    ->where('vendedor_id', '999001 - VENDEDOR PRUEBA')
    ->where('customer_id', '999001 - CLIENTE PRUEBA 01')
    ->whereDate('fecha', '2018-09-19')
    ->orderByDesc('fecha')
    ->orderByDesc('id')
    ->get()
    ->map(fn($inv) => [
        'folio' => $inv->folio,
        'movimiento' => $inv->movimiento,
        'fecha' => $inv->fecha,
        'vendedor' => $inv->vendedor_id,
        'zona' => $inv->zona_id,
        'cliente_codigo' => $inv->customer->clave ?? '',
        'cliente_nombre' => $inv->customer->nombre ?? '',
        'cliente' => $inv->customer_id,
        'subtotal' => $inv->subtotal,
        'total' => $inv->total,
        'comentario' => $inv->comentario,
    ])
    ->toArray();

state([
    'registrosFiltrados' => $iniciales,
    
    // Filtros de la UI (según captura de pantalla)
    'filtro_zona' => '99-PRUEBA',       
    'filtro_vendedor' => '999001 - VENDEDOR PRUEBA', 
    'filtro_cliente' => '999001 - CLIENTE PRUEBA 01', 
    'fecha_inicio' => '2018-09-19',       
    'fecha_fin' => '2018-09-19',          
    
    // Búsqueda
    'search' => '',
    
    // Paginación
    'filas_por_pagina' => 100,
    
    // Estatus de modal
    'mostrarModalDetalle' => false,
    'registroSeleccionado' => null,
]);

$updatedFiltroZona = function() {
    $this->filtro_vendedor = 'todos';
    $this->filtro_cliente = 'todos';
};

$obtenerVendedores = function() {
    if ($this->filtro_zona === 'todos') {
        return \App\Models\Seller::where('oculto', 'N')->pluck('id')->sort()->toArray();
    }
    
    $vendedorIds = \App\Models\Invoice::where('zona_id', $this->filtro_zona)
        ->pluck('vendedor_id')
        ->unique();
        
    return \App\Models\Seller::whereIn('id', $vendedorIds)
        ->where('oculto', 'N')
        ->pluck('id')
        ->sort()
        ->toArray();
};

$obtenerClientes = function() {
    if ($this->filtro_zona === 'todos') {
        return \App\Models\Customer::pluck('id')->sort()->toArray();
    }
    return \App\Models\Customer::where('zona_id', $this->filtro_zona)->pluck('id')->sort()->toArray();
};

$aplicarFiltros = function () {
    $query = \App\Models\Invoice::with(['customer', 'seller', 'zone']);
    
    // Filtrar por Zona
    if ($this->filtro_zona !== 'todos') {
        $query->where('zona_id', $this->filtro_zona);
    }
    
    // Filtrar por Vendedor
    if ($this->filtro_vendedor !== 'todos') {
        $query->where('vendedor_id', $this->filtro_vendedor);
    }
    
    // Filtrar por Cliente
    if ($this->filtro_cliente !== 'todos') {
        $query->where('customer_id', $this->filtro_cliente);
    }
    
    // Filtrar por Rango de Fechas
    if (!empty($this->fecha_inicio)) {
        $query->where('fecha', '>=', $this->fecha_inicio);
    }
    
    if (!empty($this->fecha_fin)) {
        $query->where('fecha', '<=', $this->fecha_fin);
    }
    
    // Filtrar por Búsqueda
    if (!empty($this->search)) {
        $searchQuery = '%' . $this->search . '%';
        $query->where(function ($q) use ($searchQuery) {
            $q->where('folio', 'like', $searchQuery)
              ->orWhere('movimiento', 'like', $searchQuery)
              ->orWhere('vendedor_id', 'like', $searchQuery)
              ->orWhere('comentario', 'like', $searchQuery);
        });
    }
    
    $this->registrosFiltrados = $query->orderByDesc('fecha')
        ->orderByDesc('id')
        ->get()
        ->map(fn($inv) => [
            'folio' => $inv->folio,
            'movimiento' => $inv->movimiento,
            'fecha' => $inv->fecha,
            'vendedor' => $inv->vendedor_id,
            'zona' => $inv->zona_id,
            'cliente_codigo' => $inv->customer->clave ?? '',
            'cliente_nombre' => $inv->customer->nombre ?? '',
            'cliente' => $inv->customer_id,
            'subtotal' => $inv->subtotal,
            'total' => $inv->total,
            'comentario' => $inv->comentario,
        ])
        ->toArray();
};

// Listeners de actualización de variables reactivas
$updatedFiltroZona = function () { $this->aplicarFiltros(); };
$updatedFiltroVendedor = function () { $this->aplicarFiltros(); };
$updatedFiltroCliente = function () { $this->aplicarFiltros(); };
$updatedFechaInicio = function () { $this->aplicarFiltros(); };
$updatedFechaFin = function () { $this->aplicarFiltros(); };
$updatedSearch = function () { $this->aplicarFiltros(); };

$limpiarBusqueda = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

$refrescarReporte = function () {
    (new \Database\Seeders\PruebaSeeder())->run();
    $this->aplicarFiltros();
    session()->flash('mensaje_exito', 'Reporte recargado correctamente.');
};

$descargarCSV = function () {
    session()->flash('mensaje_exito', 'Descarga de reporte en formato CSV iniciada.');
};

$verDetalle = function ($folio) {
    $inv = \App\Models\Invoice::with('customer')->where('folio', $folio)->first();
    if ($inv) {
        $this->registroSeleccionado = [
            'folio' => $inv->folio,
            'movimiento' => $inv->movimiento,
            'fecha' => $inv->fecha,
            'vendedor' => $inv->vendedor_id,
            'zona' => $inv->zona_id,
            'cliente_codigo' => $inv->customer->clave ?? '',
            'cliente_nombre' => $inv->customer->nombre ?? '',
            'cliente' => $inv->customer_id,
            'subtotal' => $inv->subtotal,
            'total' => $inv->total,
            'comentario' => $inv->comentario,
        ];
        $this->mostrarModalDetalle = true;
    }
};

$cerrarModal = function () {
    $this->mostrarModalDetalle = false;
    $this->registroSeleccionado = null;
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-700 transition duration-150">Cpanel</a>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500 font-medium">Reporte de Ventas por Cliente</span>
            </div>

            {{-- Alertas flotantes --}}
            @if (session()->has('mensaje_exito'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg flex items-center justify-between shadow-sm">
                    <span>{{ session('mensaje_exito') }}</span>
                    <button class="text-green-500 hover:text-green-700 font-bold focus:outline-none" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            {{-- Main Container Card --}}
            <div class="bg-white rounded-sm shadow-sm border border-gray-200/80 p-6">

                {{-- Título --}}
                <h2 class="text-lg font-bold text-[#1f2937] mb-6">Reporte de ventas por Cliente</h2>

                {{-- Sección de Filtros (Estilo Underline exactamente como en la captura) --}}
                <div class="flex flex-col lg:flex-row lg:items-end gap-6 mb-6 w-full">
                    
                    {{-- Filtro Zona --}}
                    <div class="flex flex-col w-full lg:w-[15%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-medium cursor-pointer w-full">
                            <option value="todos">Todas las Zonas</option>
                            @foreach(\App\Models\Zone::pluck('id')->sort() as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro Vendedor --}}
                    <div class="flex flex-col w-full lg:w-[22%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Vendedor</label>
                        <select wire:model.live="filtro_vendedor" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-medium cursor-pointer w-full">
                            <option value="todos">Todos los Vendedores</option>
                            @foreach($this->obtenerVendedores() as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro Cliente --}}
                    <div class="flex flex-col w-full lg:w-[35%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Cliente</label>
                        <select wire:model.live="filtro_cliente" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-medium cursor-pointer w-full">
                            <option value="todos">Todos los Clientes</option>
                            @foreach($this->obtenerClientes() as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-full lg:w-[14%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Fecha inicial</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model.live="fecha_inicio" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-medium text-sm w-full cursor-pointer" />
                            <svg class="w-4 h-4 text-gray-400 shrink-0 ml-1 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full lg:w-[14%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Fecha final</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model.live="fecha_fin" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-medium text-sm w-full cursor-pointer" />
                            <svg class="w-4 h-4 text-gray-400 shrink-0 ml-1 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                </div>

                {{-- Action Icons & Search Bar on the Right --}}
                <div class="flex justify-end items-center mb-4 gap-3">
                    
                    {{-- Buscador --}}
                    <div class="flex items-center border-0 border-b border-gray-300 rounded-none py-1 w-64">
                        <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live="search" placeholder="Buscar ..." class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 text-xs placeholder-gray-400" />
                        
                        @if(!empty($search))
                            <button wire:click="limpiarBusqueda" class="text-gray-400 hover:text-gray-600 ml-1.5 focus:outline-none cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Línea Vertical Divisora --}}
                    <span class="border-l border-gray-200 h-5 my-1"></span>

                    {{-- Configuración de Columnas --}}
                    <button class="text-gray-500 hover:text-[#004f7c] p-1.5 rounded transition duration-150 focus:outline-none cursor-pointer" title="Configurar Columnas">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Exportar / Descargar --}}
                    <button wire:click="descargarCSV" class="text-gray-500 hover:text-[#004f7c] p-1.5 rounded transition duration-150 focus:outline-none cursor-pointer" title="Descargar CSV">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>

                    {{-- Refrescar --}}
                    <button wire:click="refrescarReporte" class="text-gray-500 hover:text-[#004f7c] p-1.5 rounded transition duration-150 focus:outline-none cursor-pointer" title="Refrescar Datos">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                        </svg>
                    </button>

                </div>

                {{-- Tabla de Movimientos --}}
                @if(!empty($registrosFiltrados))
                    <div class="overflow-x-auto border border-gray-150 rounded-sm">
                        <table class="min-w-full text-left text-xs text-gray-600 border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 text-gray-500 font-bold border-b border-gray-200">
                                    <th class="px-4 py-3 font-semibold">Acciones</th>
                                    <th class="px-4 py-3 font-semibold">Folio</th>
                                    <th class="px-4 py-3 font-semibold">Movimiento</th>
                                    <th class="px-4 py-3 font-semibold">Fecha</th>
                                    <th class="px-4 py-3 font-semibold">Vendedor</th>
                                    <th class="px-4 py-3 text-right font-semibold">Subtotal</th>
                                    <th class="px-4 py-3 text-right font-semibold">Total</th>
                                    <th class="px-4 py-3 font-semibold">Comentario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($registrosFiltrados as $registro)
                                    <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                        {{-- Acciones --}}
                                        <td class="px-4 py-3 font-medium">
                                            <button wire:click="verDetalle('{{ $registro['folio'] }}')" class="p-1 hover:bg-[#004f7c]/10 text-gray-400 hover:text-[#004f7c] rounded transition duration-150 focus:outline-none cursor-pointer" title="Ver Detalle">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                        {{-- Folio --}}
                                        <td class="px-4 py-3 font-mono font-medium text-gray-900">{{ $registro['folio'] }}</td>
                                        {{-- Movimiento --}}
                                        <td class="px-4 py-3 font-medium text-gray-700">{{ $registro['movimiento'] }}</td>
                                        {{-- Fecha --}}
                                        <td class="px-4 py-3 text-gray-500 font-mono">
                                            {{ \Carbon\Carbon::parse($registro['fecha'])->format('d/m/Y') }}
                                        </td>
                                        {{-- Vendedor --}}
                                        <td class="px-4 py-3 text-gray-700 font-medium">{{ $registro['vendedor'] }}</td>
                                        {{-- Subtotal --}}
                                        <td class="px-4 py-3 text-right font-mono font-semibold">${{ number_format($registro['subtotal'], 2) }}</td>
                                        {{-- Total --}}
                                        <td class="px-4 py-3 text-right font-mono font-semibold text-gray-900">${{ number_format($registro['total'], 2) }}</td>
                                        {{-- Comentario --}}
                                        <td class="px-4 py-3 text-gray-500 italic max-w-xs truncate" title="{{ $registro['comentario'] }}">
                                            {{ $registro['comentario'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                            No hay Registros para mostrar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabla de Paginación --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-4 text-xs text-gray-500 font-medium">
                        <div class="flex items-center gap-2">
                            <span>Filas por Página</span>
                            <select wire:model.live="filas_por_pagina" class="border border-gray-200 rounded px-1.5 py-0.5 text-xs bg-white text-gray-600 focus:outline-none focus:border-[#003859]">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <span class="border-l border-gray-200 h-4 hidden sm:block"></span>
                        <div class="flex items-center gap-1.5 font-mono">
                            {{-- Botón Primero --}}
                            <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>
                                |<
                            </button>
                            {{-- Botón Anterior --}}
                            <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>
                                <
                            </button>
                            {{-- Indicador de Rango --}}
                            <span class="px-1 text-gray-600 font-semibold">
                                @if(count($registrosFiltrados) > 0)
                                    1-{{ min(count($registrosFiltrados), $filas_por_pagina) }} of {{ count($registrosFiltrados) }}
                                @else
                                    0-0 of 0
                                @endif
                            </span>
                            {{-- Botón Siguiente --}}
                            <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>
                                >
                            </button>
                            {{-- Botón Último --}}
                            <button class="p-1 hover:bg-gray-100 rounded text-gray-400 hover:text-gray-700 focus:outline-none cursor-not-allowed" disabled>
                                >|
                            </button>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Footer de Copyright centrado --}}
            <div class="mt-8 text-center text-xs text-gray-400 font-medium">
                Copyright © JB VEMOBILE SA DE CV 2026.
            </div>

        </div>
    </div>

    {{-- Modal Detallado de Transacción --}}
    @if($mostrarModalDetalle && $registroSeleccionado)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity duration-200">
            <div class="bg-white rounded-sm shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden transform transition-all scale-100">
                
                {{-- Modal Header --}}
                <div class="bg-[#003859] px-6 py-4 flex items-center justify-between text-white">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-blue-200 font-mono">Folio: {{ $registroSeleccionado['folio'] }}</span>
                        <h3 class="text-base font-bold mt-0.5">Detalle del Movimiento</h3>
                    </div>
                    <button wire:click="cerrarModal" class="text-gray-300 hover:text-white hover:bg-white/10 p-1.5 rounded transition duration-150 focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4 text-sm text-gray-600">
                    
                    <div class="grid grid-cols-2 gap-4 pb-3 border-b border-gray-100">
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Cliente</span>
                            <span class="font-bold text-gray-800">{{ $registroSeleccionado['cliente'] }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Estatus Zona</span>
                            <span class="font-bold text-gray-800">{{ $registroSeleccionado['zona'] }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pb-3 border-b border-gray-100">
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Tipo Movimiento</span>
                            <span class="font-semibold text-[#004f7c]">{{ $registroSeleccionado['movimiento'] }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Fecha Operación</span>
                            <span class="font-mono text-gray-800">{{ \Carbon\Carbon::parse($registroSeleccionado['fecha'])->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pb-3 border-b border-gray-100">
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Vendedor</span>
                            <span class="font-medium text-gray-800">{{ $registroSeleccionado['vendedor'] }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Impuestos (16% IVA)</span>
                            <span class="font-mono text-gray-800">${{ number_format($registroSeleccionado['total'] - $registroSeleccionado['subtotal'], 2) }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-4 grid grid-cols-2 gap-4 items-center">
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Subtotal</span>
                            <span class="text-sm font-semibold font-mono text-gray-700">${{ number_format($registroSeleccionado['subtotal'], 2) }}</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
                            <span class="text-lg font-bold font-mono text-gray-900">${{ number_format($registroSeleccionado['total'], 2) }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Comentario Administrativo</span>
                        <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded italic border-l-2 border-gray-300">
                            {{ $registroSeleccionado['comentario'] }}
                        </p>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                    <button wire:click="cerrarModal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg transition-colors duration-150 focus:outline-none cursor-pointer">
                        Cerrar Detalle
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
