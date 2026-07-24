<?php

use function Livewire\Volt\{state, layout};
use App\Models\Zone;

layout('layouts.app');

state([
    // Zonas catalog from DB with fallback
    'zonas' => function() {
        $dbZonas = Zone::all()->toArray();
        if (empty($dbZonas)) {
            return [
                ['id' => '1Z - Zona 1', 'name' => 'Zona 1'],
                ['id' => '2Z - Zona 2', 'name' => 'Zona 2'],
            ];
        }
        return $dbZonas;
    },
    
    // Product Lines catalog
    'lineas' => ['ALIMENTOS', 'BEBIDAS', 'BOTANAS', 'LIMPIEZA', 'OTROS'],
    
    // Comparators
    'comparadores' => [
        'Igual' => '==',
        'Mayor o igual' => '>=',
        'Menor o igual' => '<='
    ],

    // Filters (active after clicking Consultar)
    'filter_zona' => '',
    'filter_linea' => '',
    'filter_tipo_cantidad' => '',
    'filter_cantidad' => null,

    // Inputs bound to UI filters (live)
    'input_zona' => '',
    'input_linea' => '',
    'input_tipo_cantidad' => '',
    'input_cantidad' => null,

    // Live search and table options
    'search' => '',
    'per_page' => 100,
    'page' => 1,
    'show_column_dropdown' => false,
    
    // Column Visibility Configuration
    'visible_columns' => [
        'clave' => true,
        'producto' => true,
        'existencia' => true,
        'precio_compra' => true,
        'nuevo_precio_compra' => true,
        'precio_final' => true,
        'inventario_posterior' => true,
    ],

    // Audit return database (mocked base records)
    'retornos' => [
        [
            'id' => 1,
            'clave' => 'PROD-001',
            'producto' => 'Bebida Energética 500ml',
            'linea' => 'BEBIDAS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 1500,
            'precio_compra' => 15.00,
            'nuevo_precio_compra' => 15.00,
            'precio_final' => 25.00,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'VENDEDOR PRUEBA',
        ],
        [
            'id' => 2,
            'clave' => 'PROD-002',
            'producto' => 'Agua Purificada 1L',
            'linea' => 'BEBIDAS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 3200,
            'precio_compra' => 7.00,
            'nuevo_precio_compra' => 7.00,
            'precio_final' => 12.00,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'VENDEDOR PRUEBA',
        ],
        [
            'id' => 3,
            'clave' => 'PROD-003',
            'producto' => 'Galletas de Chocolate 120g',
            'linea' => 'ALIMENTOS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 850,
            'precio_compra' => 11.20,
            'nuevo_precio_compra' => 11.20,
            'precio_final' => 18.50,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'Ana María Gutiérrez',
        ],
        [
            'id' => 4,
            'clave' => 'PROD-004',
            'producto' => 'Papas Fritas Clásicas 50g',
            'linea' => 'BOTANAS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 2100,
            'precio_compra' => 9.00,
            'nuevo_precio_compra' => 9.00,
            'precio_final' => 15.00,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'Carlos López Estrada',
        ],
        [
            'id' => 5,
            'clave' => 'PROD-005',
            'producto' => 'Refresco de Cola 600ml',
            'linea' => 'BEBIDAS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 4500,
            'precio_compra' => 10.50,
            'nuevo_precio_compra' => 10.50,
            'precio_final' => 18.00,
            'retornado' => 20,
            'audited' => false,
            'vendedor' => 'VENDEDOR PRUEBA',
        ],
        [
            'id' => 6,
            'clave' => 'PROD-006',
            'producto' => 'Jugo de Naranja 1L',
            'linea' => 'BEBIDAS',
            'zona_id' => '2Z - Zona 2',
            'existencia' => 1200,
            'precio_compra' => 14.00,
            'nuevo_precio_compra' => 14.00,
            'precio_final' => 22.00,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'VENDEDOR PRUEBA',
        ],
        [
            'id' => 7,
            'clave' => 'PROD-007',
            'producto' => 'Cacahuate Japonés 100g',
            'linea' => 'BOTANAS',
            'zona_id' => '1Z - Zona 1',
            'existencia' => 5000,
            'precio_compra' => 6.00,
            'nuevo_precio_compra' => 6.00,
            'precio_final' => 10.00,
            'retornado' => 15,
            'audited' => false,
            'vendedor' => 'Ana María Gutiérrez',
        ]
    ],

    // Results to show
    'resultados' => [],
    'notification' => '',
    'notification_type' => 'success'
]);

// Apply query / filters
$consultar = function() {
    $this->filter_zona = $this->input_zona;
    $this->filter_linea = $this->input_linea;
    $this->filter_tipo_cantidad = $this->input_tipo_cantidad;
    $this->filter_cantidad = $this->input_cantidad;
    $this->page = 1;
    $this->aplicarFiltros();
};

