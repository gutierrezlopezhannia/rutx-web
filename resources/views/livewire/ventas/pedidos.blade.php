<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

// ── Mock Data: Pedidos (sin factory) ─────────────────────────
$_movs = ['Venta', 'Preventa', 'Entrega', 'Cobranza', 'NV', 'Devolución'];
$_vends = ['4686 - RUTA06', '3201 - RUTA01', '4102 - RUTA02'];
$_clientes = [['clave' => 'R10', 'nombre' => 'CLIENTE RUTA 10 EVENTUAL'], ['clave' => 'C01', 'nombre' => 'ABARROTES LA ESQUINA'], ['clave' => 'C02', 'nombre' => 'MINISUPER CENTRAL'], ['clave' => 'C03', 'nombre' => 'TIENDA DON PEPE'], ['clave' => 'C04', 'nombre' => 'BODEGA NORTE']];
$_zonas = ['1Z', '2Z', '3Z'];

$mockPedidos = [];
for ($i = 1; $i <= 20; $i++) {
    $mov = $_movs[($i - 1) % 6];
    $sinc = $i % 4 !== 0;
    $cli = $_clientes[$i % 5];
    $sub = round(100 + $i * 47.35, 2);
    $mockPedidos[] = [
        'folio' => str_pad($i, 6, '0', STR_PAD_LEFT),
        'movimiento' => $mov,
        'sincronizado' => $sinc,
        'clave_sincronizado' => $sinc ? 'SYNC-' . str_pad($i, 4, '0', STR_PAD_LEFT) : '',
        'fecha' => date('d/m/Y', mktime(0, 0, 0, date('m'), date('d') - $i, date('Y'))),
        'hora' => sprintf('%02d:%02d', 8 + ($i % 10), ($i * 7) % 60),
        'cancelacion' => '',
        'es_credito' => $i % 3 === 0,
        'comentario' => $i % 5 === 0 ? 'FOLIO-' . $i : '',
        'vendedor' => $_vends[$i % 3],
        'clave_cliente' => $cli['clave'],
        'nombre_cliente' => $cli['nombre'],
        'cobrado' => $mov === 'Cobranza' ? round($sub * 0.8, 2) : 0,
        'subtotal' => $sub,
        'total' => $sub,
        'causa' => '',
        'peso_kgs' => round(2.5 + $i * 1.3, 2),
        'zona' => $_zonas[$i % 3],
    ];
}

// ── Mock Data: Productos ──────────────────────────────────────
$mockProductos = [
    ['producto' => 'PP500 - PAPA 500g', 'contado' => 2400.0, 'credito' => 960.0, 'total_neto' => 3360.0, 'u_contado' => 20, 'u_credito' => 8, 'u_cambio' => 0, 'u_promo' => 2, 'u_total' => 30],
    ['producto' => 'CHPN1 CHICHARRIN PAPA NUBE', 'contado' => 560.0, 'credito' => 280.0, 'total_neto' => 840.0, 'u_contado' => 40, 'u_credito' => 20, 'u_cambio' => 1, 'u_promo' => 5, 'u_total' => 66],
    ['producto' => 'CHPAL1 CHICHARRIN PAPA PALITO', 'contado' => 420.0, 'credito' => 140.0, 'total_neto' => 560.0, 'u_contado' => 30, 'u_credito' => 10, 'u_cambio' => 0, 'u_promo' => 0, 'u_total' => 40],
    ['producto' => 'SAL001 SAL DE MESA 1KG', 'contado' => 360.0, 'credito' => 180.0, 'total_neto' => 540.0, 'u_contado' => 20, 'u_credito' => 10, 'u_cambio' => 2, 'u_promo' => 4, 'u_total' => 36],
    ['producto' => 'ACE001 ACEITE VEGETAL 1L', 'contado' => 900.0, 'credito' => 450.0, 'total_neto' => 1350.0, 'u_contado' => 20, 'u_credito' => 10, 'u_cambio' => 0, 'u_promo' => 0, 'u_total' => 30],
    ['producto' => 'ARR001 ARROZ MORELOS 1KG', 'contado' => 440.0, 'credito' => 220.0, 'total_neto' => 660.0, 'u_contado' => 20, 'u_credito' => 10, 'u_cambio' => 0, 'u_promo' => 3, 'u_total' => 33],
];

