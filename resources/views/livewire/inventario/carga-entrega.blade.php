<?php

use function Livewire\Volt\{state, layout, mount};
use App\Models\Zone;
use App\Models\Seller;

layout('layouts.app');

state([
    // Catálogos e inicialización
    'zonas' => [],
    'vendedores' => [],
    'selected_zona' => '',
    'selected_ruta' => '',
    'selected_linea' => '',
    'ordenar_por' => 'Orden de línea descendente',
    'fecha_entrega' => '2026-08-05',
    'search' => '',
    
    // Estados de UI
    'consultado' => false,
    'registrosFiltrados' => [],
    'toast_message' => '',
    'toast_type' => 'success',
    
    // Modal de Validación Final
    'show_validation_modal' => false,
    'selected_driver' => '',
    'justificacion' => '',
    
    // Listado maestro estático para simulación de entregas (Carga Entrega)
    'productos' => [
        [
            'clave' => 'AL-200', 
            'descripcion' => 'Galletas de Chocolate 120g', 
            'linea' => 'ALIMENTOS', 
            'existencia_almacen' => 850, 
            'existencia_movil' => 45, 
            'unidades_requeridas' => 120,
            'cantidad_a_cargar' => 75
        ],
        [
            'clave' => 'AL-201', 
            'descripcion' => 'Pan Integral 680g', 
            'linea' => 'ALIMENTOS', 
            'existencia_almacen' => 20, 
            'existencia_movil' => 10, 
            'unidades_requeridas' => 30,
            'cantidad_a_cargar' => 20 // Carga sugerida = 20, que es igual a existencia_almacen. Si se aumenta, dará insuficiente
        ],
        [
            'clave' => 'AL-202', 
            'descripcion' => 'Mermelada de Fresa 270g', 
            'linea' => 'ALIMENTOS', 
            'existencia_almacen' => 610, 
            'existencia_movil' => 15, 
            'unidades_requeridas' => 15,
            'cantidad_a_cargar' => 0 // Carga sugerida = 0
        ],
        [
            'clave' => 'BEB-100', 
            'descripcion' => 'Bebida Energética 500ml', 
            'linea' => 'BEBIDAS', 
            'existencia_almacen' => 15, // Insuficiente en Almacén si intenta cumplir al 100% (se requieren 100, móvil 10, sugerido 90, almacén 15)
            'existencia_movil' => 10, 
            'unidades_requeridas' => 100,
            'cantidad_a_cargar' => 15 // Carga real sugerida inicializada al límite del almacén para simular stock insuficiente si intenta subir
        ],
        [
            'clave' => 'BEB-101', 
            'descripcion' => 'Agua Purificada 1L', 
            'linea' => 'BEBIDAS', 
            'existencia_almacen' => 3200, 
            'existencia_movil' => 250, 
            'unidades_requeridas' => 300,
            'cantidad_a_cargar' => 50
        ],
        [
            'clave' => 'BOT-300', 
            'descripcion' => 'Papas Fritas Clásicas 50g', 
            'linea' => 'BOTANAS', 
            'existencia_almacen' => 2100, 
            'existencia_movil' => 180, 
            'unidades_requeridas' => 200,
            'cantidad_a_cargar' => 20
        ],
        [
            'clave' => 'BOT-301', 
            'descripcion' => 'Cacahuate Japonés 100g', 
            'linea' => 'BOTANAS', 
            'existencia_almacen' => 5000, 
            'existencia_movil' => 320, 
            'unidades_requeridas' => 400,
            'cantidad_a_cargar' => 80
        ],
        [
            'clave' => 'ABA-400', 
            'descripcion' => 'Frijoles Refritos 400g', 
            'linea' => 'ABARROTES', 
            'existencia_almacen' => 1200, 
            'existencia_movil' => 90, 
            'unidades_requeridas' => 100,
            'cantidad_a_cargar' => 10
        ],
    ],
    'lineas' => ['ALIMENTOS', 'BEBIDAS', 'BOTANAS', 'ABARROTES'],
    'ordenar_opciones' => [
        'Orden de línea descendente',
        'Orden de línea ascendente',
        'Clave',
        'Descripción'
    ],
]);