// Filter logic matching the inputs and UI constraints
$aplicarFiltros = function() {
    $query = collect($this->retornos);

    // 1. Filter by Zona (if selected)
    if ($this->filter_zona) {
        $query = $query->filter(function($item) {
            return str_contains(strtolower($item['zona_id']), strtolower($this->filter_zona)) || 
                   str_contains(strtolower($item['vendedor']), strtolower($this->filter_zona));
        });
    }

    // 2. Filter by Linea (if selected)
    if ($this->filter_linea) {
        $query = $query->where('linea', $this->filter_linea);
    }

    // 3. Filter by Quantity Comparator
    if ($this->filter_cantidad !== '' && $this->filter_cantidad !== null) {
        $qtyFilter = (int)$this->filter_cantidad;
        $comp = $this->filter_tipo_cantidad;
        
        $query = $query->filter(function($item) use ($comp, $qtyFilter) {
            if ($comp === 'Mayor o igual') {
                return $item['retornado'] >= $qtyFilter;
            } elseif ($comp === 'Menor o igual') {
                return $item['retornado'] <= $qtyFilter;
            } else {
                return $item['retornado'] == $qtyFilter;
            }
        });
    }

    // 4. Live Search Filter (Key or Description)
    if ($this->search) {
        $searchQuery = strtolower(trim($this->search));
        $query = $query->filter(function($item) use ($searchQuery) {
            return str_contains(strtolower($item['clave']), $searchQuery) ||
                   str_contains(strtolower($item['producto']), $searchQuery);
        });
    }

    $this->resultados = $query->values()->toArray();
};

// Handle live search changes
$updatedSearch = function() {
    $this->aplicarFiltros();
};

// Handle per page changes
$updatedPerPage = function() {
    $this->page = 1;
};

// Initialize component data
$init = function() {
    $this->aplicarFiltros();
};

// Toggle column visibility
$toggleColumna = function($col) {
    if (isset($this->visible_columns[$col])) {
        $this->visible_columns[$col] = !$this->visible_columns[$col];
    }
};

// Clear select filter line
$clearLinea = function() {
    $this->input_linea = '';
};

// Update new purchase price
$updateNuevoPrecioCompra = function($id, $val) {
    $val = floatval($val);
    foreach ($this->retornos as $idx => $item) {
        if ($item['id'] === $id) {
            $this->retornos[$idx]['nuevo_precio_compra'] = $val;
            break;
        }
    }
    $this->aplicarFiltros();
};

// Approve individual audit return
$aprobarAuditoria = function($id) {
    foreach ($this->retornos as $idx => $item) {
        if ($item['id'] === $id) {
            if ($this->retornos[$idx]['audited']) {
                $this->notification = "Este retorno ya ha sido auditado y guardado.";
                $this->notification_type = 'info';
                return;
            }
            // Mark as audited, apply returned stock to existence
            $this->retornos[$idx]['audited'] = true;
            $this->retornos[$idx]['existencia'] += $item['retornado'];
            $this->notification = "Entrada de Almacén auditada correctamente para el producto: " . $item['producto'];
            $this->notification_type = 'success';
            break;
        }
    }
    $this->aplicarFiltros();
};

// Audit all filtered records
$auditarTodo = function() {
    $auditedCount = 0;
    foreach ($this->resultados as $res) {
        foreach ($this->retornos as $idx => $item) {
            if ($item['id'] === $res['id'] && !$item['audited']) {
                $this->retornos[$idx]['audited'] = true;
                $this->retornos[$idx]['existencia'] += $item['retornado'];
                $auditedCount++;
            }
        }
    }
    
    if ($auditedCount > 0) {
        $this->notification = "Se han auditado {$auditedCount} retornos de ruta de forma exitosa.";
        $this->notification_type = 'success';
    } else {
        $this->notification = "No se encontraron retornos pendientes por auditar en el filtro actual.";
        $this->notification_type = 'info';
    }
    $this->aplicarFiltros();
};

?>

