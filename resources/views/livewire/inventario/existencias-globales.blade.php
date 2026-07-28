<?php

use function Livewire\Volt\{state, layout};
use App\Models\Zone;

layout('layouts.app');

// ═══════════════════════════════════════════════════════════════════════════
//  DATOS MOCK — reemplazar con query Firebird en producción
//  Estructura: producto, descripcion, zona_id, almacen, linea,
//              estatus, precio_compra, existencia_preventa, existencia,
//              merma, en_movil, ultimo_movimiento, total_monetario
// ═══════════════════════════════════════════════════════════════════════════
$mockExistencias = [
    // ── Zona 1 ──────────────────────────────────────────────────────────────
    ['producto'=>'DON1K', 'descripcion'=>'DONA 1 KILO',      'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'DULCERIA',    'estatus'=>'Inactivo','precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>0,  'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>null,            'total_monetario'=>0.00],
    ['producto'=>'DON1',  'descripcion'=>'DONAS',             'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'DULCERIA',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>974,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'DUL1',  'descripcion'=>'DULCES',            'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'DULCERIA',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>964,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'CHE1',  'descripcion'=>'CHETOS',            'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'CHICHARRINES','estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>974,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'TF130', 'descripcion'=>'TOSTADA 130g',      'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'ALIMENTOS',   'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>939,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'TF1000','descripcion'=>'TOSTADAS 1000g',    'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'ALIMENTOS',   'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>954,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'TF250', 'descripcion'=>'TOSTADAS 250g',     'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'ALIMENTOS',   'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>959,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'HOJ1',  'descripcion'=>'HOJUELAS',          'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'ALIMENTOS',   'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>969,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'CHC20', 'descripcion'=>'CUADRO 20 PZS',     'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'CHICHARRINES','estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>974,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    ['producto'=>'PAL1',  'descripcion'=>'PALOMITAS SAL',     'zona_id'=>'Z1','almacen'=>'Zona 1','linea'=>'ALIMENTOS',   'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>880,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
    // ── Zona 2 ──────────────────────────────────────────────────────────────
    ['producto'=>'LIM1',  'descripcion'=>'LIMON FRESH 500ml', 'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'LIMPIEZA',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>5,'existencia'=>312,'merma'=>2,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'JAB1',  'descripcion'=>'JABON BARRA 100g',  'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'HIG PERSONAL','estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>540,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'SHA1',  'descripcion'=>'SHAMPOO 400ml',     'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'HIG PERSONAL','estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>228,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'DES1',  'descripcion'=>'DESINFECTANTE 1L',  'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'LIMPIEZA',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>3,'existencia'=>180,'merma'=>0,'en_movil'=>2,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'FAR1',  'descripcion'=>'PARACETAMOL 500mg', 'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'FARMACIA',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>95, 'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-07-30 14:00','total_monetario'=>0.00],
    ['producto'=>'BEB1',  'descripcion'=>'AGUA 500ml',        'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'BEBIDAS',     'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>700,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'BEB2',  'descripcion'=>'REFRESCO COLA 600ml','zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'BEBIDAS',    'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>450,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 10:15','total_monetario'=>0.00],
    ['producto'=>'NUT1',  'descripcion'=>'NUEZ MIXTA 100G',   'zona_id'=>'Z2','almacen'=>'Zona 2','linea'=>'VARIOS',      'estatus'=>'Activo',  'precio_compra'=>0,'existencia_preventa'=>0,'existencia'=>412,'merma'=>0,'en_movil'=>0,'ultimo_movimiento'=>'2025-08-01 09:31','total_monetario'=>0.00],
];

state([
    // ── Catálogos ────────────────────────────────────────────────────────────
    'zonas'  => fn() => Zone::all()->toArray(),
    'lineas' => [
        'ALIMENTOS','DULCERIA','FARMACIA','HIG Y DESECHABLE',
        'HIG PERSONAL','LIMPIEZA','VARIOS','MASCOTAS','BEBIDAS','CHICHARRINES',
    ],
    'ordenes' => [
        'linea_desc'      => 'Orden de Línea descendente',
        'linea_asc'       => 'Orden de Línea ascendente',
        'existencia_desc' => 'Existencia descendente',
        'existencia_asc'  => 'Existencia ascendente',
        'producto_asc'    => 'Producto A-Z',
        'producto_desc'   => 'Producto Z-A',
    ],

    // ── Dataset base (mock) ──────────────────────────────────────────────────
    // NOTA: se asigna con fn() para lazy-load correcto en Volt
    'registros'          => fn() => $mockExistencias,
    'registrosFiltrados' => [],

    // ── Filtros ──────────────────────────────────────────────────────────────
    'filtro_zona'  => '',
    'filtro_linea' => '',
    'filtro_orden' => 'linea_desc',
    'busqueda'     => '',

    // ── Columnas visibles ────────────────────────────────────────────────────
    'visibleColumns' => [
        'almacen'             => true,
        'estatus'             => true,
        'precio_compra'       => true,
        'existencia_preventa' => true,
        'existencia'          => true,
        'merma'               => true,
        'en_movil'            => true,
        'ultimo_movimiento'   => true,
        'total_monetario'     => true,
    ],
]);

// ═══════════════════════════════════════════════════════════════════════════
//  ACCIÓN: Consultar — aplica todos los filtros activos sobre $this->registros
// ═══════════════════════════════════════════════════════════════════════════
$consultar = function () {
    $data = collect($this->registros);

    // 1. Filtro de Zona (por zona_id del registro mock / campo BD)
    if (!empty($this->filtro_zona)) {
        $data = $data->filter(fn($r) => ($r['zona_id'] ?? '') === $this->filtro_zona);
    }

    // 2. Filtro de Línea
    if (!empty($this->filtro_linea)) {
        $data = $data->filter(fn($r) => ($r['linea'] ?? '') === $this->filtro_linea);
    }

    // 3. Búsqueda libre (producto o descripción)
    if (!empty($this->busqueda)) {
        $q = strtolower(trim($this->busqueda));
        $data = $data->filter(fn($r) =>
            str_contains(strtolower($r['producto']), $q) ||
            str_contains(strtolower($r['descripcion']), $q)
        );
    }

    // 4. Ordenamiento
    $data = match ($this->filtro_orden) {
        'linea_desc'      => $data->sortByDesc('linea'),
        'linea_asc'       => $data->sortBy('linea'),
        'existencia_desc' => $data->sortByDesc('existencia'),
        'existencia_asc'  => $data->sortBy('existencia'),
        'producto_asc'    => $data->sortBy('producto'),
        'producto_desc'   => $data->sortByDesc('producto'),
        default           => $data,
    };

    $this->registrosFiltrados = $data->values()->toArray();
};

?>

@once
<style>
    .screen-hidden { display: none !important; }
</style>
@endonce

{{-- ══ Componente raíz: Alpine page/perPage para paginación client-side ══ --}}
<div x-data="{ page: 1, perPage: 10 }" class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4">
<div class="w-full px-6 flex flex-col flex-1">

    {{-- Breadcrumb --}}
    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1">
        <span>Cpanel</span><span class="mx-2 text-gray-400">/</span>
        <span>Inventario</span><span class="mx-2 text-gray-400">/</span>
        <span class="text-[#003859] dark:text-blue-400 font-bold">Existencias</span>
    </div>

    {{-- Título --}}
    <h1 class="text-xl font-bold text-gray-800 dark:text-white mb-5">Inventario</h1>

    {{-- ── Barra de filtros ──────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-5 mb-4">

        {{-- Zona --}}
        <div class="flex flex-col min-w-[150px]">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Zona</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <select wire:model="filtro_zona"
                    class="w-full appearance-none bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none cursor-pointer pr-5">
                    <option value="">--- Zonas ---</option>
                    @foreach ($zonas as $z)
                        <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Línea (custom dropdown Alpine para emular el diseño de la captura) --}}
        <div class="flex flex-col min-w-[160px]" x-data="{ open: false, selected: '--- Líneas ---' }">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Línea</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <button type="button" @click="open = !open" @click.away="open = false"
                    class="w-full flex items-center justify-between bg-transparent text-sm font-semibold text-[#003859] dark:text-blue-400 outline-none cursor-pointer pr-1 pb-0.5">
                    <span x-text="selected"></span>
                    <svg class="h-3.5 w-3.5 text-gray-400 shrink-0 transition-transform duration-150"
                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-transition style="display:none;"
                    class="absolute left-0 top-full mt-1 z-50 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-lg w-52 max-h-60 overflow-y-auto">
                    <div class="py-1">
                        <button type="button"
                            @click="selected = '--- Líneas ---'; $wire.set('filtro_linea', ''); open = false"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            --- Líneas ---
                        </button>
                        @foreach ($lineas as $linea)
                            <button type="button"
                                @click="selected = '{{ $linea }}'; $wire.set('filtro_linea', '{{ $linea }}'); open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700
                                    {{ $filtro_linea === $linea ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '' }}">
                                {{ $linea }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Ordenar por --}}
        <div class="flex flex-col min-w-[220px]">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ordenar por</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <select wire:model="filtro_orden"
                    class="w-full appearance-none bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none cursor-pointer pr-5">
                    @foreach ($ordenes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Búsqueda --}}
        <div class="flex flex-col flex-1 min-w-[160px]">
            <label class="text-xs text-transparent mb-1">.</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" wire:model="busqueda" placeholder="Buscar ..."
                    class="w-full bg-transparent text-sm text-gray-700 dark:text-gray-200 outline-none pl-6"
                    wire:keydown.enter="consultar">
            </div>
        </div>

        {{-- Botón Consultar --}}
        <div class="flex flex-col">
            <label class="text-xs text-transparent mb-1">.</label>
            <button wire:click="consultar" @click="page = 1" id="btn-consultar-existencias"
                class="px-6 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded-lg text-sm font-semibold transition duration-150 cursor-pointer shadow-sm">
                Consultar
            </button>
        </div>
    </div>

    {{-- ── Action Icons ───────────────────────────────────────────────────── --}}
    <div class="flex justify-end items-center gap-1 mb-2 text-gray-400">

        {{-- Toggle columnas --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false"
                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition" title="Columnas">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 5h18v2H3V5zm0 6h18v2H3v-2zm0 6h18v2H3v-2z"/>
                </svg>
            </button>
            <div x-show="open" style="display:none;"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-lg z-50 p-3 max-h-96 overflow-y-auto">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Mostrar / Ocultar columnas</p>
                @foreach([
                    'almacen'             => 'Almacén',
                    'estatus'             => 'Estatus',
                    'precio_compra'       => 'Precio de compra',
                    'existencia_preventa' => 'Existencia Preventa',
                    'existencia'          => 'Existencia',
                    'merma'               => 'Merma',
                    'en_movil'            => 'En Móvil',
                    'ultimo_movimiento'   => 'Último Movimiento',
                    'total_monetario'     => 'Total Monetario',
                ] as $col => $colLabel)
                    <label class="flex items-center gap-2 px-1 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer text-xs text-gray-700 dark:text-gray-300">
                        <input type="checkbox" wire:model.live="visibleColumns.{{ $col }}"
                            class="rounded border-gray-300 text-[#003859] focus:ring-[#003859]">
                        {{ $colLabel }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Exportar --}}
        <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition" title="Exportar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
        </button>

        {{-- Refresh --}}
        <button wire:click="consultar" @click="page = 1"
            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition" title="Actualizar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>

    {{-- ── Tabla de resultados ─────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-x-auto rounded-t-lg border border-gray-200/60 dark:border-gray-800">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-800 select-none">
                    <th class="px-3 py-3 whitespace-nowrap">Producto</th>
                    <th class="px-3 py-3 whitespace-nowrap">Descripción</th>
                    @if ($visibleColumns['almacen'])
                        <th class="px-3 py-3 whitespace-nowrap">Almacén</th>
                    @endif
                    @if ($visibleColumns['estatus'])
                        <th class="px-3 py-3 whitespace-nowrap">Estatus</th>
                    @endif
                    @if ($visibleColumns['precio_compra'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">Precio de compra</th>
                    @endif
                    @if ($visibleColumns['existencia_preventa'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">Existencia Preventa</th>
                    @endif
                    @if ($visibleColumns['existencia'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">Existencia</th>
                    @endif
                    @if ($visibleColumns['merma'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">Merma</th>
                    @endif
                    @if ($visibleColumns['en_movil'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">En Móvil</th>
                    @endif
                    @if ($visibleColumns['ultimo_movimiento'])
                        <th class="px-3 py-3 whitespace-nowrap">Ultimo Movimiento</th>
                    @endif
                    @if ($visibleColumns['total_monetario'])
                        <th class="px-3 py-3 text-right whitespace-nowrap">Total Monetario</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900 text-xs text-gray-700 dark:text-gray-300">
                @forelse ($registrosFiltrados as $row)
                    {{-- Paginación client-side: ocultar filas fuera del rango activo --}}
                    <tr :class="{ 'screen-hidden': !({{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage) }"
                        class="hover:bg-blue-50/30 dark:hover:bg-gray-800/30 transition-colors odd:bg-white even:bg-gray-50/30 dark:odd:bg-gray-900 dark:even:bg-gray-800/20">

                        <td class="px-3 py-2.5">
                            <span class="text-[#003859] dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ $row['producto'] }}</span>
                        </td>
                        <td class="px-3 py-2.5">
                            <span class="text-[#003859] dark:text-blue-400 font-medium hover:underline cursor-pointer">{{ $row['descripcion'] }}</span>
                        </td>
                        @if ($visibleColumns['almacen'])
                            <td class="px-3 py-2.5">{{ $row['almacen'] }}</td>
                        @endif
                        @if ($visibleColumns['estatus'])
                            <td class="px-3 py-2.5">
                                <span class="{{ $row['estatus'] === 'Activo' ? '' : 'text-gray-400 italic' }}">
                                    {{ $row['estatus'] }}
                                </span>
                            </td>
                        @endif
                        @if ($visibleColumns['precio_compra'])
                            <td class="px-3 py-2.5 text-right">{{ $row['precio_compra'] }}</td>
                        @endif
                        @if ($visibleColumns['existencia_preventa'])
                            <td class="px-3 py-2.5 text-right">
                                <span class="{{ $row['existencia_preventa'] > 0 ? 'text-[#003859] dark:text-blue-400 font-semibold' : 'text-gray-500' }}">
                                    {{ $row['existencia_preventa'] }}
                                </span>
                            </td>
                        @endif
                        @if ($visibleColumns['existencia'])
                            <td class="px-3 py-2.5 text-right">
                                <span class="{{ $row['existencia'] > 0 ? 'text-orange-500 dark:text-orange-400 font-semibold' : 'text-gray-400' }}">
                                    {{ $row['existencia'] }}
                                </span>
                            </td>
                        @endif
                        @if ($visibleColumns['merma'])
                            <td class="px-3 py-2.5 text-right">{{ $row['merma'] }}</td>
                        @endif
                        @if ($visibleColumns['en_movil'])
                            <td class="px-3 py-2.5 text-right">{{ $row['en_movil'] }}</td>
                        @endif
                        @if ($visibleColumns['ultimo_movimiento'])
                            <td class="px-3 py-2.5 text-gray-500 dark:text-gray-400">{{ $row['ultimo_movimiento'] ?? '' }}</td>
                        @endif
                        @if ($visibleColumns['total_monetario'])
                            <td class="px-3 py-2.5 text-right font-medium">${{ number_format($row['total_monetario'], 2) }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="font-semibold text-gray-500 dark:text-gray-400">Sin resultados</p>
                                <p class="text-xs text-gray-400">Aplica los filtros y presiona
                                    <span class="font-semibold text-[#003859] dark:text-blue-400">Consultar</span>
                                    para ver las existencias.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación — patrón idéntico a rentabilidad-ruta ───────────────── --}}
    @if (count($registrosFiltrados) > 0)
        <div class="flex justify-end items-center px-4 py-3 border border-t-0 border-gray-200/60 dark:border-gray-800 rounded-b-lg bg-gray-50/30 dark:bg-gray-800/20 text-xs text-gray-500 gap-6">

            {{-- Rows per page --}}
            <div class="flex items-center gap-1">
                <select x-model.number="perPage" @change="page = 1"
                    class="bg-transparent border-0 focus:ring-0 cursor-pointer text-gray-500 dark:text-gray-400 font-semibold py-0.5 pl-0 pr-6 text-xs w-20">
                    <option value="5">5 rows</option>
                    <option value="10">10 rows</option>
                    <option value="20">20 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                </select>
            </div>

            {{-- Rango + flechas --}}
            <div class="flex items-center gap-4">
                <span class="font-medium"
                    x-text="((page - 1) * perPage + 1) + '-' + Math.min(page * perPage, {{ count($registrosFiltrados) }}) + ' of ' + {{ count($registrosFiltrados) }}">
                </span>
                <div class="flex items-center gap-1.5">
                    <button @click="if (page > 1) page--"
                            :disabled="page === 1"
                            :class="page === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 cursor-pointer'"
                            class="p-1 rounded text-gray-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="if (page < Math.ceil({{ count($registrosFiltrados) }} / perPage)) page++"
                            :disabled="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage)"
                            :class="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 cursor-pointer'"
                            class="p-1 rounded text-gray-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
</div>
