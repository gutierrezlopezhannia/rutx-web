<?php
use function Livewire\Volt\{state, layout, computed};

layout('layouts.app');

state([
    'zona' => '1Z - Zona 1',
    'ruta' => '', // Inicia vacío para mostrar el placeholder
    'tipo_semana' => 'Semana',

    // Toggles de la barra lateral
    'agendar_desde_panel' => true,
    'filtrar_sin_agendar' => false,
    'search' => '',

    // Datos Simulados
    'zonas' => ['1Z - Zona 1', '2Z - Zona 2', '3Z - Zona 3'],
    'rutas' => [['clave' => '3983', 'nombre' => 'RUTA01'], ['clave' => '4682', 'nombre' => 'RUTA02'], ['clave' => '4683', 'nombre' => 'RUTA03']],
    'clientes' => [['id' => 'R1001', 'nombre' => 'CLIENTE RUTA 01 EVENTUAL'], ['id' => 'EVEN0001', 'nombre' => 'CLIENTE EVENTUAL R1 - 1'], ['id' => 'EVEN0002', 'nombre' => 'CLIENTE EVENTUAL R1 - 2'], ['id' => 'EVEN0003', 'nombre' => 'CLIENTE EVENTUAL R1 - 3'], ['id' => 'EVEN0004', 'nombre' => 'CLIENTE EVENTUAL R1 - 4']],
    'dias' => [
        'Lunes' => [],
        'Martes' => [],
        'Miércoles' => [],
        'Jueves' => [],
        'Viernes' => [],
        'Sábado' => [],
        'Domingo' => [['id' => 'R1001', 'nombre' => 'CLIENTE RUTA 01 EVENTUAL'], ['id' => 'EVEN0001', 'nombre' => 'CLIENTE EVENTUAL R1 - 1'], ['id' => 'EVEN0002', 'nombre' => 'CLIENTE EVENTUAL R1 - 2'], ['id' => 'EVEN0003', 'nombre' => 'CLIENTE EVENTUAL R1 - 3']],
    ],
]);

$removerCliente = function ($dia, $index) {
    unset($this->dias[$dia][$index]);
    $this->dias[$dia] = array_values($this->dias[$dia]); // Reindexar
};