mount(function() {
    try {
        $dbZones = Zone::where('id', '!=', '99-PRUEBA')->orderBy('id', 'asc')->get()->toArray();
        if (!empty($dbZones)) {
            $this->zonas = $dbZones;
        } else {
            $this->zonas = [
                ['id' => '1Z', 'name' => '1Z - Zona 1'],
                ['id' => '2Z', 'name' => '2Z - Zona 2'],
                ['id' => '3Z', 'name' => '3Z - Zona 3'],
            ];
        }
    } catch (\Exception $e) {
        $this->zonas = [
            ['id' => '1Z', 'name' => '1Z - Zona 1'],
            ['id' => '2Z', 'name' => '2Z - Zona 2'],
            ['id' => '3Z', 'name' => '3Z - Zona 3'],
        ];
    }

    try {
        $this->vendedores = Seller::all()->toArray();
    } catch (\Exception $e) {
        $this->vendedores = [
            ['id' => 'V01', 'name' => 'VENDEDOR PRUEBA'],
            ['id' => 'V02', 'name' => 'Ana María Gutiérrez'],
            ['id' => 'V03', 'name' => 'Carlos López Estrada'],
        ];
    }

    $this->selected_zona = $this->zonas[0]['id'] ?? '1Z';
    $rutas = $this->obtenerRutasDeZona();
    $this->selected_ruta = $rutas[0]['id'] ?? '';
    
    $this->registrosFiltrados = [];
    $this->consultado = false;
});

// Obtener rutas dinámicas según zona
$obtenerRutasDeZona = function() {
    $z = strtolower($this->selected_zona);
    if (str_contains($z, '1z') || $z === '1' || str_contains($z, '99-prueba')) {
        return [
            ['id' => '3983', 'name' => '3983 - RUTA01'],
            ['id' => '4682', 'name' => '4682 - RUTA02'],
            ['id' => '4683', 'name' => '4683 - RUTA03'],
        ];
    } elseif (str_contains($z, '2z') || $z === '2') {
        return [
            ['id' => '4684', 'name' => '4684 - RUTA04'],
            ['id' => '4685', 'name' => '4685 - RUTA05'],
        ];
    } elseif (str_contains($z, '3z') || $z === '3') {
        return [
            ['id' => '4686', 'name' => '4686 - RUTA06'],
        ];
    }
    return [
        ['id' => '3983', 'name' => '3983 - RUTA01'],
    ];
};

// Aplicar filtros a la tabla
$aplicarFiltros = function() {
    if (!$this->consultado) {
        $this->registrosFiltrados = [];
        return;
    }

    $data = collect($this->productos);

    // Filtro por línea
    if (!empty($this->selected_linea)) {
        $data = $data->where('linea', $this->selected_linea);
    }

    // Filtro por búsqueda
    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $data = $data->filter(fn($p) => 
            str_contains(strtolower($p['clave']), $q) || 
            str_contains(strtolower($p['descripcion']), $q)
        );
    }

    // Ordenamiento
    if ($this->ordenar_por === 'Clave') {
        $data = $data->sortBy('clave');
    } elseif ($this->ordenar_por === 'Descripción') {
        $data = $data->sortBy('descripcion');
    } elseif ($this->ordenar_por === 'Orden de línea ascendente') {
        $data = $data->sortBy('linea');
    } else {
        // Orden de línea descendente
        $data = $data->sortByDesc('linea');
    }

    $this->registrosFiltrados = $data->values()->toArray();
};

// Actualizar valores de carga dinámicamente
$actualizarCantidad = function($index, $cantidad) {
    if (!is_numeric($cantidad) || $cantidad < 0) {
        $cantidad = 0;
    }
    
    // Obtener la clave del registro filtrado
    $clave = $this->registrosFiltrados[$index]['clave'];
    
    // Actualizar en la lista maestra
    $productosActualizados = $this->productos;
    foreach ($productosActualizados as $key => $p) {
        if ($p['clave'] === $clave) {
            $productosActualizados[$key]['cantidad_a_cargar'] = intval($cantidad);
            break;
        }
    }
    $this->productos = $productosActualizados;
    
    // Re-aplicar filtros para actualizar la vista
    $this->aplicarFiltros();
};

