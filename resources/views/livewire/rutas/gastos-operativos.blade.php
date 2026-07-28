<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockRutas = [
    ['clave' => '3983', 'nombre' => 'RUTA01', 'estatus' => 'Activo', 'zona' => '1Z - Zona 1'],
    ['clave' => '4682', 'nombre' => 'RUTA02', 'estatus' => 'Activo', 'zona' => '1Z - Zona 1'],
    ['clave' => '4683', 'nombre' => 'RUTA03', 'estatus' => 'Activo', 'zona' => '1Z - Zona 1'],
    ['clave' => '4684', 'nombre' => 'RUTA04', 'estatus' => 'Activo', 'zona' => '2Z - Zona 2'],
    ['clave' => '4685', 'nombre' => 'RUTA05', 'estatus' => 'Activo', 'zona' => '2Z - Zona 2'],
    ['clave' => '4686', 'nombre' => 'RUTA06', 'estatus' => 'Activo', 'zona' => '3Z - Zona 3'],
];

state([
    // Catálogos y Datos base
    'rutas' => $mockRutas,
    'rutasFiltradas' => $mockRutas,
    
    // Filtros
    'filtro_zona' => '1Z - Zona 1',
    'search' => '',

    // Columnas visibles de la tabla
    'col_acciones' => true,
    'col_clave' => true,
    'col_nombre' => true,
    'col_estatus' => true,
    'col_zona' => true,

    // Modal
    'showGastoModal' => false,
    'selectedRuta' => null,

    // Campos del Formulario
    'formConcepto' => 'Combustible',
    'formMonto' => '',
    'formFecha' => fn() => date('Y-m-d'),
    'formComprobante' => 'Factura',
    'formReferencia' => '',
    'formObservaciones' => '',

    // Historial
    'gastosRegistrados' => [],

    // Notificaciones
    'notification' => '',
    'notificationType' => 'success', // success, error, info, delete
]);

$init = function () {
    // Si no existen gastos operativos en la sesión, inicializamos mock data realista
    if (!session()->has('gastos_operativos')) {
        $defaultGastos = [
            [
                'ruta_clave' => '3983',
                'ruta_nombre' => 'RUTA01',
                'concepto' => 'Combustible',
                'monto' => 1250.00,
                'fecha' => date('Y-m-d'),
                'comprobante' => 'Factura',
                'referencia' => 'TKT-38291',
                'observaciones' => 'Carga de diesel para inicio de ruta'
            ],
            [
                'ruta_clave' => '4683',
                'ruta_nombre' => 'RUTA03',
                'concepto' => 'Casetas',
                'monto' => 420.00,
                'fecha' => date('Y-m-d', strtotime('-1 days')),
                'comprobante' => 'Nota/Simplificado',
                'referencia' => 'PEAJE-88273',
                'observaciones' => 'Peaje autopista de cuota norte'
            ],
            [
                'ruta_clave' => '4682',
                'ruta_nombre' => 'RUTA02',
                'concepto' => 'Viáticos',
                'monto' => 180.00,
                'fecha' => date('Y-m-d'),
                'comprobante' => 'Sin Comprobante',
                'referencia' => 'ALIM-122',
                'observaciones' => 'Almuerzo del chófer de reparto'
            ]
        ];
        session(['gastos_operativos' => $defaultGastos]);
    }

    $this->gastosRegistrados = session('gastos_operativos');
    $this->aplicarFiltros();
};

$aplicarFiltros = function () {
    $filtradas = collect($this->rutas);

    if ($this->filtro_zona !== 'todos') {
        $filtradas = $filtradas->where('zona', $this->filtro_zona);
    }

    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtradas = $filtradas->filter(function ($r) use ($q) {
            return str_contains(strtolower($r['clave']), $q) || 
                   str_contains(strtolower($r['nombre']), $q) || 
                   str_contains(strtolower($r['zona']), $q);
        });
    }

    $this->rutasFiltradas = $filtradas->values()->toArray();
};

$updatedFiltroZona = function () {
    $this->aplicarFiltros();
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};

$clearSearch = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

