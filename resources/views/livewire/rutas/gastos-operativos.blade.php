<?php

use function Livewire\Volt\{state, layout, mount};

layout('layouts.app');

$mockRubros = [
    ['id' => 'Combustible', 'name' => 'Combustible'],
    ['id' => 'Casetas', 'name' => 'Casetas'],
    ['id' => 'Estacionamiento', 'name' => 'Estacionamiento'],
    ['id' => 'Viáticos', 'name' => 'Viáticos / Alimentos'],
    ['id' => 'Mantenimiento Menor', 'name' => 'Mantenimiento Menor'],
    ['id' => 'Otros', 'name' => 'Otros Gastos'],
];

state([
    // Datos base
    'zonas' => [],
    'rutas' => [],
    'rubros' => $mockRubros,

    // Historial
    'gastosRegistrados' => [],
    'gastosFiltrados' => [],

    // Filtros
    'filtro_zona' => 'todos',
    'filtro_ruta' => 'todos',
    'filtro_concepto' => 'todos',
    'search' => '',

    // Listas dependientes para filtros
    'rutasFiltradasPorZona' => [],

    // Columnas visibles de la tabla
    'col_fecha' => true,
    'col_zona' => true,
    'col_ruta' => true,
    'col_concepto' => true,
    'col_monto' => true,
    'col_comprobante' => true,
    'col_referencia' => true,
    'col_observaciones' => true,
    'col_acciones' => true,

    // Notificaciones locales
    'notification' => '',
    'notificationType' => 'success',

    // Agregados / Totales
    'totalMonto' => 0,
    'montoCombustible' => 0,
    'montoCasetas' => 0,
    'montoViaticos' => 0,
]);

mount(function () {
    // Load Zones from DB
    $this->zonas = \App\Models\Zone::orderBy('id')->get()->map(fn($z) => [
        'id' => $z->id,
        'name' => $z->id
    ])->toArray();
    
    // Load Routes/Sellers from DB
    $this->rutas = \App\Models\Seller::where('oculto', 'N')->get()->map(function ($s) {
        if (str_contains($s->id, ' - ')) {
            $parts = explode(' - ', $s->id);
            $clave = trim($parts[0]);
            $nombre = trim($parts[1]);
        } else {
            $clave = $s->id;
            $nombre = $s->name;
        }
        
        $invoice = \App\Models\Invoice::where('vendedor_id', $s->id)->first();
        $zona = $invoice ? $invoice->zona_id : '1Z - Zona 1';
        
        return [
            'clave' => $clave,
            'nombre' => $nombre,
            'zona' => $zona
        ];
    })->toArray();

    // Inicializar datos en la sesión si no existen
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
                'observaciones' => 'Carga de diesel para inicio de ruta',
                'zona' => '1Z - Zona 1'
            ],
            [
                'ruta_clave' => '4683',
                'ruta_nombre' => 'RUTA03',
                'concepto' => 'Casetas',
                'monto' => 420.00,
                'fecha' => date('Y-m-d', strtotime('-1 days')),
                'comprobante' => 'Nota/Simplificado',
                'referencia' => 'PEAJE-88273',
                'observaciones' => 'Peaje autopista de cuota norte',
                'zona' => '1Z - Zona 1'
            ],
            [
                'ruta_clave' => '4682',
                'ruta_nombre' => 'RUTA02',
                'concepto' => 'Viáticos / Alimentos',
                'monto' => 180.00,
                'fecha' => date('Y-m-d'),
                'comprobante' => 'Sin Comprobante',
                'referencia' => 'ALIM-122',
                'observaciones' => 'Almuerzo del chófer de reparto',
                'zona' => '1Z - Zona 1'
            ],
            [
                'ruta_clave' => '4684',
                'ruta_nombre' => 'RUTA04',
                'concepto' => 'Combustible',
                'monto' => 950.00,
                'fecha' => date('Y-m-d', strtotime('-2 days')),
                'comprobante' => 'Factura',
                'referencia' => 'TKT-9912',
                'observaciones' => 'Gasolina Magna RUTA04',
                'zona' => '2Z - Zona 2'
            ]
        ];
        session(['gastos_operativos' => $defaultGastos]);
    }

    // Si hay una alerta flash de éxito desde el formulario
    if (session()->has('success_gasto')) {
        $this->triggerNotification(session('success_gasto'), 'success');
    }

    $this->actualizarListasYFiltros();
});

$actualizarListasYFiltros = function () {
    $this->gastosRegistrados = session('gastos_operativos', []);
    
    // Cargar rutas disponibles según la zona seleccionada
    if ($this->filtro_zona && $this->filtro_zona !== 'todos') {
        $this->rutasFiltradasPorZona = collect($this->rutas)->where('zona', $this->filtro_zona)->values()->toArray();
    } else {
        $this->rutasFiltradasPorZona = $this->rutas;
    }

    $this->aplicarFiltros();
};

