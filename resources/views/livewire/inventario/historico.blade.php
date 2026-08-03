<?php

use function Livewire\Volt\{state, layout, mount};
use App\Models\Zone;

layout('layouts.app');

// ═══════════════════════════════════════════════════════════════════════════
//  DATOS MOCK — reemplazar con query Firebird en producción
//  Estructura: folio, articulos, zona, ruta, tipo_movimiento,
//              tipo_inventario, fecha, usuario
state([
    'zonas'              => [],
    'rutas'              => [],
    'tipos_movimiento'   => [],
    'registros'          => [],
    'registrosFiltrados' => [],
    'filtro_zona'        => '',
    'filtro_ruta'        => '',
    'filtro_movimiento'  => 'Todas',
    'fecha_inicial'      => '',
    'fecha_final'        => '',
    'busqueda'           => '',
    'consultado'         => false,
]);

mount(function () {
    $hoy = date('Y-m-d');
    
    $this->zonas = Zone::all()->toArray();
    
    $this->rutas = [
        ['id'=>'R01','nombre'=>'4682 - RUTA02'],
        ['id'=>'R02','nombre'=>'4683 - RUTA03'],
        ['id'=>'R03','nombre'=>'4684 - RUTA04'],
    ];
    
    $this->tipos_movimiento = [
        'Todas','Venta','Entrada carga a ruta','Salida carga a ruta','Devolución','Merma',
    ];

    $this->registros = [
        ['folio'=>'PVB000041','articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Venta',               'tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 16:29:09",'usuario'=>'root (vemobile)'],
        ['folio'=>'PVB000040','articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Venta',               'tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 16:27:47",'usuario'=>'root (vemobile)'],
        ['folio'=>'5',        'articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Entrada carga a ruta','tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 16:24:34",'usuario'=>'Usuario Prueba (UPrueba)'],
        ['folio'=>'5',        'articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Salida carga a ruta', 'tipo_inventario'=>'General', 'fecha'=>"$hoy 16:24:34",'usuario'=>'Usuario Prueba (UPrueba)'],
        ['folio'=>'PVB000039','articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Venta',               'tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 18:14:07",'usuario'=>'root (vemobile)'],
        ['folio'=>'4',        'articulos'=>2,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Entrada carga a ruta','tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 18:12:19",'usuario'=>'Usuario Prueba (UPrueba)'],
        ['folio'=>'4',        'articulos'=>2,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Salida carga a ruta', 'tipo_inventario'=>'General', 'fecha'=>"$hoy 18:12:19",'usuario'=>'Usuario Prueba (UPrueba)'],
        ['folio'=>'PVB000038','articulos'=>2,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Venta',               'tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 10:38:21",'usuario'=>'root (vemobile)'],
        ['folio'=>'PVB000037','articulos'=>1,'zona'=>'1Z Zona 1','ruta'=>'4682 - RUTA02','tipo_movimiento'=>'Venta',               'tipo_inventario'=>'Móvil',   'fecha'=>"$hoy 10:04:03",'usuario'=>'root (vemobile)'],
    ];
    
    $this->fecha_inicial = $hoy;
    $this->fecha_final = $hoy;
});

// ═══════════════════════════════════════════════════════════════════════════
//  ACCIÓN: Consultar — aplica todos los filtros activos sobre $this->registros
// ═══════════════════════════════════════════════════════════════════════════
$consultar = function () {
    $data = collect($this->registros);
    \Log::info('INICIO CONSULTAR', [
        'count_original' => $data->count(),
        'filtro_zona' => $this->filtro_zona,
        'filtro_ruta' => $this->filtro_ruta,
        'filtro_movimiento' => $this->filtro_movimiento,
        'fecha_inicial' => $this->fecha_inicial,
        'fecha_final' => $this->fecha_final,
    ]);

    // 1. Filtro Zona
    if (!empty($this->filtro_zona)) {
        $data = $data->filter(fn($r) => str_contains(strtolower($r['zona']), strtolower($this->filtro_zona)));
    }

    // 2. Filtro Ruta
    if (!empty($this->filtro_ruta)) {
        $data = $data->filter(fn($r) => str_contains(strtolower($r['ruta']), strtolower($this->filtro_ruta)));
    }

    // 3. Filtro Tipo Movimiento
    if (!empty($this->filtro_movimiento) && $this->filtro_movimiento !== 'Todas') {
        $data = $data->filter(fn($r) => $r['tipo_movimiento'] === $this->filtro_movimiento);
    }

    // 4. Filtro Fecha Inicial
    if (!empty($this->fecha_inicial)) {
        $data = $data->filter(fn($r) => substr($r['fecha'], 0, 10) >= $this->fecha_inicial);
    }
    // 5. Filtro Fecha Final
    if (!empty($this->fecha_final)) {
        $data = $data->filter(fn($r) => substr($r['fecha'], 0, 10) <= $this->fecha_final);
    }

    // 6. Búsqueda libre (folio o usuario)
    if (!empty($this->busqueda)) {
        $q = strtolower(trim($this->busqueda));
        $data = $data->filter(fn($r) =>
            str_contains(strtolower($r['folio']), $q) ||
            str_contains(strtolower($r['usuario']), $q)
        );
    }

    $this->registrosFiltrados = $data->values()->toArray();
    $this->consultado = true;
    \Log::info('FIN CONSULTAR', ['count_filtrado' => count($this->registrosFiltrados)]);
};

?>

@once
<style>
    .screen-hidden { display: none !important; }
</style>
@endonce

{{-- ══ Componente raíz: Alpine page/perPage para paginación client-side ══ --}}
<div x-data="{ page: 1, perPage: 50 }" class="h-full bg-white dark:bg-gray-900 flex flex-col pt-4">
<div class="w-full px-6 flex flex-col flex-1">

    {{-- Breadcrumb --}}
    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-4 px-1">
        <span>Cpanel</span><span class="mx-2 text-gray-400">/</span>
        <span>Inventario</span><span class="mx-2 text-gray-400">/</span>
        <span class="text-[#003859] dark:text-blue-400 font-bold">Histórico</span>
    </div>

    {{-- Título --}}
    <h1 class="text-xl font-bold text-gray-800 dark:text-white mb-5">Histórico Movimientos</h1>

    {{-- ── Barra de filtros ─────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-end gap-5 mb-4">

        {{-- Zona --}}
        <div class="flex flex-col min-w-[140px]">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Zona</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <select wire:model="filtro_zona"
                    class="w-full appearance-none bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none cursor-pointer pr-5">
                    <option value="">--- Zonas ---</option>
                    @foreach ($zonas as $z)
                        <option value="{{ $z['name'] }}">{{ $z['name'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Ruta --}}
        <div class="flex flex-col min-w-[170px]">
            <label class="text-xs text-gray-500 dark:text-gray-400 font-medium mb-1">Ruta</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <select wire:model="filtro_ruta"
                    class="w-full appearance-none bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none cursor-pointer pr-5">
                    <option value="">--- Rutas ---</option>
                    @foreach ($rutas as $r)
                        <option value="{{ $r['nombre'] }}">{{ $r['nombre'] }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Tipo de Movimiento --}}
        <div class="flex flex-col" style="min-width:200px">
            <label class="text-xs text-[#003859] dark:text-blue-400 font-medium mb-1">Movimiento</label>
            <div class="relative border-b border-gray-300 dark:border-gray-600 pb-0.5 flex items-center gap-2">
                <select wire:model="filtro_movimiento"
                    class="appearance-none bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none cursor-pointer pr-4 flex-1 min-w-0">
                    @foreach ($tipos_movimiento as $tipo)
                        <option value="{{ $tipo }}">{{ $tipo }}</option>
                    @endforeach
                </select>
                {{-- Badge indicador del filtro seleccionado --}}
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-[10px] font-semibold text-gray-600 dark:text-gray-300 whitespace-nowrap shrink-0">
                    {{ $filtro_movimiento ?: 'Todas' }}
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
                <div class="pointer-events-none absolute inset-y-0 right-12 flex items-center text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Fecha Inicial --}}
        <div class="flex flex-col min-w-[130px]">
            <label class="text-xs text-[#003859] dark:text-blue-400 font-medium mb-1">Fecha inicial</label>
            <div class="border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <input type="date" wire:model="fecha_inicial"
                    class="w-full bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none border-none p-0 focus:ring-0 cursor-pointer">
            </div>
        </div>

        {{-- Fecha Final --}}
        <div class="flex flex-col min-w-[130px]">
            <label class="text-xs text-[#003859] dark:text-blue-400 font-medium mb-1">Fecha final</label>
            <div class="border-b border-gray-300 dark:border-gray-600 pb-0.5">
                <input type="date" wire:model="fecha_final"
                    class="w-full bg-transparent text-sm font-semibold text-gray-700 dark:text-gray-200 outline-none border-none p-0 focus:ring-0 cursor-pointer">
            </div>
        </div>

        {{-- Botón Consultar --}}
        <div class="flex flex-col">
            <label class="text-xs text-transparent mb-1">.</label>
            <button @click="$wire.consultar(); page = 1" id="btn-consultar-historico"
                class="px-6 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded-lg text-sm font-semibold transition duration-150 cursor-pointer shadow-sm">
                Consultar
            </button>
        </div>
    </div>

    {{-- ── Búsqueda libre + Kardex buttons ─────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-3">

        {{-- Buscar --}}
        <div class="flex items-center gap-2 border-b border-gray-300 dark:border-gray-600 pb-0.5 w-48">
            <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" wire:model="busqueda" placeholder="Buscar ..."
                class="w-full bg-transparent text-sm text-gray-700 dark:text-gray-200 outline-none"
                wire:keydown.enter="consultar">
        </div>

        {{-- Kardex buttons --}}
        <div class="flex items-center gap-2">
            @if ($consultado && count($registrosFiltrados) > 0)
                <button id="btn-kardex-ruta"
                    class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                    Kardex Ruta
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            @endif
            @if ($consultado)
                <button id="btn-kardex-zona"
                    class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer">
                    Kardex Zona
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- ── Action Icons ─────────────────────────────────────────────────────── --}}
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
                class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xl rounded-lg z-50 p-3">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">Mostrar / Ocultar columnas</p>
                @foreach([
                    'col-folio'     => 'Folio',
                    'col-arts'      => 'Artículos',
                    'col-zona'      => 'Zona',
                    'col-ruta'      => 'Ruta',
                    'col-tipomov'   => 'Tipo Movimiento',
                    'col-tipoinv'   => 'Tipo de Inventario',
                    'col-fecha'     => 'Fecha',
                    'col-usuario'   => 'Usuario',
                ] as $colId => $colLabel)
                    <label class="flex items-center gap-2 px-1 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer text-xs text-gray-700 dark:text-gray-300">
                        <input type="checkbox" checked
                            class="rounded border-gray-300 text-[#003859] focus:ring-[#003859]"
                            @change="
                                document.querySelectorAll('.{{ $colId }}').forEach(function(el){
                                    el.style.display = $event.target.checked ? '' : 'none';
                                });
                            ">
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

    {{-- ── Tabla de resultados ──────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-x-auto rounded-t-lg border border-gray-200/60 dark:border-gray-800">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-800 select-none">
                    <th class="px-3 py-3 whitespace-nowrap">Acciones</th>
                    <th class="px-3 py-3 whitespace-nowrap col-folio">Folio</th>
                    <th class="px-3 py-3 whitespace-nowrap col-arts">Artículos</th>
                    <th class="px-3 py-3 whitespace-nowrap col-zona">Zona</th>
                    <th class="px-3 py-3 whitespace-nowrap col-ruta">Ruta</th>
                    <th class="px-3 py-3 whitespace-nowrap col-tipomov">Tipo Movimiento</th>
                    <th class="px-3 py-3 whitespace-nowrap col-tipoinv">Tipo de Inventario</th>
                    <th class="px-3 py-3 whitespace-nowrap col-fecha">Fecha</th>
                    <th class="px-3 py-3 whitespace-nowrap col-usuario">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900 text-xs text-gray-700 dark:text-gray-300">
                @forelse ($registrosFiltrados as $row)
                    {{-- Paginación client-side --}}
                    <tr :class="{ 'screen-hidden': !({{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage) }"
                        class="hover:bg-blue-50/30 dark:hover:bg-gray-800/30 transition-colors odd:bg-white even:bg-gray-50/30 dark:odd:bg-gray-900 dark:even:bg-gray-800/20">

                        {{-- Acciones: ver detalle + descargar --}}
                        <td class="px-3 py-2.5">
                            <div class="flex items-center gap-1">
                                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-[#003859] dark:text-blue-400 transition cursor-pointer" title="Ver detalle">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                </button>
                                <button class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-[#003859] dark:text-blue-400 transition cursor-pointer" title="Descargar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </button>
                            </div>
                        </td>

                        <td class="px-3 py-2.5 col-folio">
                            <span class="text-[#003859] dark:text-blue-400 font-semibold hover:underline cursor-pointer">{{ $row['folio'] }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-center col-arts">{{ $row['articulos'] }}</td>
                        <td class="px-3 py-2.5 col-zona">{{ $row['zona'] }}</td>
                        <td class="px-3 py-2.5 col-ruta">{{ $row['ruta'] }}</td>
                        <td class="px-3 py-2.5 col-tipomov">
                            <span class="text-[#003859] dark:text-blue-400 font-medium">{{ $row['tipo_movimiento'] }}</span>
                        </td>
                        <td class="px-3 py-2.5 col-tipoinv">{{ $row['tipo_inventario'] }}</td>
                        <td class="px-3 py-2.5 text-gray-500 dark:text-gray-400 col-fecha">{{ $row['fecha'] }}</td>
                        <td class="px-3 py-2.5 text-gray-500 dark:text-gray-400 col-usuario">{{ $row['usuario'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                No
                                <span class="font-semibold text-[#003859] dark:text-blue-400">hay</span>
                                Registros para mostrar
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Paginación — patrón idéntico a existencias-globales ──────────────── --}}
    <div class="flex justify-end items-center px-4 py-3 border border-t-0 border-gray-200/60 dark:border-gray-800 rounded-b-lg bg-gray-50/30 dark:bg-gray-800/20 text-xs text-gray-500 gap-6">

        {{-- Rows per page --}}
        <div class="flex items-center gap-1">
            <select x-model.number="perPage" @change="page = 1"
                class="bg-transparent border-0 focus:ring-0 cursor-pointer text-gray-500 dark:text-gray-400 font-semibold py-0.5 pl-0 pr-4 text-xs">
                <option value="10">10 Filas por Página</option>
                <option value="25">25 Filas por Página</option>
                <option value="50" selected>50 Filas por Página</option>
                <option value="100">100 Filas por Página</option>
            </select>
        </div>

        {{-- Rango + flechas --}}
        <div class="flex items-center gap-2">
            {{-- Primera --}}
            <button @click="page = 1" :disabled="page === 1"
                    :class="page === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer'"
                    class="p-1 rounded text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
            {{-- Anterior --}}
            <button @click="if (page > 1) page--" :disabled="page === 1"
                    :class="page === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer'"
                    class="p-1 rounded text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <span class="font-medium"
                x-text="
                    {{ count($registrosFiltrados) }} === 0
                        ? '0.0 of 0'
                        : ((page - 1) * perPage + 1) + '-' + Math.min(page * perPage, {{ count($registrosFiltrados) }}) + ' of ' + {{ count($registrosFiltrados) }}
                ">
            </span>

            {{-- Siguiente --}}
            <button @click="if (page < Math.ceil({{ count($registrosFiltrados) }} / perPage)) page++"
                    :disabled="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) || {{ count($registrosFiltrados) }} === 0"
                    :class="(page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) || {{ count($registrosFiltrados) }} === 0) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer'"
                    class="p-1 rounded text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            {{-- Última --}}
            <button @click="page = Math.max(1, Math.ceil({{ count($registrosFiltrados) }} / perPage))"
                    :disabled="page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) || {{ count($registrosFiltrados) }} === 0"
                    :class="(page >= Math.ceil({{ count($registrosFiltrados) }} / perPage) || {{ count($registrosFiltrados) }} === 0) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer'"
                    class="p-1 rounded text-gray-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

</div>
</div>