$actualizar = function () {
    $this->aplicarFiltros();
    $this->gastosRegistrados = session('gastos_operativos', []);
    $this->triggerNotification('Tabla de rutas y gastos sincronizados.', 'info');
};

$abrirNuevoGasto = function ($clave) {
    $ruta = collect($this->rutas)->firstWhere('clave', $clave);
    if ($ruta) {
        $this->selectedRuta = $ruta;
        
        // Reset form to defaults
        $this->formConcepto = 'Combustible';
        $this->formMonto = '';
        $this->formFecha = date('Y-m-d');
        $this->formComprobante = 'Factura';
        $this->formReferencia = '';
        $this->formObservaciones = '';

        $this->showGastoModal = true;
    }
};

$guardarGasto = function () {
    // Validaciones básicas
    if (empty($this->formMonto) || !is_numeric($this->formMonto) || floatval($this->formMonto) <= 0) {
        $this->triggerNotification('El monto debe ser un número mayor a 0.', 'error');
        return;
    }

    if (empty($this->formConcepto)) {
        $this->triggerNotification('El concepto es obligatorio.', 'error');
        return;
    }

    $nuevoGasto = [
        'ruta_clave' => $this->selectedRuta['clave'],
        'ruta_nombre' => $this->selectedRuta['nombre'],
        'concepto' => $this->formConcepto,
        'monto' => floatval($this->formMonto),
        'fecha' => $this->formFecha ?: date('Y-m-d'),
        'comprobante' => $this->formComprobante,
        'referencia' => trim($this->formReferencia),
        'observaciones' => trim($this->formObservaciones)
    ];

    // Obtener gastos de sesión y guardar el nuevo
    $gastos = session('gastos_operativos', []);
    array_unshift($gastos, $nuevoGasto); // Agrega al inicio
    session(['gastos_operativos' => $gastos]);

    $this->gastosRegistrados = $gastos;
    $this->showGastoModal = false;
    $this->triggerNotification('Gasto registrado con éxito en ' . $this->selectedRuta['nombre'] . '.', 'success');
};

$eliminarGasto = function ($index) {
    $gastos = session('gastos_operativos', []);
    if (isset($gastos[$index])) {
        $rutaNombre = $gastos[$index]['ruta_nombre'];
        $monto = $gastos[$index]['monto'];
        unset($gastos[$index]);
        $gastos = array_values($gastos); // reindexar
        session(['gastos_operativos' => $gastos]);
        
        $this->gastosRegistrados = $gastos;
        $this->triggerNotification('Se eliminó el gasto de $' . number_format($monto, 2) . ' de la ' . $rutaNombre . '.', 'delete');
    }
};

$triggerNotification = function ($msg, $type = 'success') {
    $this->notification = $msg;
    $this->notificationType = $type;
};

?>