// ── State ─────────────────────────────────────────────────────
state([
    'pedidos' => $mockPedidos,
    'pedidosFiltrados' => $mockPedidos,
    'productosFiltrados' => $mockProductos,

    // Filtros
    'filtro_zona' => 'todos',
    'filtro_ruta' => 'todos',
    'filtro_cliente' => 'todos', // clave del cliente (R10, C01…)
    'filtro_tipo_facturacion' => 'todos', // Todos / Facturan / No Facturan
    'tipo_comentario' => 'folio',
    'tipo_tabla' => 'Pedidos',
    'tab_activo' => 'Todos',
    'estado_registro' => 'Activos',
    'fecha_inicio' => '',
    'fecha_fin' => '',
    'search' => '',

    // Columnas visibles (tabla Productos)
    'col_producto' => true,
    'col_contado' => true,
    'col_credito' => true,
    'col_total_neto' => true,
    'col_u_contado' => true,
    'col_u_credito' => true,
    'col_u_cambio' => true,
    'col_u_promo' => true,
    'col_u_total' => true,
]);

// ── Filtrar ───────────────────────────────────────────────────
$aplicarFiltros = function () {
    $filtradas = collect($this->pedidos);

    if ($this->filtro_cliente !== 'todos') {
        $filtradas = $filtradas->where('clave_cliente', $this->filtro_cliente);
    }
    if ($this->tab_activo !== 'Todos') {
        $filtradas = $filtradas->where('movimiento', $this->tab_activo);
    }
    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtradas = $filtradas->filter(fn($item) => str_contains(strtolower($item['nombre_cliente']), $q) || str_contains(strtolower($item['vendedor']), $q) || str_contains(strtolower($item['folio']), $q));
    }
    $this->pedidosFiltrados = $filtradas->values()->toArray();
};

$setTab = function ($tab) {
    $this->tab_activo = $tab;
    $this->aplicarFiltros();
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};
$updatedFiltroZona = function () {
    $this->aplicarFiltros();
};
$updatedEstadoRegistro = function () {
    $this->aplicarFiltros();
};

$actualizar = function () {
    $this->aplicarFiltros();
};

$updatedFiltroCliente = function () {
    $this->aplicarFiltros();
};

