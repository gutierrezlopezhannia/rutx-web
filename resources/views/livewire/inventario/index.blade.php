<?php

use function Livewire\Volt\{state, layout};
use App\Models\Zone;
use App\Models\Seller;

layout('layouts.app');

state([
    // Catálogos desde BD
    'zonas' => fn() => Zone::all()->toArray(),
    'vendedores' => fn() => Seller::all()->toArray(),
    
    // Filtros
    'selected_zona' => '',
    'selected_vendedor' => '',
    'fecha_carga' => date('Y-m-d'),
    
    // Búsqueda de Productos
    'search' => '',
    'productos' => [
        ['id' => 1, 'codigo' => 'PROD-001', 'descripcion' => 'Bebida Energética 500ml', 'linea' => 'Bebidas', 'precio' => 25.00, 'existencia' => 1500],
        ['id' => 2, 'codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'linea' => 'Bebidas', 'precio' => 12.00, 'existencia' => 3200],
        ['id' => 3, 'codigo' => 'PROD-003', 'descripcion' => 'Galletas de Chocolate 120g', 'linea' => 'Abarrotes', 'precio' => 18.50, 'existencia' => 850],
        ['id' => 4, 'codigo' => 'PROD-004', 'descripcion' => 'Papas Fritas Clásicas 50g', 'linea' => 'Botanas', 'precio' => 15.00, 'existencia' => 2100],
        ['id' => 5, 'codigo' => 'PROD-005', 'descripcion' => 'Refresco de Cola 600ml', 'linea' => 'Bebidas', 'precio' => 18.00, 'existencia' => 4500],
        ['id' => 6, 'codigo' => 'PROD-006', 'descripcion' => 'Jugo de Naranja 1L', 'linea' => 'Bebidas', 'precio' => 22.00, 'existencia' => 1000],
        ['id' => 7, 'codigo' => 'PROD-007', 'descripcion' => 'Cacahuate Japonés 100g', 'linea' => 'Botanas', 'precio' => 10.00, 'existencia' => 5000],
    ],
    'productosFiltrados' => [],
    
    // Plan de Carga actual
    'plan_carga' => [],
    'producto_seleccionado_id' => '',
    'cantidad_a_cargar' => 1,
    
    // Responsiva Modal
    'show_responsiva_modal' => false,
    'texto_responsiva' => 'Por medio de la presente, el chofer/vendedor abajo firmante hace constar que recibe a entera conformidad la cantidad de mercancía detallada en este documento para su distribución y venta en la ruta establecida. Se compromete a liquidar el valor total de las ventas en efectivo, depósitos bancarios, o reportar el inventario devuelto y mermado al final del día.',
    
    // Mensajes
    'notification' => '',
    'notification_type' => 'success',
]);

// Inicialización de filtros de productos
$init = function() {
    $this->productosFiltrados = $this->productos;
};

// Al actualizar la búsqueda de productos
$updatedSearch = function() {
    if (empty($this->search)) {
        $this->productosFiltrados = $this->productos;
    } else {
        $query = strtolower(trim($this->search));
        $this->productosFiltrados = collect($this->productos)->filter(function($p) use ($query) {
            return str_contains(strtolower($p['codigo']), $query) || str_contains(strtolower($p['descripcion']), $query);
        })->values()->toArray();
    }
};

// Agregar producto al plan de carga
$agregarAlPlan = function($productoId, $cantidad) {
    if (!$this->selected_vendedor) {
        $this->notification = 'Por favor, selecciona un vendedor antes de agregar productos.';
        $this->notification_type = 'error';
        return;
    }
    
    if ($cantidad <= 0) {
        $this->notification = 'La cantidad debe ser mayor que cero.';
        $this->notification_type = 'error';
        return;
    }

    $prod = collect($this->productos)->firstWhere('id', $productoId);
    if (!$prod) return;

    if ($cantidad > $prod['existencia']) {
        $this->notification = 'La cantidad excede la existencia disponible en almacén.';
        $this->notification_type = 'error';
        return;
    }

    // Verificar si ya existe en el plan
    $index = collect($this->plan_carga)->search(fn($item) => $item['id'] === $productoId);

    if ($index !== false) {
        $nuevoTotal = $this->plan_carga[$index]['cantidad'] + $cantidad;
        if ($nuevoTotal > $prod['existencia']) {
            $this->notification = 'La cantidad total acumulada excede la existencia.';
            $this->notification_type = 'error';
            return;
        }
        $this->plan_carga[$index]['cantidad'] = $nuevoTotal;
    } else {
        $this->plan_carga[] = [
            'id' => $prod['id'],
            'codigo' => $prod['codigo'],
            'descripcion' => $prod['descripcion'],
            'linea' => $prod['linea'],
            'precio' => $prod['precio'],
            'cantidad' => intval($cantidad),
        ];
    }

    $this->notification = 'Producto agregado al plan de carga.';
    $this->notification_type = 'success';
    $this->cantidad_a_cargar = 1;
};

// Eliminar producto del plan
$eliminarDelPlan = function($productoId) {
    $this->plan_carga = collect($this->plan_carga)->reject(fn($item) => $item['id'] === $productoId)->values()->toArray();
    $this->notification = 'Producto eliminado del plan.';
    $this->notification_type = 'info';
};

// Actualizar cantidad de un producto directamente en la tabla
$actualizarCantidad = function($productoId, $nuevaCantidad) {
    $prod = collect($this->productos)->firstWhere('id', $productoId);
    if (!$prod) return;

    if ($nuevaCantidad <= 0) {
        $this->eliminarDelPlan($productoId);
        return;
    }

    if ($nuevaCantidad > $prod['existencia']) {
        $this->notification = 'La cantidad excede la existencia disponible.';
        $this->notification_type = 'error';
        return;
    }

    $index = collect($this->plan_carga)->search(fn($item) => $item['id'] === $productoId);
    if ($index !== false) {
        $this->plan_carga[$index]['cantidad'] = intval($nuevaCantidad);
    }
};

// Guardar y mostrar responsiva
$guardarPlan = function() {
    if (empty($this->plan_carga)) {
        $this->notification = 'El plan de carga está vacío. Agrega productos antes de guardar.';
        $this->notification_type = 'error';
        return;
    }

    $this->show_responsiva_modal = true;
};

// Confirmar y procesar carga
$confirmarCarga = function() {
    $this->show_responsiva_modal = false;
    $this->plan_carga = [];
    $this->notification = 'Plan de carga guardado exitosamente. Se ha generado la orden de salida.';
    $this->notification_type = 'success';
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
            <span class="text-[#003859] dark:text-blue-400 font-bold">Plan de Carga</span>
        </div>

        {{-- Header & Notificaciones --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Planificación de Carga a Ruta</h1>
            
            {{-- Toast Notification --}}
            @if ($notification)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; $wire.set('notification', '') }, 3500)"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border text-sm shadow-sm transition-all duration-150 animate-fade-in
                        {{ $notification_type === 'success' ? 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/30 dark:border-green-800 dark:text-green-300' : '' }}
                        {{ $notification_type === 'error' ? 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/30 dark:border-red-800 dark:text-red-300' : '' }}
                        {{ $notification_type === 'info' ? 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300' : '' }}">
                    <span>{{ $notification }}</span>
                </div>
            @endif
        </div>

        {{-- Filtros Superiores --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6 px-1">
            {{-- Zona --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">ZONA</label>
                <select wire:model.live="selected_zona" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="">Selecciona Zona</option>
                    @foreach ($zonas as $z)
                        <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            {{-- Vendedor --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">VENDEDOR / RUTA</label>
                <select wire:model.live="selected_vendedor" class="w-full appearance-none bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none cursor-pointer">
                    <option value="">Selecciona Vendedor</option>
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

            {{-- Fecha --}}
            <div class="relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">FECHA PROGRAMADA</label>
                <input type="date" wire:model.live="fecha_carga" class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>
        </div>

        {{-- Layout Dos Columnas --}}
        <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-[500px]">
            
            {{-- Columna Izquierda: Buscador e Inventario Almacén --}}
            <div class="lg:col-span-5 flex flex-col bg-gray-50 dark:bg-gray-800/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800/80">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#003859] dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Buscar Productos en Almacén
                </h3>
                
                {{-- Input Búsqueda --}}
                <div class="relative mb-4">
                    <input type="text" wire:model.live="search" placeholder="Buscar por código o descripción..."
                        class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg py-2 px-3 pl-9 text-xs focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 focus:ring-1 focus:ring-[#003859] text-gray-700 dark:text-gray-300 transition duration-150">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Tabla Productos --}}
                <div class="flex-1 overflow-y-auto max-h-[420px] rounded-lg border border-gray-200/60 dark:border-gray-800 bg-white dark:bg-gray-800">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200/60 dark:border-gray-800 select-none">
                                <th class="p-2.5">Código / Descripción</th>
                                <th class="p-2.5 text-center">Stock</th>
                                <th class="p-2.5 text-right font-semibold">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($productosFiltrados as $p)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="p-2.5">
                                        <div class="font-bold text-gray-700 dark:text-gray-300">{{ $p['codigo'] }}</div>
                                        <div class="text-[11px] text-gray-400 truncate max-w-[200px]">{{ $p['descripcion'] }}</div>
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p['existencia'] > 1000 ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/20 dark:text-yellow-400' }}">
                                            {{ $p['existencia'] }} pzs
                                        </span>
                                    </td>
                                    <td class="p-2.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5" x-data="{ qty: 1 }">
                                            <input type="number" min="1" max="{{ $p['existencia'] }}" x-model="qty"
                                                class="w-12 text-center border border-gray-200 dark:border-gray-700 rounded py-0.5 text-[11px] focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 bg-transparent text-gray-700 dark:text-gray-300">
                                            <button @click="$wire.agregarAlPlan({{ $p['id'] }}, qty); qty = 1"
                                                class="bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white rounded p-1 transition cursor-pointer"
                                                title="Agregar al Plan">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-400 dark:text-gray-500">No se encontraron productos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Columna Derecha: Plan de Carga del Camión --}}
            <div class="lg:col-span-7 flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <svg class="w-4.5 h-4.5 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                        </svg>
                        Artículos Cargados en el Camión
                    </h3>
                    
                    @if (count($plan_carga) > 0)
                        <button wire:click="$set('plan_carga', [])" class="text-xs text-red-500 hover:text-red-700 font-medium transition cursor-pointer">
                            Vaciar Plan
                        </button>
                    @endif
                </div>

                {{-- Tabla Plan de Carga --}}
                <div class="flex-1 overflow-y-auto max-h-[380px] rounded-lg border border-gray-100 dark:border-gray-800/80 mb-4">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200/60 dark:border-gray-800 select-none">
                                <th class="p-2.5">Código / Descripción</th>
                                <th class="p-2.5">Línea</th>
                                <th class="p-2.5 text-center w-24">Cantidad</th>
                                <th class="p-2.5 text-right w-16">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($plan_carga as $item)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="p-2.5">
                                        <div class="font-bold text-gray-700 dark:text-gray-300">{{ $item['codigo'] }}</div>
                                        <div class="text-[11px] text-gray-400 truncate max-w-[280px]">{{ $item['descripcion'] }}</div>
                                    </td>
                                    <td class="p-2.5">
                                        <span class="text-gray-500 dark:text-gray-400">{{ $item['linea'] }}</span>
                                    </td>
                                    <td class="p-2.5 text-center">
                                        <input type="number" min="1" value="{{ $item['cantidad'] }}"
                                            wire:change="actualizarCantidad({{ $item['id'] }}, $event.target.value)"
                                            class="w-16 text-center border border-gray-200 dark:border-gray-700 rounded py-0.5 text-xs bg-transparent text-gray-700 dark:text-gray-300 focus:outline-none focus:border-[#003859] dark:focus:border-blue-500 font-semibold">
                                    </td>
                                    <td class="p-2.5 text-right">
                                        <button wire:click="eliminarDelPlan({{ $item['id'] }})"
                                            class="text-gray-400 hover:text-red-500 p-1 rounded hover:bg-red-50 dark:hover:bg-red-950/20 transition cursor-pointer"
                                            title="Quitar artículo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-6 text-center text-gray-400 dark:text-gray-500 select-none">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                            </svg>
                                            <span>El camión no tiene carga asignada para hoy.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer del Plan --}}
                <div class="bg-gray-50 dark:bg-gray-800/40 rounded-xl p-4 border border-gray-100 dark:border-gray-800/80 flex flex-wrap justify-between items-center gap-4 mt-auto">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <div>Artículos distintos: <span class="font-bold text-gray-700 dark:text-gray-300">{{ count($plan_carga) }}</span></div>
                        <div class="mt-0.5">Total de piezas: <span class="font-bold text-[#003859] dark:text-blue-400 text-sm">{{ array_sum(array_column($plan_carga, 'cantidad')) }} pzs</span></div>
                    </div>
                    
                    <button wire:click="guardarPlan"
                        class="px-5 py-2 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition-all duration-150 transform hover:-translate-y-0.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
                        {{ empty($plan_carga) ? 'disabled' : '' }}>
                        Guardar Plan de Carga
                    </button>
                </div>
            </div>

        </div>

    </div>

    {{-- Responsiva Modal --}}
    @if ($show_responsiva_modal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/60 transition-opacity"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl animate-scale-up">
                    <div class="bg-white dark:bg-gray-800 px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">
                            Acta Responsiva de Carga a Ruta
                        </h3>
                    </div>
                    <div class="bg-white dark:bg-gray-800 px-6 py-4 text-xs text-gray-600 dark:text-gray-300 space-y-4">
                        <p class="leading-relaxed border-l-4 border-orange-500 pl-3 italic bg-gray-50 dark:bg-gray-800/60 py-2.5">
                            {{ $texto_responsiva }}
                        </p>

                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-white mb-2">Detalle de Mercancía Entregada:</h4>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <table class="w-full text-left border-collapse text-[11px]">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-700">
                                            <th class="p-2">Código</th>
                                            <th class="p-2">Descripción</th>
                                            <th class="p-2 text-center">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                        @foreach ($plan_carga as $item)
                                            <tr>
                                                <td class="p-2 font-semibold text-gray-700 dark:text-gray-300">{{ $item['codigo'] }}</td>
                                                <td class="p-2 text-gray-500 dark:text-gray-400">{{ $item['descripcion'] }}</td>
                                                <td class="p-2 text-center font-bold text-gray-800 dark:text-gray-200">{{ $item['cantidad'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8 pt-8">
                            <div class="text-center border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="font-semibold text-gray-800 dark:text-white">Firma de Entrega</div>
                                <div class="text-[10px] text-gray-450 mt-0.5">Almacén General / Cpanel</div>
                            </div>
                            <div class="text-center border-t border-gray-200 dark:border-gray-700 pt-3">
                                <div class="font-semibold text-gray-800 dark:text-white">Firma de Recibido</div>
                                <div class="text-[10px] text-gray-450 mt-0.5">Vendedor Chofer</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/80 px-6 py-4 flex justify-end gap-3 rounded-b-xl border-t border-gray-100 dark:border-gray-700">
                        <button wire:click="$set('show_responsiva_modal', false)"
                            class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="confirmarCarga"
                            class="px-4 py-2 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition cursor-pointer">
                            Firmar y Salir a Ruta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
