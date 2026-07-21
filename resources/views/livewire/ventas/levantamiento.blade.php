<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

// ── Mock Data ────────────────────────────────────────────────
$mockZonas = [['id' => '1Z', 'nombre' => '1Z - Zona 1'], ['id' => '2Z', 'nombre' => '2Z - Zona 2'], ['id' => '3Z', 'nombre' => '3Z - Zona 3']];

$mockVendedores = [['id' => '4686', 'nombre' => '4686 - RUTA06', 'zona_id' => '1Z'], ['id' => '3201', 'nombre' => '3201 - RUTA01', 'zona_id' => '1Z'], ['id' => '4102', 'nombre' => '4102 - RUTA02', 'zona_id' => '2Z'], ['id' => '5210', 'nombre' => '5210 - RUTA03', 'zona_id' => '2Z'], ['id' => '6100', 'nombre' => '6100 - RUTA07', 'zona_id' => '3Z']];

$mockClientes = [
    ['id' => 'R10', 'nombre' => 'R10 CLIENTE RUTA 10 EVENTUAL', 'vendedor_id' => '4686', 'clave' => 'R10', 'razon_social' => 'CLIENTE RUTA 10 EVENTUAL', 'direccion' => 'Av. Principal 123', 'ruta' => '4690 RUTA10', 'rfc' => '', 'tipo_venta' => 'Contado', 'limite_credito' => 0.0, 'saldo' => 0.0, 'credito_restante' => 0.0, 'lista_precio' => 'Precio de lista', 'plazo_credito' => 0.0],
    ['id' => 'C01', 'nombre' => 'C01 ABARROTES LA ESQUINA', 'vendedor_id' => '4686', 'clave' => 'C01', 'razon_social' => 'ABARROTES LA ESQUINA', 'direccion' => 'Calle 5 de Mayo 45', 'ruta' => '4686 RUTA06', 'rfc' => 'AEL880512DEF', 'tipo_venta' => 'Contado', 'limite_credito' => 5000.0, 'saldo' => 1200.0, 'credito_restante' => 3800.0, 'lista_precio' => 'Precio de lista', 'plazo_credito' => 30.0],
    ['id' => 'C02', 'nombre' => 'C02 MINISUPER CENTRAL', 'vendedor_id' => '3201', 'clave' => 'C02', 'razon_social' => 'MINISUPER CENTRAL', 'direccion' => 'Blvd. Norte 890', 'ruta' => '3201 RUTA01', 'rfc' => 'MIC991201GHI', 'tipo_venta' => 'Crédito', 'limite_credito' => 15000.0, 'saldo' => 5000.0, 'credito_restante' => 10000.0, 'lista_precio' => 'Precio especial', 'plazo_credito' => 60.0],
    ['id' => 'C03', 'nombre' => 'C03 TIENDA DON PEPE', 'vendedor_id' => '4102', 'clave' => 'C03', 'razon_social' => 'TIENDA DON PEPE', 'direccion' => '1ra Sur S/N', 'ruta' => '4102 RUTA02', 'rfc' => '', 'tipo_venta' => 'Contado', 'limite_credito' => 0.0, 'saldo' => 0.0, 'credito_restante' => 0.0, 'lista_precio' => 'Precio de lista', 'plazo_credito' => 0.0],
    ['id' => 'C04', 'nombre' => 'C04 BODEGA NORTE', 'vendedor_id' => '5210', 'clave' => 'C04', 'razon_social' => 'BODEGA NORTE', 'direccion' => 'Periférico Norte 500', 'ruta' => '5210 RUTA03', 'rfc' => 'BNO010101XYZ', 'tipo_venta' => 'Crédito', 'limite_credito' => 20000.0, 'saldo' => 0.0, 'credito_restante' => 20000.0, 'lista_precio' => 'Precio de lista', 'plazo_credito' => 30.0],
    ['id' => 'C05', 'nombre' => 'C05 MISCELANEA EL CRUCERO', 'vendedor_id' => '6100', 'clave' => 'C05', 'razon_social' => 'MISCELANEA EL CRUCERO', 'direccion' => 'Carretera Federal Km 5', 'ruta' => '6100 RUTA07', 'rfc' => '', 'tipo_venta' => 'Contado', 'limite_credito' => 0.0, 'saldo' => 0.0, 'credito_restante' => 0.0, 'lista_precio' => 'Precio de lista', 'plazo_credito' => 0.0],
];