$updatedFiltroTipoFacturacion = function () {
    $this->aplicarFiltros();
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
                <span class="text-[#003859] font-bold">Pedidos</span>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">
                <h2 class="text-base font-bold text-gray-800 mb-4">Pedidos</h2>

                {{-- ── Filtros Fila 1: Zona | Ruta | Cliente | Tipo Facturación ── --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-3 mb-3">

                    {{-- Zona --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Zona</label>
                        <div class="relative">
                            <select wire:model.live="filtro_zona"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="todos">Todas</option>
                                <option value="1Z">1Z - Zona 1</option>
                                <option value="2Z">2Z - Zona 2</option>
                                <option value="3Z">3Z - Zona 3</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Ruta --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Ruta</label>
                        <div class="relative">
                            <select wire:model.live="filtro_ruta"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="todos">Todas</option>
                                <option value="4686">4686 - RUTA06</option>
                                <option value="3201">3201 - RUTA01</option>
                                <option value="4102">4102 - RUTA02</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Cliente (clientes reales) --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Cliente</label>
                        <div class="relative">
                            <select wire:model.live="filtro_cliente"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="todos">Todos</option>
                                <option value="R10">R10 - CLIENTE RUTA 10 EVENTUAL</option>
                                <option value="C01">C01 - ABARROTES LA ESQUINA</option>
                                <option value="C02">C02 - MINISUPER CENTRAL</option>
                                <option value="C03">C03 - TIENDA DON PEPE</option>
                                <option value="C04">C04 - BODEGA NORTE</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Tipo Facturación (Todos / Facturan / No Facturan) --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">&nbsp;</label>
                        <div class="relative">
                            <select wire:model.live="filtro_tipo_facturacion"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="todos">Todos</option>
                                <option value="facturan">Clientes que Facturan</option>
                                <option value="no_facturan">Clientes que No Facturan</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Filtros Fila 2: Fechas + Search ── --}}
                <div class="grid grid-cols-3 gap-x-4 gap-y-3 mb-3">

                    {{-- Fecha inicial --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Fecha inicial</label>
                        <label class="flex items-center border-b border-gray-300 py-1.5 cursor-pointer"
                            @click.prevent="$el.querySelector('input').showPicker()">
                            <input type="date" wire:model.live="fecha_inicio"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-sm text-gray-800 font-medium [&::-webkit-calendar-picker-indicator]:hidden" />
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 ml-1" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </label>
                    </div>

                    {{-- Fecha final --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Fecha final</label>
                        <label class="flex items-center border-b border-gray-300 py-1.5 cursor-pointer"
                            @click.prevent="$el.querySelector('input').showPicker()">
                            <input type="date" wire:model.live="fecha_fin"
                                class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-sm text-gray-800 font-medium [&::-webkit-calendar-picker-indicator]:hidden" />
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 ml-1" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </label>
                    </div>

                    {{-- Search --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">&nbsp;</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" wire:model.live="search" placeholder="Buscar..."
                                class="w-full pl-5 border-b border-gray-300 py-1.5 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 placeholder-gray-400" />
                        </div>
                    </div>
                </div>

                {{-- ── Filtros Fila 2 ── --}}
                <div class="flex flex-wrap gap-4 items-end mb-5">

                    {{-- Tipo comentario --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Tipo comentario</label>
                        <div class="relative w-36">
                            <select wire:model.live="tipo_comentario"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="folio">folio</option>
                                <option value="comentario">comentario</option>
                                <option value="clave_producto">Clave producto</option>
                                <option value="producto">producto</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Tipo de tabla --}}
                    <div>
                        <label class="block text-[10px] text-gray-400 mb-1">Tipo de tabla</label>
                        <div class="relative w-32">
                            <select wire:model.live="tipo_tabla"
                                class="w-full appearance-none border-b border-gray-300 bg-transparent py-1.5 pr-6 text-sm text-gray-800 font-medium focus:outline-none focus:border-[#003859]">
                                <option value="Pedidos">Pedidos</option>
                                <option value="Productos">Productos</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Consultar --}}
                    <button wire:click="aplicarFiltros"
                        class="px-5 py-1.5 bg-[#003859] text-white text-sm font-semibold rounded hover:bg-[#004f7c] transition">
                        Consultar
                    </button>
                </div>

                {{-- ── Tabs ── --}}
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        @foreach (['Todos', 'Contado', 'Crédito', 'Cobranza', 'NV', 'Preventa', 'Entrega', 'Devolución'] as $tab)
                            <button wire:click="setTab('{{ $tab }}')"
                                class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                                    {{ $tab_activo === $tab
                                        ? 'border-[#003859] text-[#003859]'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ $tab }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- ── Sub-toolbar ── --}}
                <div class="flex items-center justify-between py-2.5 border-b border-gray-100"
                    x-data="{ showColumnas: false, showExportar: false }">

                    {{-- Activos / Borrados --}}
                    <div class="relative">
                        <select wire:model.live="estado_registro"
                            class="appearance-none border-b border-gray-400 bg-transparent py-1 pr-5 text-sm text-gray-800 font-semibold focus:outline-none focus:border-[#003859]">
                            <option value="Activos">Activos</option>
                            <option value="Borrados">Borrados</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none text-gray-500">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">

                        {{-- Ver Columnas (solo en Productos) --}}
                        <div class="relative">
                            <button @click="showColumnas = !showColumnas; showExportar = false" title="Ver Columnas"
                                class="p-1.5 rounded border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z"/>
                                </svg>
                            </button>
                            <div x-show="showColumnas" @click.outside="showColumnas = false"
                                class="absolute right-0 top-9 z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-64">
                                <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Add or remove columns
                                </p>
                                @foreach ([['col_producto', 'Producto'], ['col_contado', 'Contado'], ['col_credito', 'Crédito'], ['col_total_neto', 'Total neto'], ['col_u_contado', 'Unidades vendidas de Contado'], ['col_u_credito', 'Unidades vendidas de Crédito'], ['col_u_cambio', 'Unidades Cambio'], ['col_u_promo', 'Unidades Promoción'], ['col_u_total', 'Total de unidades vendidas']] as [$field, $label])
                                    <label
                                        class="flex items-center gap-2 py-1 px-1 rounded cursor-pointer hover:bg-gray-50 text-sm text-gray-700">
                                        <input type="checkbox" wire:model.live="{{ $field }}"
                                            class="w-4 h-4 text-pink-500 rounded border-gray-300 focus:ring-pink-400" />
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Exportar --}}
                        <div class="relative">
                            <button @click="showExportar = !showExportar; showColumnas = false" title="Exportar"
                                class="p-1.5 rounded border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </button>
                            <div x-show="showExportar" @click.outside="showExportar = false"
                                class="absolute right-0 top-9 z-50 bg-white border border-gray-200 rounded-lg shadow-lg py-1 w-56">
                                @foreach (['Exportar pedidos CSV', 'Exportar pedidos PDF', 'Exportar Productos CSV', 'Exportar Productos PDF', 'Exportar Detalle Cobranzas CSV', 'Exportar Detalle Cobranzas PDF', 'Exportar No Ventas CSV', 'Exportar No Ventas PDF', 'Exportar Pedidos cnt/cre'] as $op)
                                    <button
                                        class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">{{ $op }}</button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Actualizar --}}
                        <button wire:click="actualizar" title="Actualizar"
                            class="p-1.5 rounded border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- ── TABLA PEDIDOS ── --}}
                @if ($tipo_tabla === 'Pedidos')
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-left">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    <th class="px-3 py-3 whitespace-nowrap">Acciones Folio</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Movimiento</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Sincronizado</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Clave Sincronizado</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Fecha</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Hora</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Cancelación</th>
                                    <th class="px-3 py-3 whitespace-nowrap">¿Es Crédito?</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Comentario</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Vendedor</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Clave Cliente</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Nombre Cliente</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Cobrado</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Subtotal</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Total</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Causa</th>
                                    <th class="px-3 py-3 whitespace-nowrap">Peso (Kgs)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($pedidosFiltrados as $item)
                                    <tr class="hover:bg-gray-50/40 transition">
                                        <td class="px-3 py-3 font-medium text-[#003859]">
                                            {{ $item['folio'] }}
                                        </td>

                                        <td class="px-3 py-3">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                            {{ $item['movimiento'] === 'Venta'
                                                ? 'bg-green-100 text-green-700'
                                                : ($item['movimiento'] === 'Preventa'
                                                    ? 'bg-blue-100 text-blue-700'
                                                    : ($item['movimiento'] === 'Cobranza'
                                                        ? 'bg-purple-100 text-purple-700'
                                                        : ($item['movimiento'] === 'NV'
                                                            ? 'bg-yellow-100 text-yellow-700'
                                                            : ($item['movimiento'] === 'Devolución'
                                                                ? 'bg-red-100 text-red-700'
                                                                : 'bg-gray-100 text-gray-600')))) }}">
                                                {{ $item['movimiento'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            @if ($item['sincronizado'])
                                                <span class="text-green-600 font-semibold">✓ Sí</span>
                                            @else
                                                <span class="text-orange-500 font-semibold">✗ No</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-gray-500">{{ $item['clave_sincronizado'] ?: '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-gray-600">{{ $item['fecha'] }}</td>
                                        <td class="px-3 py-3 text-gray-600">{{ $item['hora'] }}</td>
                                        <td class="px-3 py-3 text-gray-400">{{ $item['cancelacion'] ?: '-' }}</td>
                                        <td class="px-3 py-3">
                                            @if ($item['es_credito'])
                                                <span class="text-[#003859] font-semibold text-[10px]">Crédito</span>
                                            @else
                                                <span class="text-gray-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-gray-500">{{ $item['comentario'] ?: '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap">{{ $item['vendedor'] }}</td>
                                        <td class="px-3 py-3 font-medium text-gray-800">{{ $item['clave_cliente'] }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap">{{ $item['nombre_cliente'] }}</td>
                                        <td class="px-3 py-3">${{ number_format($item['cobrado'], 2) }}</td>
                                        <td class="px-3 py-3">${{ number_format($item['subtotal'], 2) }}</td>
                                        <td class="px-3 py-3 font-semibold text-gray-900">
                                            ${{ number_format($item['total'], 2) }}</td>
                                        <td class="px-3 py-3 text-gray-400">{{ $item['causa'] ?: '-' }}</td>
                                        <td class="px-3 py-3">{{ $item['peso_kgs'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="18" class="px-6 py-10 text-center text-gray-400">No hay
                                            Registros para mostrar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- ── TABLA PRODUCTOS ── --}}
                @if ($tipo_tabla === 'Productos')
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs text-left">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                    @if ($col_producto)
                                        <th class="px-4 py-3 whitespace-nowrap">Producto ↑</th>
                                    @endif
                                    @if ($col_contado)
                                        <th class="px-4 py-3 whitespace-nowrap">Contado</th>
                                    @endif
                                    @if ($col_credito)
                                        <th class="px-4 py-3 whitespace-nowrap">Crédito</th>
                                    @endif
                                    @if ($col_total_neto)
                                        <th class="px-4 py-3 whitespace-nowrap">Total neto</th>
                                    @endif
                                    @if ($col_u_contado)
                                        <th class="px-4 py-3 whitespace-nowrap">Unidades vendidas de Contado</th>
                                    @endif
                                    @if ($col_u_credito)
                                        <th class="px-4 py-3 whitespace-nowrap">Unidades vendidas de Crédito</th>
                                    @endif
                                    @if ($col_u_cambio)
                                        <th class="px-4 py-3 whitespace-nowrap">Unidades Cambio</th>
                                    @endif
                                    @if ($col_u_promo)
                                        <th class="px-4 py-3 whitespace-nowrap">Unidades Promoción</th>
                                    @endif
                                    @if ($col_u_total)
                                        <th class="px-4 py-3 whitespace-nowrap">Total de unidades vendidas</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-700">
                                @forelse($productosFiltrados as $p)
                                    <tr class="hover:bg-gray-50/40 transition">
                                        @if ($col_producto)
                                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $p['producto'] }}</td>
                                        @endif
                                        @if ($col_contado)
                                            <td class="px-4 py-3">${{ number_format($p['contado'], 2) }}</td>
                                        @endif
                                        @if ($col_credito)
                                            <td class="px-4 py-3">${{ number_format($p['credito'], 2) }}</td>
                                        @endif
                                        @if ($col_total_neto)
                                            <td class="px-4 py-3 font-semibold">
                                                ${{ number_format($p['total_neto'], 2) }}</td>
                                        @endif
                                        @if ($col_u_contado)
                                            <td class="px-4 py-3 text-center">{{ $p['u_contado'] }}</td>
                                        @endif
                                        @if ($col_u_credito)
                                            <td class="px-4 py-3 text-center">{{ $p['u_credito'] }}</td>
                                        @endif
                                        @if ($col_u_cambio)
                                            <td class="px-4 py-3 text-center">{{ $p['u_cambio'] }}</td>
                                        @endif
                                        @if ($col_u_promo)
                                            <td class="px-4 py-3 text-center">{{ $p['u_promo'] }}</td>
                                        @endif
                                        @if ($col_u_total)
                                            <td class="px-4 py-3 text-center font-semibold">{{ $p['u_total'] }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-10 text-center text-gray-400">No hay
                                            Registros para mostrar</td>
                                    </tr>
                                @endforelse

                                {{-- Totales --}}
                                <tr class="bg-gray-50 font-bold text-gray-700 border-t-2 border-gray-200">
                                    @if ($col_producto)
                                        <td class="px-4 py-3">Total</td>
                                    @endif
                                    @if ($col_contado)
                                        <td class="px-4 py-3">
                                            ${{ number_format(collect($productosFiltrados)->sum('contado'), 2) }}</td>
                                    @endif
                                    @if ($col_credito)
                                        <td class="px-4 py-3">
                                            ${{ number_format(collect($productosFiltrados)->sum('credito'), 2) }}</td>
                                    @endif
                                    @if ($col_total_neto)
                                        <td class="px-4 py-3">
                                            ${{ number_format(collect($productosFiltrados)->sum('total_neto'), 2) }}
                                        </td>
                                    @endif
                                    @if ($col_u_contado)
                                        <td class="px-4 py-3 text-center">
                                            {{ collect($productosFiltrados)->sum('u_contado') }}</td>
                                    @endif
                                    @if ($col_u_credito)
                                        <td class="px-4 py-3 text-center">
                                            {{ collect($productosFiltrados)->sum('u_credito') }}</td>
                                    @endif
                                    @if ($col_u_cambio)
                                        <td class="px-4 py-3 text-center">
                                            {{ collect($productosFiltrados)->sum('u_cambio') }}</td>
                                    @endif
                                    @if ($col_u_promo)
                                        <td class="px-4 py-3 text-center">
                                            {{ collect($productosFiltrados)->sum('u_promo') }}</td>
                                    @endif
                                    @if ($col_u_total)
                                        <td class="px-4 py-3 text-center">
                                            {{ collect($productosFiltrados)->sum('u_total') }}</td>
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