<div class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4 overflow-y-auto">
    <div class="w-full px-6 flex flex-col flex-1 pb-10">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1">
            <span>Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span>Ruta</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Gastos Operativos</span>
        </div>

        {{-- Header & Toast --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Gastos Operativos en Ruta</h1>
                <p class="text-xs text-gray-450 dark:text-gray-400 mt-1">Registra y administra los egresos de combustible, casetas, y alimentos generados en tránsito.</p>
            </div>
            
            {{-- Toast Notification --}}
            @if ($notification)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('notification', '') }, 4000)"
                    :class="{
                        'bg-green-50 text-green-800 border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30': '{{ $notificationType }}' === 'success',
                        'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30': '{{ $notificationType }}' === 'error',
                        'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30': '{{ $notificationType }}' === 'info',
                        'bg-orange-50 text-orange-800 border-orange-200 dark:bg-orange-950/20 dark:text-orange-400 dark:border-orange-900/30': '{{ $notificationType }}' === 'delete',
                    }"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm shadow-sm transition-all duration-300 animate-fade-in">
                    <span>
                        @if ($notificationType === 'success')
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        @elseif ($notificationType === 'error')
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        @elseif ($notificationType === 'info')
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        @endif
                    </span>
                    <span class="font-medium">{{ $notification }}</span>
                </div>
            @endif
        </div>

        {{-- Dos columnas principales --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
            
            {{-- Columna 1 y 2: Unidades y registro (Tabla) --}}
            <div class="xl:col-span-2 bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">Unidades de Reparto en Ruta</h2>
                    <span class="text-[11px] bg-blue-50 dark:bg-blue-900/30 text-[#003859] dark:text-blue-400 px-2 py-0.5 rounded-full font-bold">
                        {{ count($rutasFiltradas) }} Rutas
                    </span>
                </div>

                {{-- Toolbar: Filtros + Buscar + Iconos --}}
                <div class="px-5 py-4 bg-gray-50/50 dark:bg-gray-800/40 flex flex-col lg:flex-row lg:items-center gap-3 border-b border-gray-100 dark:border-gray-700/50" x-data="{ showColumnas: false }">
                    
                    {{-- Filtro Zona --}}
                    <div class="relative w-full lg:w-48">
                        <label class="absolute -top-2 left-2 px-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800 tracking-wider">ZONA</label>
                        <select wire:model.live="filtro_zona"
                            class="appearance-none border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold cursor-pointer w-full shadow-sm">
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                            <option value="todos">Todas las Zonas</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Spacer --}}
                    <div class="flex-grow"></div>

                    {{-- Buscar --}}
                    <div class="relative w-full lg:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar ..."
                            class="w-full pl-9 pr-9 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-medium placeholder-gray-400 shadow-sm" />
                        @if (!empty($search))
                            <button wire:click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Iconos de acción --}}
                    <div class="flex items-center gap-2">
                        
                        {{-- Ver Columnas --}}
                        <div class="relative">
                            <button @click="showColumnas = !showColumnas" title="Ver Columnas"
                                class="p-1.5 border border-gray-255 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z" />
                                </svg>
                            </button>
                            <div x-show="showColumnas" @click.outside="showColumnas = false" x-cloak
                                class="absolute right-0 top-10 z-50 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 w-44">
                                <p class="text-[10px] font-bold text-gray-450 dark:text-gray-555 uppercase tracking-wider mb-2">Columnas</p>
                                <div class="space-y-1.5">
                                    @foreach ([['col_acciones', 'Acciones'], ['col_clave', 'Clave'], ['col_nombre', 'Nombre'], ['col_estatus', 'Estatus'], ['col_zona', 'Zona']] as [$field, $label])
                                        <label class="flex items-center gap-2 py-0.5 rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 text-[11px] text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" wire:model.live="{{ $field }}"
                                                class="w-3.5 h-3.5 text-[#003859] dark:text-blue-600 rounded border-gray-300 dark:border-gray-700 focus:ring-[#003859]" />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Exportar mock --}}
                        <button wire:click="triggerNotification('Exportación en formato CSV iniciada.', 'info')" title="Descargar reporte"
                            class="p-1.5 border border-gray-255 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>

                        {{-- Actualizar --}}
                        <button wire:click="actualizar" title="Actualizar"
                            class="p-1.5 border border-gray-255 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>

                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-left border-collapse">
                        <thead class="bg-[#f8fafc] dark:bg-gray-800/80 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider select-none">
                            <tr>
                                @if ($col_acciones)
                                    <th scope="col" class="px-6 py-4 w-24">Acciones</th>
                                @endif
                                @if ($col_clave)
                                    <th scope="col" class="px-6 py-4 w-32">Clave</th>
                                @endif
                                @if ($col_nombre)
                                    <th scope="col" class="px-6 py-4">Nombre</th>
                                @endif
                                @if ($col_estatus)
                                    <th scope="col" class="px-6 py-4 w-32">Estatus</th>
                                @endif
                                @if ($col_zona)
                                    <th scope="col" class="px-6 py-4 w-44">Zona</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 text-xs text-gray-700 dark:text-gray-300">
                            @forelse($rutasFiltradas as $ruta)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                    @if ($col_acciones)
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <button wire:click="abrirNuevoGasto('{{ $ruta['clave'] }}')"
                                                class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-all duration-150 cursor-pointer flex items-center justify-center"
                                                title="Registrar nuevo gasto operativo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                    @if ($col_clave)
                                        <td class="px-6 py-3.5 whitespace-nowrap font-bold text-gray-850 dark:text-gray-200">
                                            {{ $ruta['clave'] }}
                                        </td>
                                    @endif
                                    @if ($col_nombre)
                                        <td class="px-6 py-3.5 font-bold text-[#003859] dark:text-blue-400">
                                            {{ $ruta['nombre'] }}
                                        </td>
                                    @endif
                                    @if ($col_estatus)
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-150 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30">
                                                {{ $ruta['estatus'] }}
                                            </span>
                                        </td>
                                    @endif
                                    @if ($col_zona)
                                        <td class="px-6 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ $ruta['zona'] }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 dark:text-gray-550 select-none">
                                        No se encontraron rutas con los criterios de búsqueda especificados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/80 bg-[#f8fafc]/50 dark:bg-gray-800/40 flex justify-between items-center text-xs text-gray-500 dark:text-gray-450 select-none">
                    <div class="flex items-center gap-1.5">
                        <span>100 Filas por Página</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 cursor-pointer" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-medium">1-{{ count($rutasFiltradas) }} of {{ count($rutasFiltradas) }}</span>
                        <div class="flex items-center gap-1">
                            <button class="p-1 text-gray-300 dark:text-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="p-1 text-gray-300 dark:text-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 cursor-not-allowed" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna 3: Historial y Sumario (Sidebar derecho / Historial) --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-800/40">
                    <h2 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider">Historial de Gastos</h2>
                    <p class="text-[11px] text-gray-400 dark:text-gray-400 mt-1">Historial del día en curso (persistencia temporal en sesión).</p>
                    
                    {{-- Total gastado del día --}}
                    <div class="mt-4 bg-[#003859] dark:bg-blue-950/40 border border-[#002d48] dark:border-blue-900/30 p-4 rounded-xl text-white flex justify-between items-center shadow-inner">
                        <div>
                            <div class="text-[10px] font-bold text-blue-200 uppercase tracking-widest">Total Reportado</div>
                            <div class="text-2xl font-extrabold mt-0.5 tracking-tight text-white dark:text-blue-300">
                                ${{ number_format(collect($gastosRegistrados)->sum('monto'), 2) }}
                            </div>
                        </div>
                        <div class="p-2.5 bg-white/10 dark:bg-blue-800/30 rounded-lg">
                            <svg class="w-6 h-6 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Listado de Gastos --}}
                <div class="p-5 flex-1 max-h-[500px] overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/80">
                    @forelse($gastosRegistrados as $idx => $gasto)
                        <div class="py-4 first:pt-0 last:pb-0 group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-extrabold text-[#003859] dark:text-blue-400">
                                            {{ $gasto['ruta_nombre'] }}
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                            {{ $gasto['concepto'] }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5 font-medium">
                                        {{ date('d M, Y', strtotime($gasto['fecha'])) }} @if($gasto['referencia']) • Ref: {{ $gasto['referencia'] }} @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-right">
                                        <span class="text-xs font-bold text-gray-800 dark:text-gray-100">
                                            ${{ number_format($gasto['monto'], 2) }}
                                        </span>
                                        <div class="text-[8px] text-gray-400 font-bold uppercase">{{ $gasto['comprobante'] }}</div>
                                    </div>
                                    <button wire:click="eliminarGasto({{ $idx }})"
                                        class="p-1 text-gray-355 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 rounded transition-all duration-150 cursor-pointer"
                                        title="Eliminar gasto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @if($gasto['observaciones'])
                                <div class="mt-1.5 text-[10px] text-gray-500 dark:text-gray-400 italic bg-gray-50/70 dark:bg-gray-900/40 p-1.5 rounded border border-gray-100 dark:border-gray-800/80">
                                    "{{ $gasto['observaciones'] }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-400 dark:text-gray-550 select-none text-xs">
                            No hay gastos operativos registrados para hoy.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    {{-- Modal: Registro de Gasto Operativo --}}
    @if ($showGastoModal && $selectedRuta)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity backdrop-blur-[2px]"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md animate-scale-up border border-gray-100 dark:border-gray-700">
                    
                    {{-- Header Modal --}}
                    <div class="bg-[#003859] dark:bg-gray-800 px-6 py-4 flex justify-between items-center text-white border-b border-[#002d48] dark:border-gray-700">
                        <div>
                            <h3 class="text-sm font-bold tracking-wide uppercase text-white" id="modal-title">
                                Nuevo Gasto Operativo
                            </h3>
                            <div class="text-[10px] text-blue-200 dark:text-gray-450 mt-0.5">REGISTRO PARA {{ $selectedRuta['nombre'] }} ({{ $selectedRuta['clave'] }})</div>
                        </div>
                        <button wire:click="$set('showGastoModal', false)" class="text-blue-100 hover:text-white dark:text-gray-400 dark:hover:text-white transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form body --}}
                    <div class="px-6 py-5 space-y-4 text-xs">
                        
                        {{-- Concepto --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-450 dark:text-gray-400 uppercase tracking-wider mb-1.5">Concepto / Categoría</label>
                            <div class="relative">
                                <select wire:model="formConcepto"
                                    class="w-full appearance-none bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-10 py-2 focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent text-gray-800 dark:text-gray-200 font-medium cursor-pointer">
                                    <option value="Combustible">⛽ Combustible</option>
                                    <option value="Casetas">🛣️ Casetas</option>
                                    <option value="Estacionamiento">🅿️ Estacionamiento</option>
                                    <option value="Viáticos">🍔 Viáticos / Alimentos</option>
                                    <option value="Mantenimiento Menor">🔧 Mantenimiento Menor</option>
                                    <option value="Otros">📦 Otros Gastos</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Fila: Monto + Fecha --}}
                        <div class="grid grid-cols-2 gap-4">
                            
                            {{-- Monto --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-450 dark:text-gray-400 uppercase tracking-wider mb-1.5">Monto ($)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-455 font-bold">$</span>
                                    <input type="number" step="0.01" min="0.01" wire:model="formMonto" placeholder="0.00"
                                        class="w-full pl-7 pr-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent font-bold text-gray-800 dark:text-gray-200" required />
                                </div>
                            </div>

                            {{-- Fecha --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-400 uppercase tracking-wider mb-1.5">Fecha del Gasto</label>
                                <input type="date" wire:model="formFecha"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent font-semibold text-gray-800 dark:text-gray-200" />
                            </div>

                        </div>

                        {{-- Fila: Referencia/Ticket + Comprobante --}}
                        <div class="grid grid-cols-2 gap-4">
                            
                            {{-- Referencia --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-400 uppercase tracking-wider mb-1.5">Ticket / Referencia</label>
                                <input type="text" wire:model="formReferencia" placeholder="Opcional (Ej: TKT-102)"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent font-medium text-gray-800 dark:text-gray-200" />
                            </div>

                            {{-- Comprobante --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-400 uppercase tracking-wider mb-1.5">Comprobante</label>
                                <div class="relative">
                                    <select wire:model="formComprobante"
                                        class="w-full appearance-none bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-10 py-2 focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent text-gray-800 dark:text-gray-200 font-medium cursor-pointer">
                                        <option value="Factura">📄 Factura (XML/PDF)</option>
                                        <option value="Nota/Simplificado">🧾 Nota / Simplificado</option>
                                        <option value="Sin Comprobante">❌ Sin Comprobante</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Observaciones --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-455 dark:text-gray-400 uppercase tracking-wider mb-1.5">Observaciones</label>
                            <textarea wire:model="formObservaciones" rows="2" placeholder="Detalles adicionales del gasto (establecimiento, motivo, etc.)"
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent font-medium text-gray-800 dark:text-gray-200 resize-none"></textarea>
                        </div>

                    </div>

                    {{-- Footer Modal --}}
                    <div class="bg-gray-50 dark:bg-gray-800/80 px-6 py-4 flex justify-end gap-3 border-t border-gray-150 dark:border-gray-700/60 rounded-b-xl">
                        <button wire:click="$set('showGastoModal', false)"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 font-bold transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="guardarGasto"
                            class="px-4 py-2 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold shadow-sm transition cursor-pointer">
                            Guardar Gasto
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
