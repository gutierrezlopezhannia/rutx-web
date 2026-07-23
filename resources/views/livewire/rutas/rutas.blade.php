<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockRutas = [
    ['clave' => '3983', 'nombre' => 'RUTA01', 'contrasena' => '3983', 'sincronizado' => true, 'tipo_ruta' => 'Ruta de Venta | Preventa', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '1Z - Zona 1', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => true, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Semanal', 'tipo_inventario' => 'Inv. Móvil'],
    ['clave' => '4682', 'nombre' => 'RUTA02', 'contrasena' => '4682', 'sincronizado' => true, 'tipo_ruta' => 'Ruta de Venta | Preventa', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '1Z - Zona 1', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => true, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Semanal', 'tipo_inventario' => 'Inv. Móvil'],
    ['clave' => '4683', 'nombre' => 'RUTA03', 'contrasena' => '4683', 'sincronizado' => false, 'tipo_ruta' => 'Ruta de Venta | Preventa', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '1Z - Zona 1', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => false, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Quincenal', 'tipo_inventario' => 'Inv. Móvil'],
    ['clave' => '4684', 'nombre' => 'RUTA04', 'contrasena' => '4684', 'sincronizado' => false, 'tipo_ruta' => 'Ruta de Venta | Preventa', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '2Z - Zona 2', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => false, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Semanal', 'tipo_inventario' => 'Inv. Móvil'],
    ['clave' => '4685', 'nombre' => 'RUTA05', 'contrasena' => '4685', 'sincronizado' => false, 'tipo_ruta' => 'Ruta de Venta | Preventa', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '2Z - Zona 2', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => false, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Semanal', 'tipo_inventario' => 'Inv. Móvil'],
    ['clave' => '4686', 'nombre' => 'RUTA06', 'contrasena' => '4686', 'sincronizado' => false, 'tipo_ruta' => 'Ruta de mostrador', 'estatus' => 'Activo', 'telefono' => '', 'zona' => '3Z - Zona 3', 'rutas_extras' => '', 'movimientos' => 'Venta', 'unidad_reparto' => '', 'lista_precio' => '', 'permitir_cobranza' => false, 'permitir_sinc_venta' => false, 'validar_km' => false, 'valida_km_geocerca' => false, 'alta_cliente_nuevo' => false, 'validar_inv_gral_preventa' => false, 'sincronizado_check' => false, 'actualizar_coord_cliente' => false, 'descargar_solo_agendados' => false, 'valida_geocerca' => false, 'tipo_agenda' => 'Trisemanal', 'tipo_inventario' => 'Inv. Móvil'],
];

$mockSeries = [['tipo' => 'Venta', 'serie' => 'VA', 'folio' => '1', 'rellenar' => true, 'longitud' => '9', 'previsualizacion' => 'VA0000001'], ['tipo' => 'Cobranza', 'serie' => 'CA', 'folio' => '0', 'rellenar' => true, 'longitud' => '9', 'previsualizacion' => 'CA0000000'], ['tipo' => 'No-Venta', 'serie' => 'NA', 'folio' => '0', 'rellenar' => true, 'longitud' => '9', 'previsualizacion' => 'NA0000000'], ['tipo' => 'Preventa', 'serie' => 'PA', 'folio' => '0', 'rellenar' => true, 'longitud' => '9', 'previsualizacion' => 'PA0000000'], ['tipo' => 'Entrega', 'serie' => 'EA', 'folio' => '0', 'rellenar' => true, 'longitud' => '9', 'previsualizacion' => 'EA0000000']];

state([
    'rutas' => $mockRutas,
    'rutasFiltradas' => $mockRutas,
    'series' => $mockSeries,
    'search' => '',
    'filtro_estatus' => 'todos',
    'filtro_zona' => 'todos',

    // Columnas visibles
    'col_acciones' => true,
    'col_clave' => true,
    'col_nombre' => true,
    'col_contrasena' => true,
    'col_sincronizado' => true,
    'col_tipo_ruta' => true,
    'col_estatus' => true,

    // Modal editar
    'showEditModal' => false,
    'editIndex' => null,
    'editNombre' => '',
    'editClave' => '',
    'editEstatus' => 'Activo',
    'editTipoRuta' => '',
    'editTelefono' => '',
    'editZona' => '1Z - Zona 1',
    'editContrasena' => '',
    'editRutasExtras' => '',
    'editMovimientos' => 'Venta',
    'editUnidadReparto' => '',
    'editListaPrecio' => '',
    'editPermitirCobranza' => false,
    'editPermitirSincVenta' => false,
    'editValidarKm' => false,
    'editValidaKmGeocerca' => false,
    'editAltaClienteNuevo' => false,
    'editValidarInvGralPreventa' => false,
    'editSincronizadoCheck' => false,
    'editActualizarCoordCliente' => false,
    'editDescargarSoloAgendados' => false,
    'editValidaGeocerca' => false,
    'editDistanciaGeocerca' => 0,
    'editTipoAgenda' => 'Semanal',
    'editTipoInventario' => 'Inv. Móvil',

    // Modal serie
    'showSerieModal' => false,
    'serieRutaNombre' => '',

    // Modal kilometraje
    'showKmModal' => false,
    'kmRutaNombre' => '',
]);

$aplicarFiltros = function () {
    $filtradas = collect($this->rutas);

    if ($this->filtro_estatus !== 'todos') {
        $filtradas = $filtradas->where('estatus', $this->filtro_estatus);
    }

    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtradas = $filtradas->filter(fn($r) => str_contains(strtolower($r['clave']), $q) || str_contains(strtolower($r['nombre']), $q) || str_contains(strtolower($r['tipo_ruta']), $q));
    }

    $this->rutasFiltradas = $filtradas->values()->toArray();
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};
$updatedFiltroEstatus = function () {
    $this->aplicarFiltros();
};
$updatedFiltroZona = function () {
    $this->aplicarFiltros();
};
$actualizar = function () {
    $this->aplicarFiltros();
};

$clearSearch = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

$abrirEditar = function ($index) {
    $ruta = $this->rutasFiltradas[$index];
    $this->editIndex = $index;
    $this->editNombre = $ruta['nombre'];
    $this->editClave = $ruta['clave'];
    $this->editEstatus = $ruta['estatus'];
    $this->editTipoRuta = $ruta['tipo_ruta'];
    $this->editTelefono = $ruta['telefono'] ?? '';
    $this->editZona = $ruta['zona'] ?? '1Z - Zona 1';
    $this->editContrasena = $ruta['contrasena'];
    $this->editRutasExtras = $ruta['rutas_extras'] ?? '';
    $this->editMovimientos = $ruta['movimientos'] ?? 'Venta';
    $this->editUnidadReparto = $ruta['unidad_reparto'] ?? '';
    $this->editListaPrecio = $ruta['lista_precio'] ?? '';
    $this->editPermitirCobranza = $ruta['permitir_cobranza'] ?? false;
    $this->editPermitirSincVenta = $ruta['permitir_sinc_venta'] ?? false;
    $this->editValidarKm = $ruta['validar_km'] ?? false;
    $this->editValidaKmGeocerca = $ruta['valida_km_geocerca'] ?? false;
    $this->editAltaClienteNuevo = $ruta['alta_cliente_nuevo'] ?? false;
    $this->editValidarInvGralPreventa = $ruta['validar_inv_gral_preventa'] ?? false;
    $this->editSincronizadoCheck = $ruta['sincronizado_check'] ?? false;
    $this->editActualizarCoordCliente = $ruta['actualizar_coord_cliente'] ?? false;
    $this->editDescargarSoloAgendados = $ruta['descargar_solo_agendados'] ?? false;
    $this->editValidaGeocerca = $ruta['valida_geocerca'] ?? false;
    $this->editDistanciaGeocerca = $ruta['distancia_geocerca'] ?? 0;
    $this->editTipoAgenda = $ruta['tipo_agenda'] ?? 'Semanal';
    $this->editTipoInventario = $ruta['tipo_inventario'] ?? 'Inv. Móvil';
    $this->showEditModal = true;
};

$cerrarEditar = function () {
    $this->showEditModal = false;
};

$abrirSerie = function ($index) {
    $ruta = $this->rutasFiltradas[$index];
    $this->serieRutaNombre = $ruta['nombre'];
    $this->showSerieModal = true;
};

$cerrarSerie = function () {
    $this->showSerieModal = false;
};

$abrirKm = function ($index) {
    $ruta = $this->rutasFiltradas[$index];
    $this->kmRutaNombre = $ruta['clave'] . ' ' . $ruta['nombre'];
    $this->showKmModal = true;
};

$cerrarKm = function () {
    $this->showKmModal = false;
};
?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Ruta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Rutas</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">

                {{-- Title --}}
                <div class="px-5 pt-5 pb-3">
                    <h1 class="text-xl font-bold text-[#003859]">Rutas</h1>
                </div>

                {{-- Toolbar: Filtros + Buscar + Iconos --}}
                <div class="px-5 pb-4 flex flex-col lg:flex-row lg:items-center gap-3" x-data="{ showColumnas: false, showExportar: false }">
                    {{-- Filtro Estatus --}}
                    <div class="relative w-full lg:w-40">
                        <select wire:model.live="filtro_estatus"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Activos</option>
                            <option value="Borrados">Borrados</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtro Zona --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="filtro_zona"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Spacer --}}
                    <div class="flex-1"></div>

                    {{-- Buscar --}}
                    <div class="relative w-full lg:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" wire:model.live="search" placeholder="Buscar ..."
                            class="w-full pl-9 pr-9 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400" />
                        @if (!empty($search))
                            <button wire:click="clearSearch"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Ver Columnas --}}
                    <div class="relative">
                        <button @click="showColumnas = !showColumnas; showExportar = false" title="Ver Columnas"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 4h4v16H4V4zm6 0h4v16h-4V4zm6 0h4v16h-4V4z" />
                            </svg>
                        </button>
                        <div x-show="showColumnas" @click.outside="showColumnas = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-48">
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-2">Columnas</p>
                            @foreach ([['col_acciones', 'Acciones'], ['col_clave', 'Clave'], ['col_nombre', 'Nombre'], ['col_contrasena', 'Contraseña'], ['col_sincronizado', 'Sincronizado'], ['col_tipo_ruta', 'Tipo Ruta'], ['col_estatus', 'Estatus']] as [$field, $label])
                                <label
                                    class="flex items-center gap-2 py-1 px-1 rounded cursor-pointer hover:bg-gray-50 text-sm text-gray-700">
                                    <input type="checkbox" wire:model.live="{{ $field }}"
                                        class="w-4 h-4 text-[#003859] rounded border-gray-300 focus:ring-[#003859]" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Exportar --}}
                    <div class="relative">
                        <button @click="showExportar = !showExportar; showColumnas = false" title="Exportar"
                            class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                        <div x-show="showExportar" @click.outside="showExportar = false" x-cloak
                            class="absolute right-0 top-10 z-50 bg-white border border-gray-200 rounded-lg shadow-lg py-1 w-40">
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a
                                CSV</button>
                            <button class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Exportar a
                                PDF</button>
                        </div>
                    </div>

                    {{-- Actualizar --}}
                    <button wire:click="actualizar" title="Actualizar"
                        class="p-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-[#003859] transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead
                            class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                            <tr>
                                @if ($col_acciones)
                                    <th scope="col" class="px-6 py-4">Acciones</th>
                                @endif
                                @if ($col_clave)
                                    <th scope="col" class="px-6 py-4">Clave</th>
                                @endif
                                @if ($col_nombre)
                                    <th scope="col" class="px-6 py-4">Nombre</th>
                                @endif
                                @if ($col_contrasena)
                                    <th scope="col" class="px-6 py-4">Contraseña</th>
                                @endif
                                @if ($col_sincronizado)
                                    <th scope="col" class="px-6 py-4 text-center">Sincronizado</th>
                                @endif
                                @if ($col_tipo_ruta)
                                    <th scope="col" class="px-6 py-4">Tipo Ruta</th>
                                @endif
                                @if ($col_estatus)
                                    <th scope="col" class="px-6 py-4">Estatus</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($rutasFiltradas as $idx => $ruta)
                                <tr class="hover:bg-gray-50/70 transition-colors duration-100">
                                    @if ($col_acciones)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <button wire:click="abrirEditar({{ $idx }})"
                                                    class="p-1.5 text-gray-400 hover:text-[#003859] hover:bg-gray-100 rounded transition"
                                                    title="Editar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <a href="{{ route('ruta.mapa-clientes') }}" wire:navigate
                                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded transition"
                                                    title="Mapa de Clientes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                </a>
                                                <button wire:click="abrirSerie({{ $idx }})"
                                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition"
                                                    title="Serie de Filtros">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="abrirKm({{ $idx }})"
                                                    class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded transition"
                                                    title="Kilometraje">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                    @if ($col_clave)
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-[#004066]">
                                            {{ $ruta['clave'] }}</td>
                                    @endif
                                    @if ($col_nombre)
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $ruta['nombre'] }}</td>
                                    @endif
                                    @if ($col_contrasena)
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-500">
                                            {{ $ruta['contrasena'] }}</td>
                                    @endif
                                    @if ($col_sincronizado)
                                        <td class="px-6 py-4 text-center">
                                            @if ($ruta['sincronizado'])
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </span>
                                            @endif
                                        </td>
                                    @endif
                                    @if ($col_tipo_ruta)
                                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $ruta['tipo_ruta'] }}</td>
                                    @endif
                                    @if ($col_estatus)
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($ruta['estatus'] === 'Activo')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#e6f2ff] text-[#0066cc] border border-[#cce3ff]">Activo</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#f0f4f8] text-[#102a43] border border-[#d9e2ec]">Inactivo</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="text-sm">No se encontraron rutas.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 bg-[#fafbfc] flex items-center justify-between text-xs text-gray-500 select-none">
                    <div>Mostrando <span class="font-semibold text-gray-700">{{ count($rutasFiltradas) }}</span> de
                        <span class="font-semibold text-gray-700">{{ count($rutas) }}</span> registros</div>
                    <div class="flex gap-1">
                        <button disabled
                            class="px-3 py-1.5 border border-gray-200 rounded bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed">Anterior</button>
                        <button disabled
                            class="px-3 py-1.5 border border-gray-200 rounded bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">Siguiente</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ MODAL: Editar Ruta ═══════════════ --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarEditar">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 mx-4">
                <h2 class="text-lg font-bold text-[#003859] mb-5">Editar Ruta</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Nombre --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Nombre</label>
                        <input type="text" wire:model="editNombre"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]" />
                    </div>
                    {{-- Clave --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Clave</label>
                        <input type="text" wire:model="editClave"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500"
                            readonly />
                    </div>
                    {{-- Estatus --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Estatus</label>
                        <input type="text" wire:model="editEstatus"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500"
                            readonly />
                    </div>
                    {{-- Tipo de Ruta --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Tipo de Ruta</label>
                        <input type="text" wire:model="editTipoRuta"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500"
                            readonly />
                    </div>
                    {{-- Teléfono --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Teléfono</label>
                        <input type="text" wire:model="editTelefono"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]" />
                    </div>
                    {{-- Zona --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Zona</label>
                        <select wire:model="editZona"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                            <option value="3Z - Zona 3">3Z - Zona 3</option>
                        </select>
                    </div>
                    {{-- Contraseña --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Contraseña</label>
                        <input type="text" wire:model="editContrasena"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]" />
                    </div>
                    {{-- Rutas Extras --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Rutas Extras</label>
                        <select wire:model="editRutasExtras"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="">Seleccionar...</option>
                            @foreach($rutas as $r)
                                <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Movimientos --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Movimientos</label>
                        <select wire:model="editMovimientos"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="Venta">Venta</option>
                            <option value="Devolucion">Devolucion</option>
                            <option value="Cambio">Cambio</option>
                        </select>
                    </div>
                    {{-- Unidad de Reparto --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Unidad de Reparto</label>
                        <select wire:model="editUnidadReparto"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    {{-- Lista de Precio --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Lista de Precio</label>
                        <select wire:model="editListaPrecio"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="">Seleccionar...</option>
                        </select>
                    </div>
                    {{-- Tipo de Inventario --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Tipo de Inventario</label>
                        <select wire:model="editTipoInventario"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="Inv. Móvil">Inv. Móvil</option>
                            <option value="Inv. General">Inv. General</option>
                        </select>
                    </div>
                    {{-- Tipo de Agenda --}}
                    <div>
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Tipo de Agenda</label>
                        <select wire:model="editTipoAgenda"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]">
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                            <option value="Trisemanal">Trisemanal</option>
                        </select>
                    </div>
                </div>

                {{-- Checkboxes --}}
                <div class="grid grid-cols-2 gap-3 mt-5">
                    @foreach ([['editPermitirCobranza', 'Permitir Cobranza'], ['editPermitirSincVenta', 'Permitir Sinc. Venta'], ['editValidarKm', 'Validar Km.'], ['editValidaKmGeocerca', '¿Valída Km Geocerca?'], ['editAltaClienteNuevo', 'Alta Cliente Nuevo'], ['editValidarInvGralPreventa', 'Validar Inv. Gral. Preventa'], ['editSincronizadoCheck', 'Sincronizado'], ['editActualizarCoordCliente', 'Actualizar Coord. Cliente'], ['editDescargarSoloAgendados', '¿Descargar sólo agendados?'], ['editValidaGeocerca', 'Valída Geocerca']] as [$field, $label])
                        <div class="flex flex-col justify-center">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="{{ $field }}"
                                    class="w-4 h-4 text-[#003859] rounded border-gray-300 focus:ring-[#003859]" />
                                {{ $label }}
                            </label>
                            @if($field === 'editValidaGeocerca' && $editValidaGeocerca)
                                <div class="mt-2 pl-6 animate-fade-in-down">
                                    <label class="text-xs font-semibold text-gray-500 mb-1 block">Distancia Geocerca</label>
                                    <input type="number" wire:model="editDistanciaGeocerca" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859]" />
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button wire:click="cerrarEditar"
                        class="px-5 py-2 text-sm font-semibold text-[#003859] hover:bg-gray-50 rounded-lg transition cursor-pointer">Cancelar</button>
                    <button wire:click="cerrarEditar"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#003859] hover:bg-[#004f7c] rounded-lg transition shadow-sm cursor-pointer">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════ MODAL: Serie de Filtros ═══════════════ --}}
    @if ($showSerieModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarSerie">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 mx-4">
                <h2 class="text-lg font-bold text-[#003859] mb-5">{{ $serieRutaNombre }}</h2>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Serie</th>
                                <th class="px-4 py-3">Folio</th>
                                <th class="px-4 py-3 text-center">Rellenar</th>
                                <th class="px-4 py-3">Longitud</th>
                                <th class="px-4 py-3">Previsualización</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                            @foreach ($series as $idx => $serie)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 font-medium">{{ $serie['tipo'] }}</td>
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="series.{{ $idx }}.serie" class="w-20 border border-gray-200 rounded px-2 py-1 text-xs font-mono focus:outline-none focus:ring-1 focus:ring-[#003859]" />
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $serie['folio'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" wire:model="series.{{ $idx }}.rellenar"
                                            class="w-4 h-4 text-[#003859] rounded border-gray-300 focus:ring-[#003859]" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" wire:model="series.{{ $idx }}.longitud" class="w-16 border border-gray-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#003859]" />
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                                        {{ $serie['previsualizacion'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button wire:click="cerrarSerie"
                        class="px-5 py-2 text-sm font-semibold text-[#003859] hover:bg-gray-50 rounded-lg transition cursor-pointer">Cancelar</button>
                    <button wire:click="cerrarSerie"
                        class="px-5 py-2 text-sm font-semibold text-white bg-[#003859] hover:bg-[#004f7c] rounded-lg transition shadow-sm cursor-pointer">Guardar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════ MODAL: Kilometraje ═══════════════ --}}
    @if ($showKmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cerrarKm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 mx-4">
                <div class="flex items-center gap-3 mb-5">
                    <button wire:click="cerrarKm" class="text-gray-400 hover:text-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-bold text-[#003859]">Kilometrajes - {{ $kmRutaNombre }}</h2>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Unidad de Reparto</th>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Kilometraje inicial</th>
                                <th class="px-4 py-3">Fecha Km inicial</th>
                                <th class="px-4 py-3">Kilometraje final</th>
                                <th class="px-4 py-3">Fecha Km final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 font-medium">Sin
                                    Registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end gap-4 mt-4 text-xs text-gray-500">
                    <span>0–0 of 0</span>
                </div>
            </div>
        </div>
    @endif
</div>
