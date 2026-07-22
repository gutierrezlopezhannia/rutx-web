<?php

use function Livewire\Volt\{state, layout};
use App\Models\Seller;

layout('layouts.app');

state([
    // Catálogos desde BD
    'vendedores' => fn() => Seller::all()->toArray(),
    
    // Filtros
    'selected_vendedor' => '',
    'fecha_cierre' => date('Y-m-d'),
    
    // Datos cargados
    'cierre_cargado' => false,
    'active_tab' => 'inventario_final', // inventario_final, mermas, devoluciones
    
    // Datos mock del Cierre
    'inventario_final' => [],
    'mermas' => [],
    'devoluciones' => [],
    
    // Notificaciones
    'notification' => '',
    'notification_type' => 'success',
]);

// Consultar cierre
$consultarCierre = function() {
    if (!$this->selected_vendedor) {
        $this->notification = 'Por favor, selecciona un vendedor para consultar el cierre.';
        $this->notification_type = 'error';
        return;
    }

    // Datos simulados de alta fidelidad correspondientes a lo que la app móvil envía por la sincronización de cierre
    $vendedor = collect($this->vendedores)->firstWhere('id', $this->selected_vendedor);
    
    $this->inventario_final = [
        ['codigo' => 'PROD-001', 'descripcion' => 'Bebida Energética 500ml', 'unidades_restantes' => 12],
        ['codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'unidades_restantes' => 35],
        ['codigo' => 'PROD-005', 'descripcion' => 'Refresco de Cola 600ml', 'unidades_restantes' => 8],
    ];

    $this->mermas = [
        ['codigo' => 'PROD-003', 'descripcion' => 'Galletas de Chocolate 120g', 'unidades' => 2, 'motivo' => 'Empaque dañado en trayecto'],
        ['codigo' => 'PROD-004', 'descripcion' => 'Papas Fritas Clásicas 50g', 'unidades' => 1, 'motivo' => 'Producto caducado'],
    ];

    $this->devoluciones = [
        ['cliente' => 'CLIENTE PRUEBA 01', 'codigo' => 'PROD-006', 'descripcion' => 'Jugo de Naranja 1L', 'unidades' => 3, 'motivo' => 'Exceso de pedido solicitado'],
        ['cliente' => 'CLIENTE PRUEBA 03', 'codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'unidades' => 5, 'motivo' => 'Mal estado del envase'],
    ];

    $this->cierre_cargado = true;
    $this->notification = 'Cierre cargado con éxito. Listo para conciliación.';
    $this->notification_type = 'success';
};

// Conciliar cierre
$conciliarCierre = function() {
    $this->cierre_cargado = false;
    $this->selected_vendedor = '';
    $this->notification = 'Cierre de ruta conciliado correctamente. Los artículos han sido reingresados a almacén.';
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
            <span class="text-[#003859] dark:text-blue-400 font-bold">Cierre de Ruta</span>
        </div>

        {{-- Header & Notificaciones --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Cierre de Ruta / Liquidación de Inventario</h1>
            
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
        <div class="flex flex-wrap items-end gap-6 mb-6 px-1">
            {{-- Vendedor --}}
            <div class="w-64 relative border-b border-gray-300 dark:border-gray-700 pb-1">
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
            <div class="w-48 relative border-b border-gray-300 dark:border-gray-700 pb-1">
                <label class="block text-[10px] text-gray-400 dark:text-gray-500 mb-0.5 font-bold">FECHA DEL CIERRE</label>
                <input type="date" wire:model.live="fecha_cierre" class="w-full bg-transparent text-xs text-gray-700 dark:text-gray-300 outline-none border-none p-0 focus:ring-0">
            </div>

            {{-- Botón Buscar --}}
            <button wire:click="consultarCierre"
                class="px-5 py-1.5 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition duration-150 cursor-pointer">
                Consultar Cierre
            </button>
        </div>

        @if ($cierre_cargado)
            {{-- Tabs de Navegación --}}
            <div class="flex border-b border-gray-200 dark:border-gray-800 mb-6">
                <button wire:click="$set('active_tab', 'inventario_final')"
                    class="py-2.5 px-4 text-xs font-bold border-b-2 transition duration-150 cursor-pointer
                        {{ $active_tab === 'inventario_final' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600' }}">
                    Inventario Restante ({{ count($inventario_final) }})
                </button>
                <button wire:click="$set('active_tab', 'mermas')"
                    class="py-2.5 px-4 text-xs font-bold border-b-2 transition duration-150 cursor-pointer
                        {{ $active_tab === 'mermas' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600' }}">
                    Mermas ({{ count($mermas) }})
                </button>
                <button wire:click="$set('active_tab', 'devoluciones')"
                    class="py-2.5 px-4 text-xs font-bold border-b-2 transition duration-150 cursor-pointer
                        {{ $active_tab === 'devoluciones' ? 'border-orange-500 text-orange-500 font-bold' : 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-600' }}">
                    Devoluciones de Clientes ({{ count($devoluciones) }})
                </button>
            </div>

            {{-- Contenido de los Tabs --}}
            <div class="flex-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm p-5 flex flex-col mb-6">
                
                {{-- TAB 1: Inventario Restante --}}
                @if ($active_tab === 'inventario_final')
                    <div class="flex-1 flex flex-col">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">Reingreso de Artículos a Almacén</h3>
                        <p class="text-xs text-gray-450 dark:text-gray-400 mb-4">Detalle de la mercancía no vendida en la unidad que se reincorporará al stock general.</p>
                        
                        <div class="flex-1 overflow-y-auto max-h-[350px] border border-gray-150 dark:border-gray-800 rounded-lg">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-800">
                                        <th class="p-3">Código</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3 text-center">Unidades Restantes</th>
                                        <th class="p-3 text-center">Acción Propuesta</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach ($inventario_final as $item)
                                        <tr>
                                            <td class="p-3 font-semibold text-gray-700 dark:text-gray-300">{{ $item['codigo'] }}</td>
                                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $item['descripcion'] }}</td>
                                            <td class="p-3 text-center font-bold text-gray-800 dark:text-gray-200">{{ $item['unidades_restantes'] }} pzs</td>
                                            <td class="p-3 text-center">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Reingresar a Stock
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- TAB 2: Mermas --}}
                @if ($active_tab === 'mermas')
                    <div class="flex-1 flex flex-col">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">Mermas y Daños Declarados</h3>
                        <p class="text-xs text-gray-450 dark:text-gray-400 mb-4">Productos inutilizables reportados durante el recorrido de la ruta.</p>
                        
                        <div class="flex-1 overflow-y-auto max-h-[350px] border border-gray-150 dark:border-gray-800 rounded-lg">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-800">
                                        <th class="p-3">Código</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3 text-center">Cantidad</th>
                                        <th class="p-3">Motivo Declarado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach ($mermas as $item)
                                        <tr>
                                            <td class="p-3 font-semibold text-gray-700 dark:text-gray-300">{{ $item['codigo'] }}</td>
                                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $item['descripcion'] }}</td>
                                            <td class="p-3 text-center font-bold text-red-600 dark:text-red-400">{{ $item['unidades'] }} pzs</td>
                                            <td class="p-3">
                                                <span class="text-gray-700 dark:text-gray-300 bg-red-50 dark:bg-red-950/20 px-2 py-0.5 rounded text-[11px] font-medium border border-red-100 dark:border-red-900/30">
                                                    {{ $item['motivo'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- TAB 3: Devoluciones --}}
                @if ($active_tab === 'devoluciones')
                    <div class="flex-1 flex flex-col">
                        <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-3">Devoluciones Efectuadas por Clientes</h3>
                        <p class="text-xs text-gray-450 dark:text-gray-400 mb-4">Listado de productos devueltos por el cliente en el punto de entrega con su respectiva justificación.</p>
                        
                        <div class="flex-1 overflow-y-auto max-h-[350px] border border-gray-150 dark:border-gray-800 rounded-lg">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-800">
                                        <th class="p-3">Cliente</th>
                                        <th class="p-3">Código</th>
                                        <th class="p-3">Descripción</th>
                                        <th class="p-3 text-center">Cantidad Devuelta</th>
                                        <th class="p-3">Causa de Devolución</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach ($devoluciones as $item)
                                        <tr>
                                            <td class="p-3 font-semibold text-gray-800 dark:text-white">{{ $item['cliente'] }}</td>
                                            <td class="p-3 font-semibold text-gray-700 dark:text-gray-300">{{ $item['codigo'] }}</td>
                                            <td class="p-3 text-gray-500 dark:text-gray-400">{{ $item['descripcion'] }}</td>
                                            <td class="p-3 text-center font-bold text-orange-600 dark:text-orange-400">{{ $item['unidades'] }} pzs</td>
                                            <td class="p-3 text-gray-600 dark:text-gray-300">
                                                <span class="bg-yellow-50 dark:bg-yellow-950/20 text-yellow-800 dark:text-yellow-400 px-2 py-0.5 rounded text-[11px] font-medium border border-yellow-100 dark:border-yellow-900/30">
                                                    {{ $item['motivo'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Resumen y Conciliación --}}
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-800 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex gap-6 text-xs text-gray-500 dark:text-gray-400">
                        <div>Reingreso Almacén: <span class="font-bold text-green-600 dark:text-green-400">{{ array_sum(array_column($inventario_final, 'unidades_restantes')) }} pzs</span></div>
                        <div>Total Mermado: <span class="font-bold text-red-600 dark:text-red-400">{{ array_sum(array_column($mermas, 'unidades')) }} pzs</span></div>
                        <div>Devoluciones Clientes: <span class="font-bold text-orange-600 dark:text-orange-400">{{ array_sum(array_column($devoluciones, 'unidades')) }} pzs</span></div>
                    </div>

                    <button wire:click="conciliarCierre"
                        class="px-5 py-2.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold shadow-sm transition-all duration-150 transform hover:-translate-y-0.5 cursor-pointer">
                        Confirmar y Conciliar Inventario
                    </button>
                </div>

            </div>
        @else
            {{-- Estado Vacío (Antes de buscar) --}}
            <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-dashed border-gray-300 dark:border-gray-800 p-8 text-center select-none min-h-[300px]">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Sin información seleccionada</h3>
                <p class="text-xs text-gray-450 dark:text-gray-400 max-w-sm">Selecciona una ruta o vendedor superior y haz clic en "Consultar Cierre" para cargar la liquidación del día.</p>
            </div>
        @endif

    </div>
</div>