$mockProductos = [['id' => 'PP500', 'nombre' => 'PP500 - PAPA 500g', 'linea' => 'PAPAS', 'existencia' => 919, 'precio' => 120.0, 'impuesto' => 0], ['id' => 'CHPN1', 'nombre' => 'CHPN1 CHICHARRIN PAPA NUBE', 'linea' => 'CHICHARRINES', 'existencia' => 59, 'precio' => 14.0, 'impuesto' => 0], ['id' => 'CHPAL1', 'nombre' => 'CHPAL1 CHICHARRIN PAPA PALITO', 'linea' => 'CHICHARRINES', 'existencia' => 84, 'precio' => 14.0, 'impuesto' => 0], ['id' => 'SAL001', 'nombre' => 'SAL001 SAL DE MESA 1KG', 'linea' => 'CONDIMENTOS', 'existencia' => 200, 'precio' => 18.0, 'impuesto' => 0], ['id' => 'ACE001', 'nombre' => 'ACE001 ACEITE VEGETAL 1L', 'linea' => 'ACEITES', 'existencia' => 150, 'precio' => 45.0, 'impuesto' => 16], ['id' => 'ARR001', 'nombre' => 'ARR001 ARROZ MORELOS 1KG', 'linea' => 'GRANOS', 'existencia' => 320, 'precio' => 22.0, 'impuesto' => 0]];

$mockRutasEntrega = [['id' => 'RE01', 'nombre' => 'RE01 - Ruta de Entrega Norte'], ['id' => 'RE02', 'nombre' => 'RE02 - Ruta de Entrega Sur'], ['id' => 'RE03', 'nombre' => 'RE03 - Ruta de Entrega Centro']];

// ── State ────────────────────────────────────────────────────
state([
    'zonas' => $mockZonas,
    'todosVendedores' => $mockVendedores,
    'todosClientes' => $mockClientes,
    'productos' => $mockProductos,
    'rutasEntrega' => $mockRutasEntrega,
    'vendedoresFiltrados' => [],
    'clientesFiltrados' => [],
    'selected_zona' => '',
    'selected_vendedor' => '',
    'selected_cliente' => '',
    'cliente_info' => null,
    'tipo_inventario' => '',
    'movimiento' => '',
    'tipo_venta' => '',
    'ruta_entrega' => '',
    'selected_producto' => '',
    'producto_info' => null,
    'piezas' => 1,
    'productos_pedido' => [],
    'comentario' => '',
    'notification' => '',
    'resumen' => ['productos' => 0, 'subtotal' => 0.0, 'impuesto' => 0.0, 'descuento' => 0.0, 'total' => 0.0],
]);

// ── Cascada Zona → Vendedores ────────────────────────────────
$updatedSelectedZona = function () {
    $this->selected_vendedor = '';
    $this->selected_cliente = '';
    $this->cliente_info = null;
    $this->movimiento = '';
    $this->tipo_inventario = '';
    $this->productos_pedido = [];
    $this->resetResumen();

    if (!empty($this->selected_zona)) {
        $this->vendedoresFiltrados = collect($this->todosVendedores)->where('zona_id', $this->selected_zona)->values()->toArray();
        $this->tipo_inventario = 'Inv. General';
    } else {
        $this->vendedoresFiltrados = [];
    }
    $this->clientesFiltrados = [];
};