$aplicarFiltros = function () {
    $filtrados = collect($this->gastosRegistrados);

    // Filtro por Zona
    if ($this->filtro_zona !== 'todos') {
        $filtrados = $filtrados->where('zona', $this->filtro_zona);
    }

    // Filtro por Ruta
    if ($this->filtro_ruta !== 'todos') {
        $filtrados = $filtrados->where('ruta_clave', $this->filtro_ruta);
    }

    // Filtro por Concepto/Rubro
    if ($this->filtro_concepto !== 'todos') {
        $filtrados = $filtrados->filter(function ($g) {
            return str_contains(strtolower($g['concepto']), strtolower($this->filtro_concepto));
        });
    }

    // Búsqueda de texto libre
    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtrados = $filtrados->filter(function ($g) use ($q) {
            return str_contains(strtolower($g['ruta_nombre']), $q) ||
                   str_contains(strtolower($g['ruta_clave']), $q) ||
                   str_contains(strtolower($g['concepto']), $q) ||
                   str_contains(strtolower($g['referencia']), $q) ||
                   str_contains(strtolower($g['observaciones']), $q);
        });
    }

    $this->gastosFiltrados = $filtrados->values()->toArray();

    // Calcular agregados
    $this->totalMonto = $filtrados->sum('monto');
    $this->montoCombustible = $filtrados->filter(fn($g) => str_contains($g['concepto'], 'Combustible'))->sum('monto');
    $this->montoCasetas = $filtrados->filter(fn($g) => str_contains($g['concepto'], 'Casetas'))->sum('monto');
    $this->montoViaticos = $filtrados->filter(fn($g) => str_contains($g['concepto'], 'Viáticos') || str_contains($g['concepto'], 'Alimentos'))->sum('monto');
};

$updatedFiltroZona = function () {
    $this->filtro_ruta = 'todos'; // Reset de la ruta elegida
    $this->actualizarListasYFiltros();
};

$updatedFiltroRuta = fn() => $this->aplicarFiltros();
$updatedFiltroConcepto = fn() => $this->aplicarFiltros();
$updatedSearch = fn() => $this->aplicarFiltros();

$clearSearch = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

$actualizar = function () {
    $this->actualizarListasYFiltros();
    $this->triggerNotification('Gastos operativos actualizados y sincronizados.', 'info');
};

$eliminarGasto = function ($index) {
    $gastos = session('gastos_operativos', []);
    
    // El index que pasamos es el de la lista filtrada, debemos encontrarlo en la lista de sesión
    $gastoAEliminar = $this->gastosFiltrados[$index] ?? null;
    
    if ($gastoAEliminar) {
        foreach ($gastos as $sIdx => $g) {
            if ($g['referencia'] === $gastoAEliminar['referencia'] && $g['monto'] == $gastoAEliminar['monto'] && $g['ruta_clave'] === $gastoAEliminar['ruta_clave']) {
                unset($gastos[$sIdx]);
                break;
            }
        }
        $gastos = array_values($gastos); // Reindexar
        session(['gastos_operativos' => $gastos]);
        
        $this->actualizarListasYFiltros();
        $this->triggerNotification('Gasto operativo eliminado correctamente.', 'delete');
    }
};

$triggerNotification = function ($msg, $type = 'success') {
    $this->notification = $msg;
    $this->notificationType = $type;
};

?>