<div class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4" x-data="{ openCols: false }">
    <div class="w-full px-6 flex flex-col flex-1">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1">
            <span class="hover:text-gray-700 cursor-pointer">Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="hover:text-gray-700 cursor-pointer">Inventario</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Entrada Almacén</span>
        </div>

        {{-- Header & Notificaciones --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Entrada Inventario</h1>
            
            {{-- Toast Notification --}}
            @if ($notification)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('notification', '') }, 3500)"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border text-sm shadow-sm transition-all duration-150 animate-fade-in z-50
                        {{ $notification_type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300' : '' }}
                        {{ $notification_type === 'error' ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300' : '' }}
                        {{ $notification_type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300' : '' }}">
                    <span>{{ $notification }}</span>
                </div>
            @endif
        </div>

        {{-- Filtros Superiores --}}
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-6 mb-6 px-1 items-end">
            {{-- Zona --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">ZONA</label>
                <select wire:model="input_zona" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="">Todas las Zonas</option>
                    @foreach ($zonas as $z)
                        <option value="{{ $z['id'] }}">{{ $z['id'] }} - {{ $z['name'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Línea --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">LÍNEA</label>
                <div class="flex items-center justify-between gap-1 w-full relative">
                    <select wire:model="input_linea" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer pr-4">
                        <option value="">Selecciona Línea</option>
                        @foreach ($lineas as $l)
                            <option value="{{ $l }}">{{ $l }}</option>
                        @endforeach
                    </select>
                    @if ($input_linea)
                        <button wire:click="clearLinea" class="text-gray-400 hover:text-gray-600 focus:outline-none absolute right-4 bottom-1.5 cursor-pointer">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                    <div class="pointer-events-none absolute right-0 bottom-1.5 text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tipo Cantidad --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">TIPO CANTIDAD</label>
                <select wire:model="input_tipo_cantidad" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="">Selecciona Comparación</option>
                    @foreach ($comparadores as $name => $sym)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Cantidad --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">CANTIDAD</label>
                <input type="number" wire:model="input_cantidad" class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>

            {{-- Consultar Button --}}
            <div class="pb-0.5">
                <button wire:click="consultar"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-6 rounded shadow transition duration-150 cursor-pointer">
                    Consultar
                </button>
            </div>
        </div>

        {{-- Toolbar Row: Column Visibility & Search & Bulk Actions --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-4 px-1">
            <div class="flex items-center gap-3">
                {{-- Column visibility dropdown --}}
                <div class="relative">
                    <button @click="openCols = !openCols"
                        class="flex items-center gap-2 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer select-none font-semibold">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>Columnas</span>
                        <svg class="w-3 h-3 text-gray-400 transition-transform duration-200" :class="openCols ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="openCols" @click.outside="openCols = false" style="display: none;"
                        class="absolute left-0 mt-1 w-52 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 z-40 py-1.5 text-xs">
                        <div class="px-3 py-1 font-bold text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 mb-1">Visibilidad Columnas</div>
                        @foreach ($visible_columns as $col => $visible)
                            <label class="flex items-center gap-2 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer text-gray-700 dark:text-gray-300 select-none">
                                <input type="checkbox" wire:click="toggleColumna('{{ $col }}')" {{ $visible ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5">
                                <span class="capitalize">{{ str_replace('_', ' ', $col) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" wire:model.live="search" placeholder="Buscar..."
                        class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg py-1.5 pl-9 pr-3 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-700 dark:text-gray-300 transition duration-150">
                </div>
            </div>

            @if (count($resultados) > 0)
                <div>
                    <button wire:click="auditarTodo"
                        class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-1.5 px-4 rounded-lg shadow-sm transition duration-150 cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Auditar Resultados</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Table Section --}}
        <div class="flex-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl overflow-hidden shadow-sm flex flex-col min-h-[350px] mb-4">
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200/80 dark:border-gray-800/80 select-none">
                            @if ($visible_columns['clave'])
                                <th class="p-3 font-semibold w-24">Clave</th>
                            @endif
                            @if ($visible_columns['producto'])
                                <th class="p-3 font-semibold">Producto</th>
                            @endif
                            @if ($visible_columns['existencia'])
                                <th class="p-3 font-semibold text-center w-24">Existencia</th>
                            @endif
                            @if ($visible_columns['precio_compra'])
                                <th class="p-3 font-semibold text-right w-36">Precio de Compra</th>
                            @endif
                            @if ($visible_columns['nuevo_precio_compra'])
                                <th class="p-3 font-semibold text-right w-44">Nuevo Precio de Compra</th>
                            @endif
                            @if ($visible_columns['precio_final'])
                                <th class="p-3 font-semibold text-right w-32">Precio final</th>
                            @endif
                            @if ($visible_columns['inventario_posterior'])
                                <th class="p-3 font-semibold text-center w-36">Inventario posterior</th>
                            @endif
                            <th class="p-3 font-semibold text-right w-24">Auditoría</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @php
                            $offset = ($page - 1) * $per_page;
                            $paginated = array_slice($resultados, $offset, $per_page);
                        @endphp
                        
                        @forelse ($paginated as $item)
                            @php
                                $inventarioPost = $item['existencia'] + ($item['audited'] ? 0 : $item['retornado']);
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors {{ $item['audited'] ? 'bg-green-50/20 dark:bg-green-950/5 text-gray-400 dark:text-gray-500' : '' }}">
                                @if ($visible_columns['clave'])
                                    <td class="p-3 font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $item['clave'] }}
                                    </td>
                                @endif
                                @if ($visible_columns['producto'])
                                    <td class="p-3">
                                        <div class="font-bold text-gray-800 dark:text-gray-200 {{ $item['audited'] ? 'text-gray-400 dark:text-gray-500' : '' }}">{{ $item['producto'] }}</div>
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 flex items-center gap-2 mt-0.5">
                                            <span class="px-1.5 py-0.2 bg-gray-100 dark:bg-gray-800 rounded font-medium">{{ $item['linea'] }}</span>
                                            <span>Ruta: {{ $item['vendedor'] }}</span>
                                            @if ($item['audited'])
                                                <span class="px-1 py-0.2 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded font-bold text-[9px] uppercase">AUDITADO</span>
                                            @else
                                                <span class="px-1 py-0.2 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded font-bold text-[9px] uppercase">Retorno: +{{ $item['retornado'] }} pzs</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                @if ($visible_columns['existencia'])
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['existencia'] > 1000 ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/20 dark:text-yellow-400' }}">
                                            {{ $item['existencia'] }}
                                        </span>
                                    </td>
                                @endif
                                @if ($visible_columns['precio_compra'])
                                    <td class="p-3 text-right font-medium text-gray-600 dark:text-gray-400">
                                        ${{ number_format($item['precio_compra'], 2) }}
                                    </td>
                                @endif
                                @if ($visible_columns['nuevo_precio_compra'])
                                    <td class="p-3 text-right">
                                        @if ($item['audited'])
                                            <span class="font-medium text-gray-400">${{ number_format($item['nuevo_precio_compra'], 2) }}</span>
                                        @else
                                            <div class="flex items-center justify-end gap-1">
                                                <span class="text-gray-400 text-[10px]">$</span>
                                                <input type="number" step="0.01" min="0" value="{{ $item['nuevo_precio_compra'] }}"
                                                    wire:change="updateNuevoPrecioCompra({{ $item['id'] }}, $event.target.value)"
                                                    class="w-20 text-right border border-gray-300 dark:border-gray-700 rounded px-1.5 py-0.5 text-xs bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:outline-none focus:border-blue-500">
                                            </div>
                                        @endif
                                    </td>
                                @endif
                                @if ($visible_columns['precio_final'])
                                    <td class="p-3 text-right font-semibold text-gray-700 dark:text-gray-300">
                                        ${{ number_format($item['precio_final'], 2) }}
                                    </td>
                                @endif
                                @if ($visible_columns['inventario_posterior'])
                                    <td class="p-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold text-green-700 bg-green-50 dark:text-green-400 dark:bg-green-950/20">
                                            {{ $inventarioPost }}
                                        </span>
                                    </td>
                                @endif
                                <td class="p-3 text-right">
                                    @if ($item['audited'])
                                        <span class="text-green-500 font-semibold flex items-center justify-end gap-1 select-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Listo
                                        </span>
                                    @else
                                        <button wire:click="aprobarAuditoria({{ $item['id'] }})"
                                            class="bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-1 px-2.5 rounded text-[10px] transition cursor-pointer"
                                            title="Confirmar Entrada">
                                            Auditar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-gray-400 dark:text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                        <span class="font-medium">Sin Registros</span>
                                        <span class="text-[11px] text-gray-300 dark:text-gray-600">No se encontraron devoluciones o retornos con los filtros seleccionados.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination and footer matching layout --}}
            <div class="bg-gray-50 dark:bg-gray-800/40 border-t border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 select-none">
                <div class="flex items-center gap-2">
                    <span>Filas por Página</span>
                    <div class="relative">
                        <select wire:model.live="per_page" class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2.5 py-1 pr-6 font-semibold outline-none cursor-pointer text-gray-700 dark:text-gray-300">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                @php
                    $total = count($resultados);
                    $start = $total > 0 ? $offset + 1 : 0;
                    $end = min($offset + $per_page, $total);
                    $maxPage = max(1, ceil($total / $per_page));
                @endphp

                <div class="flex items-center gap-6">
                    <span class="font-medium">{{ $start }}-{{ $end }} of {{ $total }}</span>
                    
                    <div class="flex items-center gap-1">
                        {{-- First Page --}}
                        <button wire:click="$set('page', 1)" {{ $page == 1 ? 'disabled' : '' }}
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 disabled:hover:bg-transparent cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            </svg>
                        </button>
                        
                        {{-- Prev Page --}}
                        <button wire:click="$decrement('page')" {{ $page == 1 ? 'disabled' : '' }}
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 disabled:hover:bg-transparent cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        {{-- Next Page --}}
                        <button wire:click="$increment('page')" {{ $page >= $maxPage ? 'disabled' : '' }}
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 disabled:hover:bg-transparent cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Last Page --}}
                        <button wire:click="$set('page', {{ $maxPage }})" {{ $page >= $maxPage ? 'disabled' : '' }}
                            class="p-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 disabled:hover:bg-transparent cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