// ── Cascada Vendedor → Clientes ──────────────────────────────
$updatedSelectedVendedor = function () {
    $this->selected_cliente = '';
    $this->cliente_info = null;
    $this->movimiento = '';
    $this->productos_pedido = [];
    $this->resetResumen();

    if (!empty($this->selected_vendedor)) {
        $this->clientesFiltrados = collect($this->todosClientes)->where('vendedor_id', $this->selected_vendedor)->values()->toArray();
    } else {
        $this->clientesFiltrados = [];
    }
};

// ── Cliente seleccionado → Ficha ─────────────────────────────
$updatedSelectedCliente = function () {
    $this->movimiento = '';
    $this->tipo_venta = '';
    $this->ruta_entrega = '';
    $this->productos_pedido = [];
    $this->resetResumen();

    if (!empty($this->selected_cliente)) {
        $this->cliente_info = collect($this->todosClientes)->firstWhere('id', $this->selected_cliente);
        if ($this->cliente_info) {
            $this->tipo_venta = $this->cliente_info['tipo_venta'];
        }
    } else {
        $this->cliente_info = null;
    }
};

// ── Movimiento → reset productos ─────────────────────────────
$updatedMovimiento = function () {
    $this->productos_pedido = [];
    $this->selected_producto = '';
    $this->producto_info = null;
    $this->piezas = 1;
    $this->resetResumen();
};

// ── Producto seleccionado → Info ──────────────────────────────
$updatedSelectedProducto = function () {
    if (!empty($this->selected_producto)) {
        $this->producto_info = collect($this->productos)->firstWhere('id', $this->selected_producto);
        $this->piezas = 1;
    } else {
        $this->producto_info = null;
    }
};

// ── Agregar producto al pedido ────────────────────────────────
$agregarProducto = function () {
    if (empty($this->selected_producto) || intval($this->piezas) < 1) {
        return;
    }

    $prod = collect($this->productos)->firstWhere('id', $this->selected_producto);
    if (!$prod) {
        return;
    }

    $piezas = intval($this->piezas);
    $precio = $prod['precio'];
    $imp_pct = $prod['impuesto'];
    $subtotal = $precio * $piezas;
    $impuesto = round(($subtotal * $imp_pct) / 100, 2);

    $existingIdx = null;
    foreach ($this->productos_pedido as $i => $p) {
        if ($p['id'] === $this->selected_producto) {
            $existingIdx = $i;
            break;
        }
    }

    if ($existingIdx !== null) {
        $np = $this->productos_pedido[$existingIdx]['piezas'] + $piezas;
        $newSub = $precio * $np;
        $newImp = round(($newSub * $imp_pct) / 100, 2);
        $this->productos_pedido[$existingIdx]['piezas'] = $np;
        $this->productos_pedido[$existingIdx]['subtotal'] = $newSub;
        $this->productos_pedido[$existingIdx]['impuesto_monto'] = $newImp;
        $this->productos_pedido[$existingIdx]['total'] = $newSub + $newImp;
    } else {
        $this->productos_pedido[] = [
            'id' => $prod['id'],
            'nombre' => $prod['nombre'],
            'precio' => $precio,
            'piezas' => $piezas,
            'existencia' => $prod['existencia'],
            'subtotal' => $subtotal,
            'impuesto_pct' => $imp_pct,
            'impuesto_monto' => $impuesto,
            'descuento' => 0,
            'total' => $subtotal + $impuesto,
        ];
    }

    $this->calcularResumen();
    $this->selected_producto = '';
    $this->producto_info = null;
    $this->piezas = 1;
};