<div class="h-full bg-[#f4f6f8] dark:bg-gray-900 flex flex-col pt-4 overflow-y-auto">
    <div class="w-full px-6 flex flex-col flex-1 pb-10">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1 select-none">
            <span>Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span>Ruta</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Gastos Operativos (Concentrador)</span>
        </div>

        {{-- Header & Notification --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">Concentrador de Gastos Operativos</h1>
                <p class="text-xs text-gray-450 dark:text-gray-400 mt-1">Revisa, audita y filtra todos los egresos registrados en tránsito por zona y ruta.</p>
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
                    class="flex items-center gap-2 px-4 py-2.5 rounded-lg border text-sm shadow-sm transition-all duration-300 animate-fade-in z-30">
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

        {{-- KPI Widgets --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            
            {{-- KPI Total Gastado --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block">Total Reportado</span>
                    <span class="text-xl font-extrabold text-[#003859] dark:text-blue-400 mt-1 block">${{ number_format($totalMonto, 2) }}</span>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-[#003859] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- KPI Combustible --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block">⛽ Combustible</span>
                    <span class="text-xl font-extrabold text-orange-600 dark:text-orange-400 mt-1 block">${{ number_format($montoCombustible, 2) }}</span>
                </div>
                <div class="p-2 bg-orange-50 dark:bg-orange-950/20 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                    </svg>
                </div>
            </div>

            {{-- KPI Casetas --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block">🛣️ Casetas</span>
                    <span class="text-xl font-extrabold text-green-600 dark:text-green-400 mt-1 block">${{ number_format($montoCasetas, 2) }}</span>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-950/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
            </div>

            {{-- KPI Viáticos --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl p-4 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest block">🍔 Viáticos</span>
                    <span class="text-xl font-extrabold text-purple-600 dark:text-purple-400 mt-1 block">${{ number_format($montoViaticos, 2) }}</span>
                </div>
                <div class="p-2 bg-purple-50 dark:bg-purple-950/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </div>
            </div>

        </div>

        {{-- Main Table Section --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-700/80 rounded-xl shadow-sm overflow-hidden flex flex-col flex-1">
            
            {{-- Toolbar / Filters --}}
            <div class="p-5 border-b border-gray-150 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-800/40 flex flex-col gap-4" x-data="{ showColumnas: false }">
                
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        
                        {{-- Filtro Zona --}}
                        <div class="relative w-full sm:w-44">
                            <label class="absolute -top-2 left-2 px-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-850 tracking-wider">ZONA</label>
                            <select wire:model.live="filtro_zona"
                                class="appearance-none border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold cursor-pointer w-full shadow-sm">
                                <option value="todos">Todas las Zonas</option>
                                @foreach($zonas as $z)
                                    <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Filtro Ruta --}}
                        <div class="relative w-full sm:w-44">
                            <label class="absolute -top-2 left-2 px-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-850 tracking-wider">RUTA / VENDEDOR</label>
                            <select wire:model.live="filtro_ruta"
                                class="appearance-none border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold cursor-pointer w-full shadow-sm">
                                <option value="todos">Todas las Rutas</option>
                                @foreach($rutasFiltradasPorZona as $r)
                                    <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                        {{-- Filtro Concepto --}}
                        <div class="relative w-full sm:w-44">
                            <label class="absolute -top-2 left-2 px-1 text-[9px] font-bold text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-850 tracking-wider">RUBRO / CONCEPTO</label>
                            <select wire:model.live="filtro_concepto"
                                class="appearance-none border border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-8 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold cursor-pointer w-full shadow-sm">
                                <option value="todos">Todos los Rubros</option>
                                @foreach($rubros as $rub)
                                    <option value="{{ $rub['id'] }}">{{ $rub['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>

                    </div>

                    {{-- Registrar Gasto Button --}}
                    <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                        <a href="{{ route('ventas.nuevo-gasto-op') }}" wire:navigate
                            class="px-4 py-1.5 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold shadow-sm transition-all duration-155 text-xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Registrar Gasto
                        </a>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-2 border-t border-gray-100 dark:border-gray-700/50">
                    {{-- Search bar --}}
                    <div class="relative w-full lg:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar gasto por ref, obs, ruta..."
                            class="w-full pl-9 pr-9 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 focus:border-transparent bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-medium placeholder-gray-400 shadow-sm" />
                        @if (!empty($search))
                            <button wire:click="clearSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Icons --}}
                    <div class="flex items-center gap-2 justify-end">
                        
                        {{-- Select Columnas --}}
                        <div class="relative">
                            <button @click="showColumnas = !showColumnas" title="Ver Columnas"
                                class="p-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z" />
                                </svg>
                            </button>
                            <div x-show="showColumnas" @click.outside="showColumnas = false" x-cloak
                                class="absolute right-0 top-10 z-40 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-4 w-44">
                                <p class="text-[10px] font-bold text-gray-450 dark:text-gray-500 uppercase tracking-wider mb-2">Columnas</p>
                                <div class="space-y-1.5">
                                    @foreach ([['col_fecha', 'Fecha'], ['col_zona', 'Zona'], ['col_ruta', 'Ruta'], ['col_concepto', 'Rubro'], ['col_monto', 'Monto'], ['col_comprobante', 'Comprobante'], ['col_referencia', 'Referencia'], ['col_observaciones', 'Observaciones'], ['col_acciones', 'Acciones']] as [$field, $label])
                                        <label class="flex items-center gap-2 py-0.5 rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 text-[11px] text-gray-700 dark:text-gray-300">
                                            <input type="checkbox" wire:model.live="{{ $field }}"
                                                class="w-3.5 h-3.5 text-[#003859] dark:text-blue-600 rounded border-gray-300 dark:border-gray-700 focus:ring-[#003859]" />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Exportar CSV --}}
                        <button wire:click="triggerNotification('Exportación en formato CSV iniciada.', 'info')" title="Exportar CSV"
                            class="p-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>

                        {{-- Refresh --}}
                        <button wire:click="actualizar" title="Actualizar"
                            class="p-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-[#003859] dark:hover:text-blue-400 transition flex items-center justify-center bg-white dark:bg-gray-800 shadow-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>

                    </div>
                </div>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-left border-collapse">
                    <thead class="bg-[#f8fafc] dark:bg-gray-800/80 text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider select-none">
                        <tr>
                            @if ($col_fecha)
                                <th scope="col" class="px-6 py-4 w-32">Fecha</th>
                            @endif
                            @if ($col_zona)
                                <th scope="col" class="px-6 py-4 w-32">Zona</th>
                            @endif
                            @if ($col_ruta)
                                <th scope="col" class="px-6 py-4">Ruta / Vendedor</th>
                            @endif
                            @if ($col_concepto)
                                <th scope="col" class="px-6 py-4 w-44">Concepto / Rubro</th>
                            @endif
                            @if ($col_monto)
                                <th scope="col" class="px-6 py-4 w-32 text-right">Monto</th>
                            @endif
                            @if ($col_comprobante)
                                <th scope="col" class="px-6 py-4 w-36">Comprobante</th>
                            @endif
                            @if ($col_referencia)
                                <th scope="col" class="px-6 py-4 w-36">Referencia</th>
                            @endif
                            @if ($col_observaciones)
                                <th scope="col" class="px-6 py-4 max-w-xs">Observaciones</th>
                            @endif
                            @if ($col_acciones)
                                <th scope="col" class="px-6 py-4 w-24 text-center">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700 text-xs text-gray-700 dark:text-gray-300">
                        @forelse($gastosFiltrados as $idx => $gasto)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                @if ($col_fecha)
                                    <td class="px-6 py-3.5 whitespace-nowrap font-medium text-gray-500 dark:text-gray-400">
                                        {{ date('d M, Y', strtotime($gasto['fecha'])) }}
                                    </td>
                                @endif
                                @if ($col_zona)
                                    <td class="px-6 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                        {{ $gasto['zona'] ?? '1Z - Zona 1' }}
                                    </td>
                                @endif
                                @if ($col_ruta)
                                    <td class="px-6 py-3.5 font-bold text-[#003859] dark:text-blue-400">
                                        {{ $gasto['ruta_clave'] }} - {{ $gasto['ruta_nombre'] }}
                                    </td>
                                @endif
                                @if ($col_concepto)
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-150 dark:border-gray-600">
                                            {{ $gasto['concepto'] }}
                                        </span>
                                    </td>
                                @endif
                                @if ($col_monto)
                                    <td class="px-6 py-3.5 whitespace-nowrap font-extrabold text-right text-gray-800 dark:text-gray-100">
                                        ${{ number_format($gasto['monto'], 2) }}
                                    </td>
                                @endif
                                @if ($col_comprobante)
                                    <td class="px-6 py-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider
                                            {{ $gasto['comprobante'] === 'Factura' ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30' : '' }}
                                            {{ $gasto['comprobante'] === 'Nota/Simplificado' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30' : '' }}
                                            {{ $gasto['comprobante'] === 'Sin Comprobante' ? 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30' : '' }}
                                        ">
                                            {{ $gasto['comprobante'] }}
                                        </span>
                                    </td>
                                @endif
                                @if ($col_referencia)
                                    <td class="px-6 py-3.5 whitespace-nowrap font-mono text-gray-500 dark:text-gray-400">
                                        {{ $gasto['referencia'] ?: 'N/A' }}
                                    </td>
                                @endif
                                @if ($col_observaciones)
                                    <td class="px-6 py-3.5 max-w-xs truncate text-gray-500 dark:text-gray-400" title="{{ $gasto['observaciones'] }}">
                                        {{ $gasto['observaciones'] }}
                                    </td>
                                @endif
                                @if ($col_acciones)
                                    <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                        <button wire:click="eliminarGasto({{ $idx }})"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg transition-all duration-150 cursor-pointer inline-flex items-center justify-center"
                                            title="Eliminar gasto operativo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-450 dark:text-gray-550 select-none">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-gray-300 dark:text-gray-650" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>No hay gastos registrados que coincidan con los filtros seleccionados.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Table Footer / Pagination mock --}}
            <div class="px-5 py-4 border-t border-gray-150 dark:border-gray-700 bg-[#f8fafc]/50 dark:bg-gray-800/40 flex justify-between items-center text-xs text-gray-500 dark:text-gray-450 select-none">
                <div class="flex items-center gap-1.5">
                    <span>100 Filas por Página</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 cursor-pointer animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-medium">1-{{ count($gastosFiltrados) }} of {{ count($gastosFiltrados) }}</span>
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

    </div>
</div>