// Disparadores de filtros
$updatedSelectedZona = function() {
    $rutas = $this->obtenerRutasDeZona();
    $this->selected_ruta = $rutas[0]['id'] ?? '';
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSelectedRuta = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSelectedLinea = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedOrdenarPor = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$updatedSearch = function() {
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$clearLinea = function() {
    $this->selected_linea = '';
    if ($this->consultado) {
        $this->aplicarFiltros();
    }
};

$consultar = function() {
    $this->consultado = true;
    $this->aplicarFiltros();
    $this->triggerToast('Datos de preventa y stock consultados correctamente.', 'success');
};

// Helper de Toast notifications
$triggerToast = function($message, $type = 'success') {
    $this->toast_message = $message;
    $this->toast_type = $type;
};

// Validación Final e inicio de Guardado
$validarYGuardar = function() {
    // Verificar bloqueos (stock insuficiente en almacén)
    $bloqueos = false;
    $totalExcedido = 0;
    
    foreach ($this->productos as $p) {
        if ($p['cantidad_a_cargar'] > $p['existencia_almacen']) {
            $bloqueos = true;
            $totalExcedido++;
        }
    }
    
    if ($bloqueos) {
        $this->triggerToast("❌ No se puede guardar la carga: $totalExcedido productos exceden la existencia en Almacén.", 'error');
        return;
    }
    
    // Inicializar valores de modal de validación
    $this->selected_driver = $this->vendedores[0]['id'] ?? '';
    $this->justificacion = '';
    $this->show_validation_modal = true;
};

// Confirmar y Procesar Carga Final
$confirmarCarga = function() {
    // Si hay discrepancias (advertencias), validar justificación
    $hasWarnings = false;
    foreach ($this->productos as $p) {
        $totalCargado = $p['existencia_movil'] + $p['cantidad_a_cargar'];
        if ($totalCargado != $p['unidades_requeridas']) {
            $hasWarnings = true;
            break;
        }
    }
    
    if ($hasWarnings && empty(trim($this->justificacion))) {
        $this->triggerToast('⚠ Debe ingresar una justificación para las discrepancias detectadas.', 'warning');
        return;
    }
    
    // Procesar guardado y reiniciar estado
    $this->show_validation_modal = false;
    $this->consultado = false;
    $this->registrosFiltrados = [];
    
    // Reiniciar cantidades cargadas a sus valores iniciales
    $productosReiniciados = $this->productos;
    foreach ($productosReiniciados as $key => $p) {
        // Valores por defecto
        if ($p['clave'] === 'AL-200') $productosReiniciados[$key]['cantidad_a_cargar'] = 75;
        if ($p['clave'] === 'AL-201') $productosReiniciados[$key]['cantidad_a_cargar'] = 20;
        if ($p['clave'] === 'AL-202') $productosReiniciados[$key]['cantidad_a_cargar'] = 0;
        if ($p['clave'] === 'BEB-100') $productosReiniciados[$key]['cantidad_a_cargar'] = 15;
        if ($p['clave'] === 'BEB-101') $productosReiniciados[$key]['cantidad_a_cargar'] = 50;
        if ($p['clave'] === 'BOT-300') $productosReiniciados[$key]['cantidad_a_cargar'] = 20;
        if ($p['clave'] === 'BOT-301') $productosReiniciados[$key]['cantidad_a_cargar'] = 80;
        if ($p['clave'] === 'ABA-400') $productosReiniciados[$key]['cantidad_a_cargar'] = 10;
    }
    $this->productos = $productosReiniciados;
    
    $this->triggerToast('✔ Carga de entrega guardada y validada con éxito.', 'success');
};

?>

<div class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4">
    <div class="w-full px-6 flex flex-col flex-1">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-6 px-1 no-print">
            <span>Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span>Inventario</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Carga Entrega</span>
        </div>

        {{-- Toast Notifications --}}
        @if ($toast_message)
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('toast_message', '') }, 4000)"
                class="fixed bottom-5 right-5 flex items-center gap-3 px-4 py-3 rounded-lg border text-xs shadow-md transition-all duration-300 z-50 animate-bounce
                    {{ $toast_type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300' : '' }}
                    {{ $toast_type === 'error' ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300' : '' }}
                    {{ $toast_type === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-800 dark:text-yellow-300' : '' }}
                    {{ $toast_type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300' : '' }}">
                <span class="font-semibold">{{ $toast_message }}</span>
            </div>
        @endif

        {{-- Main Filter Panel matching layout --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 mb-6">
            
            <div class="flex items-center gap-2 mb-5">
                <h2 class="text-base font-bold text-[#1f2937] dark:text-white select-none">Carga Inventario Móvil Entrega</h2>
                <div class="relative group cursor-pointer">
                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-72 p-2 bg-gray-900 text-white text-[11px] rounded shadow-lg z-30">
                        Esta pantalla permite realizar la carga y validación de inventario requerida para las entregas agendadas en ruta.
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                
                {{-- Zona Select --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Zona</label>
                    <select wire:model.live="selected_zona" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                        @foreach($zonas as $z)
                            <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Ruta de entrega Select --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Ruta de entrega</label>
                    <select wire:model.live="selected_ruta" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                        <option value="">Selecciona Ruta de entrega</option>
                        @foreach($this->obtenerRutasDeZona() as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Linea Select --}}
                <div class="flex flex-col w-full">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Línea</label>
                    @if ($selected_linea)
                        <div class="flex items-center border-0 border-b border-gray-300 dark:border-gray-600 py-1.5 w-full h-8 cursor-pointer select-none text-gray-750 dark:text-gray-200 font-semibold text-sm" wire:click="clearLinea">
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-250 text-xs px-2.5 py-0.5 rounded border border-gray-200 dark:border-gray-650 flex items-center gap-1 hover:bg-gray-200 dark:hover:bg-gray-650 transition-colors">
                                {{ $selected_linea }}
                                <span class="text-gray-400 font-bold">×</span>
                            </span>
                            <svg class="w-4 h-4 text-gray-400 ml-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    @else
                        <select wire:model.live="selected_linea" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1.5 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full h-8">
                            <option value="">Todas</option>
                            @foreach($lineas as $l)
                                <option value="{{ $l }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Ordenar Por Select --}}
                <div class="flex flex-col w-full col-span-1">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Ordenar por</label>
                    <select wire:model.live="ordenar_por" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-200 font-semibold cursor-pointer w-full">
                        @foreach($ordenar_opciones as $op)
                            <option value="{{ $op }}">{{ $op }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Consultar Button --}}
                <div>
                    <button wire:click="consultar" class="w-full bg-[#003859] hover:bg-[#002b45] text-white py-2 px-6 rounded text-sm font-semibold transition duration-150 cursor-pointer shadow-md active:scale-98">
                        Consultar
                    </button>
                </div>
            </div>

            {{-- Date & Search Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4 items-end">
                <div class="flex flex-col">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Fecha de entrega agendada</label>
                    <input type="date" wire:model.live="fecha_entrega" class="border-0 border-b border-gray-300 dark:border-gray-600 rounded-none py-1 px-0 text-sm focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-250 font-semibold focus:ring-0 w-full" />
                </div>
                <div class="flex flex-col">
                    <label class="text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">Buscar ...</label>
                    <div class="relative flex items-center border-0 border-b border-gray-300 dark:border-gray-600 py-1">
                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live="search" placeholder="Buscar por clave o descripción..." class="border-none bg-transparent outline-none text-xs p-0 text-gray-750 dark:text-gray-200 focus:ring-0 placeholder-gray-400 dark:placeholder-gray-500 w-full" />
                    </div>
                </div>
            </div>
        </div>

        @if($consultado)
            {{-- Main Layout: Table and Validation Summary Side by Side --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mb-6">
                
                {{-- Table column (span 8) --}}
                <div class="lg:col-span-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 overflow-hidden">
                    <div class="overflow-x-auto w-full">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-750 table-fixed">
                            <thead class="bg-gray-50/70 dark:bg-gray-900/20 text-gray-400 dark:text-gray-550 text-[11px] font-bold uppercase tracking-wider">
                                <tr>
                                    <th scope="col" class="w-10 text-center py-3 select-none">#</th>
                                    <th scope="col" class="w-24 text-left py-3 px-4 select-none">Clave</th>
                                    <th scope="col" class="text-left py-3 px-4 select-none">Descripción</th>
                                    <th scope="col" class="w-24 text-center py-3 px-2 select-none">Requerido</th>
                                    <th scope="col" class="w-24 text-center py-3 px-2 select-none">En Almacén</th>
                                    <th scope="col" class="w-24 text-center py-3 px-2 select-none">A Bordo</th>
                                    <th scope="col" class="w-28 text-center py-3 px-2 select-none">Carga Real</th>
                                    <th scope="col" class="w-36 text-center py-3 px-4 select-none">Estatus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-750 text-xs">
                                @forelse($registrosFiltrados as $index => $row)
                                    @php
                                        $totalCargado = $row['existencia_movil'] + $row['cantidad_a_cargar'];
                                        $isLowStock = $row['cantidad_a_cargar'] > $row['existencia_almacen'];
                                        $isIncomplete = $totalCargado < $row['unidades_requeridas'];
                                        $isExcess = $totalCargado > $row['unidades_requeridas'];
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/5 transition-colors duration-100">
                                        <td class="text-center py-3 text-gray-400 font-medium">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="py-3 px-4 font-mono font-bold text-gray-750 dark:text-gray-300">
                                            {{ $row['clave'] }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-800 dark:text-gray-250 truncate" title="{{ $row['descripcion'] }}">
                                            {{ $row['descripcion'] }}
                                        </td>
                                        <td class="py-3 px-2 text-center font-bold text-gray-650 dark:text-gray-450">
                                            {{ number_format($row['unidades_requeridas']) }}
                                        </td>
                                        <td class="py-3 px-2 text-center text-gray-600 dark:text-gray-455">
                                            {{ number_format($row['existencia_almacen']) }}
                                        </td>
                                        <td class="py-3 px-2 text-center text-gray-600 dark:text-gray-455">
                                            {{ number_format($row['existencia_movil']) }}
                                        </td>
                                        <td class="py-3 px-2 text-center">
                                            <input type="number" min="0" value="{{ $row['cantidad_a_cargar'] }}" 
                                                wire:change="actualizarCantidad({{ $index }}, $event.target.value)"
                                                class="w-20 text-center border border-gray-200 dark:border-gray-700 rounded py-1 px-1 focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-850 dark:text-white font-bold text-xs" />
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($isLowStock)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-150 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/30">
                                                    Stock Insuficiente
                                                </span>
                                            @elseif ($isIncomplete)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-150 dark:bg-yellow-950/20 dark:text-yellow-400 dark:border-yellow-900/30">
                                                    Incompleto (-{{ $row['unidades_requeridas'] - $totalCargado }} pzs)
                                                </span>
                                            @elseif ($isExcess)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-150 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/30">
                                                    Excedente (+{{ $totalCargado - $row['unidades_requeridas'] }} pzs)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-150 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900/30">
                                                    ✔ Conciliado
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-12 text-center text-gray-400 dark:text-gray-550 select-none">
                                            No se encontraron registros de entrega.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Validation Panel column (span 4) --}}
                <div class="lg:col-span-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200/80 dark:border-gray-700/80 p-5 flex flex-col gap-4">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2">Resumen de Validación Final</h3>
                    
                    @php
                        $hasStockBlocker = false;
                        $hasDiscrepancies = false;
                        $totalSolicitado = 0;
                        $totalProducCargar = 0;
                        $totalUnidadesCargar = 0;

                        foreach ($this->productos as $p) {
                            $totalSolicitado += $p['unidades_requeridas'];
                            $totalUnidadesCargar += $p['cantidad_a_cargar'];
                            if ($p['cantidad_a_cargar'] > 0) {
                                $totalProducCargar++;
                            }
                            if ($p['cantidad_a_cargar'] > $p['existencia_almacen']) {
                                $hasStockBlocker = true;
                            }
                            if (($p['existencia_movil'] + $p['cantidad_a_cargar']) != $p['unidades_requeridas']) {
                                $hasDiscrepancies = true;
                            }
                        }
                    @endphp

                    {{-- Metrics --}}
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-gray-50 dark:bg-gray-900/40 p-3 rounded border border-gray-100 dark:border-gray-750">
                            <div class="text-gray-400 font-semibold text-[10px] uppercase">Productos a Cargar</div>
                            <div class="text-base font-bold text-gray-800 dark:text-white mt-1">{{ $totalProducCargar }} ítems</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/40 p-3 rounded border border-gray-100 dark:border-gray-750">
                            <div class="text-gray-400 font-semibold text-[10px] uppercase">Unidades a Cargar</div>
                            <div class="text-base font-bold text-gray-800 dark:text-white mt-1">{{ number_format($totalUnidadesCargar) }} pzs</div>
                        </div>
                    </div>

                    {{-- Validation States Alert Box --}}
                    <div class="flex-1">
                        @if ($hasStockBlocker)
                            <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/45 p-4 rounded-lg text-xs text-red-800 dark:text-red-300">
                                <div class="flex items-center gap-2 font-bold mb-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Bloqueado: Stock Insuficiente
                                </div>
                                Algunos productos solicitados superan el inventario físico disponible en el almacén central. Debe ajustar las cantidades antes de continuar.
                            </div>
                        @elseif ($hasDiscrepancies)
                            <div class="bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-200 dark:border-yellow-900/45 p-4 rounded-lg text-xs text-yellow-800 dark:text-yellow-300">
                                <div class="flex items-center gap-2 font-bold mb-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Carga con Discrepancias
                                </div>
                                La carga total (Mercancía a bordo + Carga propuesta) no cubre o excede el 100% de las unidades requeridas de entrega. Se requerirá justificación del supervisor al guardar.
                            </div>
                        @else
                            <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-900/45 p-4 rounded-lg text-xs text-green-800 dark:text-green-300">
                                <div class="flex items-center gap-2 font-bold mb-1">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Carga Conciliada al 100%
                                </div>
                                La carga cuadra perfectamente con las entregas programadas de la ruta y se cuenta con el stock suficiente en el almacén central. Listo para confirmar.
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                        <button wire:click="validarYGuardar" 
                            {{ $hasStockBlocker ? 'disabled' : '' }}
                            class="w-full bg-[#003859] hover:bg-[#002b45] disabled:bg-gray-200 disabled:dark:bg-gray-750 disabled:text-gray-400 disabled:cursor-not-allowed text-white text-center py-2.5 rounded font-bold transition duration-150 cursor-pointer shadow-md text-xs active:scale-98">
                            Validar y Guardar Carga
                        </button>
                    </div>

                </div>
            </div>
        @endif

        {{-- Final Validation Modal --}}
        @if ($show_validation_modal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity"></div>

                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-scale-up border border-gray-250 dark:border-gray-700">
                        
                        {{-- Header --}}
                        <div class="bg-white dark:bg-gray-800 px-6 py-5 border-b border-gray-150 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                                Validación de Carga Final
                            </h3>
                            <button @click="$wire.set('show_validation_modal', false)" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Body --}}
                        <div class="bg-white dark:bg-gray-800 px-6 py-5 text-xs space-y-4">
                            
                            {{-- Checklists list --}}
                            <div class="space-y-2">
                                <span class="font-bold text-[10px] text-gray-400 uppercase tracking-wider block">Verificación de Parámetros</span>
                                
                                <div class="flex items-center gap-3 py-1 bg-gray-50/50 dark:bg-gray-900/10 px-2.5 rounded border border-gray-100 dark:border-gray-750">
                                    <span class="text-green-600 dark:text-green-400 font-bold text-sm">✔</span>
                                    <div class="flex-1 font-medium text-gray-700 dark:text-gray-300">Inventario disponible en Almacén Central</div>
                                </div>

                                @php
                                    $discrepancyList = [];
                                    foreach ($this->productos as $p) {
                                        $finalTotal = $p['existencia_movil'] + $p['cantidad_a_cargar'];
                                        if ($finalTotal != $p['unidades_requeridas']) {
                                            $discrepancyList[] = $p;
                                        }
                                    }
                                @endphp

                                @if (count($discrepancyList) > 0)
                                    <div class="flex items-start gap-3 py-2.5 bg-yellow-50/60 dark:bg-yellow-950/10 px-2.5 rounded border border-yellow-150/40 dark:border-yellow-900/20">
                                        <span class="text-yellow-600 dark:text-yellow-400 font-bold text-sm">⚠</span>
                                        <div class="flex-1">
                                            <div class="font-bold text-yellow-800 dark:text-yellow-350">Desajustes detectados en entregas ({{ count($discrepancyList) }} ítems)</div>
                                            <div class="text-[10px] text-yellow-700/80 dark:text-yellow-400/80 mt-1 space-y-0.5">
                                                @foreach ($discrepancyList as $disc)
                                                    <div>• {{ $disc['clave'] }}: Requerido {{ $disc['unidades_requeridas'] }}, Propuesto {{ $disc['existencia_movil'] + $disc['cantidad_a_cargar'] }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 py-1 bg-gray-50/50 dark:bg-gray-900/10 px-2.5 rounded border border-gray-100 dark:border-gray-750">
                                        <span class="text-green-600 dark:text-green-400 font-bold text-sm">✔</span>
                                        <div class="flex-1 font-medium text-gray-700 dark:text-gray-300">Carga propuesta concilia al 100% con entregas</div>
                                    </div>
                                @endif

                                <div class="flex items-center gap-3 py-1 bg-gray-50/50 dark:bg-gray-900/10 px-2.5 rounded border border-gray-100 dark:border-gray-750">
                                    <span class="text-green-600 dark:text-green-400 font-bold text-sm">✔</span>
                                    <div class="flex-1 font-medium text-gray-700 dark:text-gray-300">Capacidad máxima volumétrica de la unidad de reparto</div>
                                </div>
                            </div>

                            {{-- Driver Assignee --}}
                            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                                <label class="block text-[10px] text-gray-400 dark:text-gray-550 mb-0.5 font-bold">REPARTIDOR / CHOFER DESIGNADO</label>
                                <select wire:model="selected_driver" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-350 outline-none cursor-pointer focus:ring-0 border-none p-0">
                                    @foreach ($vendedores as $v)
                                        <option value="{{ $v['id'] }}">{{ $v['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Justification (if warning exists) --}}
                            @if (count($discrepancyList) > 0)
                                <div class="space-y-1">
                                    <label class="block text-[10px] text-red-600 dark:text-red-400 font-bold">JUSTIFICACIÓN OBLIGATORIA DE DESAJUSTE</label>
                                    <textarea wire:model.live="justificacion" rows="3" placeholder="Detalle el motivo del desajuste con las entregas (ej. Falta de existencias en bodega)..."
                                        class="w-full bg-white dark:bg-gray-800 border border-gray-250 dark:border-gray-700 rounded p-2 text-xs text-gray-750 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:border-[#003859] dark:focus:border-blue-500"></textarea>
                                </div>
                            @endif

                            {{-- Acceptance terms description --}}
                            <div class="bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-750 p-3 rounded text-[10px] text-gray-400 leading-normal">
                                Al confirmar, se generará la responsiva de inventario y se actualizarán las existencias móviles a bordo de la unidad. El repartidor responderá por las cantidades asignadas.
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="bg-gray-50 dark:bg-gray-900/20 px-6 py-4 border-t border-gray-150 dark:border-gray-700 flex justify-end gap-3 select-none">
                            <button @click="$wire.set('show_validation_modal', false)"
                                class="px-4 py-2 border border-gray-350 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 rounded font-semibold text-xs transition duration-150 cursor-pointer shadow-sm">
                                Cancelar
                            </button>
                            
                            <button wire:click="confirmarCarga"
                                {{ (count($discrepancyList) > 0 && empty(trim($justificacion))) ? 'disabled' : '' }}
                                class="px-4 py-2 bg-[#003859] hover:bg-[#002b45] disabled:bg-gray-250 disabled:dark:bg-gray-750 disabled:text-gray-400 disabled:cursor-not-allowed text-white rounded font-bold text-xs transition duration-150 cursor-pointer shadow-md">
                                Confirmar y Conciliar Carga
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        {{-- Copyright footer matching designs --}}
        <div class="mt-8 text-center text-[10px] text-gray-400 dark:text-gray-550 uppercase tracking-widest select-none no-print">
            Copyright © JB VEMOBILE SA DE CV 2026.
        </div>

    </div>
</div>