// ── +/- piezas desde tabla ───────────────────────────────────
$actualizarPiezas = function ($index, $delta) {
    if (!isset($this->productos_pedido[$index])) {
        return;
    }
    $np = $this->productos_pedido[$index]['piezas'] + $delta;
    if ($np < 1) {
        return;
    }

    $precio = $this->productos_pedido[$index]['precio'];
    $imp_pct = $this->productos_pedido[$index]['impuesto_pct'];
    $newSub = $precio * $np;
    $newImp = round(($newSub * $imp_pct) / 100, 2);

    $this->productos_pedido[$index]['piezas'] = $np;
    $this->productos_pedido[$index]['subtotal'] = $newSub;
    $this->productos_pedido[$index]['impuesto_monto'] = $newImp;
    $this->productos_pedido[$index]['total'] = $newSub + $newImp;
    $this->calcularResumen();
};

// ── Eliminar producto ─────────────────────────────────────────
$eliminarProducto = function ($index) {
    array_splice($this->productos_pedido, $index, 1);
    $this->calcularResumen();
};

// ── Calcular resumen ──────────────────────────────────────────
$calcularResumen = function () {
    $col = collect($this->productos_pedido);
    $this->resumen = [
        'productos' => $col->count(),
        'subtotal' => $col->sum('subtotal'),
        'impuesto' => $col->sum('impuesto_monto'),
        'descuento' => $col->sum('descuento'),
        'total' => $col->sum('total'),
    ];
};

// ── Reset resumen ─────────────────────────────────────────────
$resetResumen = function () {
    $this->resumen = ['productos' => 0, 'subtotal' => 0.0, 'impuesto' => 0.0, 'descuento' => 0.0, 'total' => 0.0];
};