$toggleDiaCliente = function ($dia, $clienteId) {
    $encontrado = false;
    foreach ($this->dias[$dia] as $idx => $c) {
        if ($c['id'] === $clienteId) {
            unset($this->dias[$dia][$idx]);
            $this->dias[$dia] = array_values($this->dias[$dia]); // Reindexar
            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        $clienteData = collect($this->clientes)->firstWhere('id', $clienteId);
        if ($clienteData) {
            $this->dias[$dia][] = $clienteData;
        }
    }
};

$clientesFiltrados = computed(function () {
    if (empty($this->search)) {
        return $this->clientes;
    }
    return array_filter($this->clientes, function ($c) {
        return stripos($c['id'] . ' ' . $c['nombre'], $this->search) !== false;
    });
});
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
                <span class="text-[#003859] font-bold">Agenda</span>
            </div>

            {{-- Contenedor Principal --}}
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden flex flex-col h-[calc(100vh-180px)] min-h-[600px]">

                {{-- Title --}}
                <div class="px-5 pt-5 pb-3">
                    <h1 class="text-xl font-bold text-[#003859]">Agenda</h1>
                </div>

                {{-- Barra de Filtros Superior --}}
                <div class="px-5 pb-4 border-b border-gray-100 flex flex-wrap gap-3 items-center">

                    {{-- Filtro Zona --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="zona"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            @foreach ($zonas as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtro Ruta --}}
                    <div class="relative w-full lg:w-64">
                        <select wire:model.live="ruta"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="">Seleccione la ruta</option>
                            @foreach ($rutas as $r)
                                <option value="{{ $r['clave'] }}">{{ $r['clave'] }} - {{ $r['nombre'] }}</option>
                            @endforeach
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Filtro Semana --}}
                    <div class="relative w-full lg:w-48">
                        <select wire:model.live="tipo_semana"
                            class="appearance-none border border-gray-200 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] bg-white text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="Semana">Semana</option>
                            <option value="Lunes a Viernes">Lunes a Viernes</option>
                            <option value="Fin de semana">Fin de semana</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                @if ($ruta)
                    {{-- Área del Tablero (Kanban) --}}
                    <div class="flex flex-1 overflow-x-auto overflow-y-hidden bg-[#f1f5f9] p-4 gap-4">

                        {{-- Panel Izquierdo: Lista de Clientes --}}
                        <div class="flex-shrink-0 w-72 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col max-h-full z-10">
                            <div class="p-4 border-b border-gray-100 space-y-4 rounded-t-xl bg-white">

                                {{-- Toggle: Agendar desde Panel --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Agendar desde Panel</span>
                                    <button type="button" wire:click="$toggle('agendar_desde_panel')"
                                        class="{{ $agendar_desde_panel ? 'bg-[#003859]' : 'bg-gray-200' }} relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out">
                                        <span
                                            class="{{ $agendar_desde_panel ? 'translate-x-4' : 'translate-x-0' }} pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </div>

                                {{-- Toggle: Filtrar Clientes sin agendar --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">Filtrar Clientes sin
                                        agendar</span>
                                    <button type="button" wire:click="$toggle('filtrar_sin_agendar')"
                                        class="{{ $filtrar_sin_agendar ? 'bg-[#003859]' : 'bg-gray-200' }} relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out">
                                        <span
                                            class="{{ $filtrar_sin_agendar ? 'translate-x-4' : 'translate-x-0' }} pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                    </button>
                                </div>

                                {{-- Buscador --}}
                                <div class="relative pt-2">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-1 mt-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </span>
                                    <input type="text" wire:model.live="search" placeholder="Filtrar..."
                                        class="w-full pl-7 pr-3 py-1.5 border-b border-gray-300 focus:border-[#003859] focus:outline-none text-sm text-gray-700 transition-colors bg-transparent" />
                                </div>
                            </div>

                            {{-- Lista scrolleable de Clientes --}}
                            <div class="flex-1 overflow-y-auto p-3 space-y-3 bg-[#f8fafc] rounded-b-xl">
                                <div class="text-xs font-bold text-[#003859] px-1 border-b border-gray-200 pb-1 mb-2">
                                    Clientes: {{ count($this->clientesFiltrados) }}</div>
                                @foreach ($this->clientesFiltrados as $cliente)
                                    <div
                                        class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:border-[#003859] transition cursor-grab">
                                        <p class="text-xs font-bold text-gray-800 mb-2 truncate"
                                            title="{{ $cliente['id'] }} - {{ $cliente['nombre'] }}">
                                            {{ $cliente['id'] }} - {{ $cliente['nombre'] }}
                                        </p>
                                        <div class="flex gap-1 flex-wrap">
                                            @foreach(['Lunes'=>'L', 'Martes'=>'M', 'Miércoles'=>'M', 'Jueves'=>'J', 'Viernes'=>'V', 'Sábado'=>'S', 'Domingo'=>'D'] as $nombreDia => $letra)
                                                @php
                                                    $estaEnDia = in_array($cliente['id'], array_column($dias[$nombreDia] ?? [], 'id'));
                                                @endphp
                                                <button wire:click="toggleDiaCliente('{{ $nombreDia }}', '{{ $cliente['id'] }}')"
                                                    title="{{ $nombreDia }}"
                                                    class="w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold transition focus:outline-none {{ $estaEnDia ? 'bg-[#003859] text-white border border-[#003859]' : 'bg-gray-100 text-gray-400 hover:bg-gray-200 border border-transparent' }}">
                                                    {{ $letra }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Columnas del Tablero (Días) --}}
                            @foreach ($dias as $dia => $clientesDia)
                                <div
                                    class="flex-shrink-0 w-64 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col max-h-full">
                                    <div
                                        class="p-3 border-b border-gray-100 flex items-center justify-between bg-white rounded-t-xl sticky top-0 z-10 shadow-sm">
                                        <h3 class="font-bold text-sm text-[#003859]">{{ $dia }} <span
                                                class="text-gray-500 font-semibold ml-1">({{ count($clientesDia) }})</span>
                                        </h3>
                                        <button class="text-gray-400 hover:text-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="flex-1 overflow-y-auto p-2 space-y-2 min-h-[150px] bg-[#f8fafc]">
                                        @foreach ($clientesDia as $idx => $c)
                                            <div
                                                class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition group relative border-l-4 border-l-[#003859] cursor-pointer">
                                                <p class="text-xs font-semibold text-gray-700 pr-5 leading-tight">
                                                    {{ $c['id'] }} - {{ $c['nombre'] }}</p>
                                                <button
                                                    wire:click="removerCliente('{{ $dia }}', {{ $idx }})"
                                                    class="absolute top-2 right-2 text-gray-300 hover:text-red-500 transition bg-white rounded-full p-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                    </div>
                @else
                    {{-- Estado Vacío (cuando no hay ruta seleccionada) --}}
                    <div class="flex-1 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                        <div class="bg-gray-100 p-4 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <p class="text-lg font-semibold text-gray-500">Seleccione una ruta para ver la agenda</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