// ── Enviar ────────────────────────────────────────────────────
$enviarVenta = function () {
    $this->notification = ucfirst($this->movimiento) . ' enviada correctamente (mock).';
    $this->selected_cliente = '';
    $this->cliente_info = null;
    $this->movimiento = '';
    $this->tipo_venta = '';
    $this->ruta_entrega = '';
    $this->productos_pedido = [];
    $this->selected_producto = '';
    $this->producto_info = null;
    $this->comentario = '';
    $this->resetResumen();
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Levantamiento</span>
            </div>

            {{-- Notificación --}}
            @if ($notification)
                <div
                    class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $notification }}
                </div>
            @endif

            {{-- ── SECCIÓN 1: Zona / Vendedor / Cliente ── --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-6 mb-4">
                <h2 class="text-base font-bold text-gray-800 mb-5">Levantamiento de Venta</h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-4">

                    {{-- Columna Izquierda: Zona + Vendedor + Tipo Inventario --}}
                    <div class="space-y-5">

                        {{-- Zona --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Zona</label>
                            <div class="relative">
                                <select wire:model.live="selected_zona"
                                    class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                    <option value="">Zona</option>
                                    @foreach ($zonas as $zona)
                                        <option value="{{ $zona['id'] }}">{{ $zona['nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Vendedor (aparece al escoger zona) --}}
                        @if (!empty($selected_zona))
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Vendedor</label>
                                <div class="relative">
                                    <select wire:model.live="selected_vendedor"
                                        class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                        <option value="">Vendedor</option>
                                        @foreach ($vendedoresFiltrados as $v)
                                            <option value="{{ $v['id'] }}">{{ $v['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($selected_vendedor))
                                <p class="text-xs text-gray-500">
                                    Tipo de Inventario: <span
                                        class="font-bold text-gray-800">{{ $tipo_inventario }}</span>
                                </p>
                            @endif
                        @endif
                    </div>

                    {{-- Columna Derecha: Cliente + Ficha --}}
                    <div>
                        @if (!empty($selected_vendedor))
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Cliente</label>
                                <div class="relative">
                                    <select wire:model.live="selected_cliente"
                                        class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                        <option value="">Cliente</option>
                                        @foreach ($clientesFiltrados as $c)
                                            <option value="{{ $c['id'] }}">{{ $c['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Ficha del cliente --}}
                            @if ($cliente_info)
                                <div
                                    class="mt-4 grid grid-cols-2 gap-x-8 gap-y-2 text-xs border-t border-gray-100 pt-3">
                                    <div><span class="text-gray-400">Clave:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['clave'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Límite de Crédito:</span> <span
                                            class="font-semibold text-gray-800">${{ number_format($cliente_info['limite_credito'], 3) }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Nombre:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['razon_social'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Saldo:</span> <span
                                            class="font-semibold text-gray-800">${{ number_format($cliente_info['saldo'], 3) }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Dirección:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['direccion'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Crédito restante:</span> <span
                                            class="font-semibold text-gray-800">${{ number_format($cliente_info['credito_restante'], 3) }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Ruta:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['ruta'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Lista de Precio:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['lista_precio'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">RFC:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['rfc'] ?: '-' }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Plazo crédito:</span> <span
                                            class="font-semibold text-gray-800">${{ number_format($cliente_info['plazo_credito'], 3) }}</span>
                                    </div>
                                    <div class="col-span-2"><span class="text-gray-400">Tipo de Venta:</span> <span
                                            class="font-semibold text-gray-800">{{ $cliente_info['tipo_venta'] }}</span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN 2: Movimiento ── --}}
            @if ($cliente_info)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 mb-4">
                    <div class="flex flex-wrap items-end gap-6">

                        {{-- Movimiento --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Movimiento</label>
                            <div class="relative w-44">
                                <select wire:model.live="movimiento"
                                    class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                    <option value="">Movimiento</option>
                                    <option value="Venta">Venta</option>
                                    <option value="Preventa">Preventa</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Tipo de Venta (si Venta) --}}
                        @if ($movimiento === 'Venta')
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tipo de Venta</label>
                                <div class="relative w-44">
                                    <select wire:model.live="tipo_venta"
                                        class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                        <option value="Contado">Contado</option>
                                        <option value="Crédito">Crédito</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Ruta de Entrega (si Preventa) --}}
                        @if ($movimiento === 'Preventa')
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Ruta de Entrega</label>
                                <div class="relative w-56">
                                    <select wire:model.live="ruta_entrega"
                                        class="w-full appearance-none border-b border-gray-300 bg-transparent py-2 pr-8 pl-0 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                        <option value="">Ruta de Entrega</option>
                                        @foreach ($rutasEntrega as $r)
                                            <option value="{{ $r['id'] }}">{{ $r['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ── SECCIÓN 3: Agregar Producto + Resumen ── --}}
            @if (!empty($movimiento))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

                    {{-- Agregar Producto (2/3) --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 h-full">
                            <h3 class="text-sm font-bold text-gray-800 mb-4">Agregar Producto</h3>

                            {{-- Producto dropdown --}}
                            <div class="mb-3">
                                <label class="block text-xs text-gray-400 mb-1">Producto</label>
                                <div class="relative">
                                    <select wire:model.live="selected_producto"
                                        class="w-full appearance-none border border-gray-200 rounded-lg pl-3 pr-10 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white">
                                        <option value="">Producto</option>
                                        @foreach ($productos as $p)
                                            <option value="{{ $p['id'] }}">{{ $p['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Info del producto --}}
                            @if ($producto_info)
                                <div class="grid grid-cols-2 gap-x-8 gap-y-1 text-xs mb-3 p-3 bg-gray-50 rounded-lg">
                                    <div><span class="text-gray-400">Línea:</span> <span
                                            class="font-semibold text-gray-800">{{ $producto_info['linea'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Existencia:</span> <span
                                            class="font-semibold text-gray-800">{{ $producto_info['existencia'] }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Precio:</span> <span
                                            class="font-semibold text-gray-800">${{ number_format($producto_info['precio'], 2) }}</span>
                                    </div>
                                    <div><span class="text-gray-400">Impuesto:</span> <span
                                            class="font-semibold text-gray-800">{{ $producto_info['impuesto'] }}%</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Piezas --}}
                            <div class="mb-3">
                                <input type="number" wire:model.live="piezas" min="1" placeholder="Piezas"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#003859]" />
                            </div>

                            {{-- Botón Agregar --}}
                            <button wire:click="agregarProducto"
                                class="w-full py-2 rounded-lg text-sm font-semibold transition duration-150 {{ !empty($selected_producto) ? 'bg-[#003859] text-white hover:bg-[#004f7c]' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}">
                                Agregar
                            </button>
                        </div>
                    </div>

                    {{-- Resumen del Pedido (1/3) --}}
                    <div>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 sticky top-4">
                            <h3 class="text-sm font-bold text-gray-800 mb-4">Resumen del Pedido</h3>

                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex justify-between">
                                    <span>Productos</span>
                                    <span class="font-semibold text-gray-800">{{ $resumen['productos'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span
                                        class="font-semibold text-gray-800">${{ number_format($resumen['subtotal'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Impuesto</span>
                                    <span
                                        class="font-semibold text-gray-800">${{ number_format($resumen['impuesto'], 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Descuento</span>
                                    <span
                                        class="font-semibold text-gray-800">-${{ number_format($resumen['descuento'], 2) }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-2 mt-1">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span
                                        class="font-bold text-gray-900 text-base">${{ number_format($resumen['total'], 2) }}</span>
                                </div>
                            </div>

                            <textarea wire:model="comentario" placeholder="Comentario"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#003859] resize-none mb-3"
                                rows="2"></textarea>

                            <button wire:click="enviarVenta"
                                class="w-full py-2 rounded-lg bg-[#003859] text-white text-sm font-semibold hover:bg-[#004f7c] transition duration-150">
                                Enviar {{ $movimiento }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── SECCIÓN 4: Tabla de productos (ancho completo) ── --}}
                @if (count($productos_pedido) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 overflow-hidden">
                        <div class="px-6 pt-4 border-b border-gray-200">
                            <span
                                class="text-sm font-semibold text-[#003859] border-b-2 border-[#003859] pb-3 inline-block">
                                {{ $movimiento }}
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-left">
                                <thead>
                                    <tr
                                        class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                        <th class="px-6 py-4">Producto</th>
                                        <th class="px-6 py-4">Precio</th>
                                        <th class="px-6 py-4">Piezas</th>
                                        <th class="px-6 py-4">Subtotal</th>
                                        <th class="px-6 py-4">Impuesto</th>
                                        <th class="px-6 py-4">Descuento</th>
                                        <th class="px-6 py-4">Total</th>
                                        <th class="px-6 py-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-700">
                                    @foreach ($productos_pedido as $i => $p)
                                        <tr class="hover:bg-gray-50/40 transition duration-150">
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                {{ $p['nombre'] }}
                                                <div class="text-gray-400 text-xs mt-0.5">Existencia:
                                                    {{ $p['existencia'] }}</div>
                                            </td>
                                            <td class="px-6 py-4">${{ number_format($p['precio'], 2) }}</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <button wire:click="actualizarPiezas({{ $i }}, -1)"
                                                        class="w-7 h-7 rounded border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold text-base">−</button>
                                                    <span
                                                        class="w-8 text-center font-semibold">{{ $p['piezas'] }}</span>
                                                    <button wire:click="actualizarPiezas({{ $i }}, 1)"
                                                        class="w-7 h-7 rounded border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 font-bold text-base">+</button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">${{ number_format($p['subtotal'], 2) }}</td>
                                            <td class="px-6 py-4">${{ number_format($p['impuesto_monto'], 2) }}</td>
                                            <td class="px-6 py-4">-${{ number_format($p['descuento'], 2) }}</td>
                                            <td class="px-6 py-4 font-semibold text-gray-900">
                                                ${{ number_format($p['total'], 2) }}</td>
                                            <td class="px-6 py-4">
                                                <button wire:click="eliminarProducto({{ $i }})"
                                                    class="text-gray-400 hover:text-red-500 transition duration-150">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            @endif

        </div>
    </div>
</div>
