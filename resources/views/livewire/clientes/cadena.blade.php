<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockCadenas = [
    [
        'id' => 1,
        'nombre' => 'Grupo Supermercados del Norte',
        'identificador' => 'CAD-001',
        'descripcion' => 'Cadena principal de autoservicios en la región norte.',
        'clientes' => [
            ['id_cliente' => 'CLI-1001', 'razon_social' => 'Supermercados del Norte SA - Sucursal Hermosillo', 'rfc' => 'SUN980122ABC', 'limite_credito' => 50000.00],
            ['id_cliente' => 'CLI-1004', 'razon_social' => 'Supermercados del Norte SA - Sucursal Nogales', 'rfc' => 'SUN980122ABC', 'limite_credito' => 100000.00],
        ]
    ],
    [
        'id' => 2,
        'nombre' => 'Franquicia Tiendas Rápida 24/7',
        'identificador' => 'CAD-002',
        'descripcion' => 'Tiendas de conveniencia con operación continua.',
        'clientes' => [
            ['id_cliente' => 'CLI-1002', 'razon_social' => 'Tiendas Rápida 24/7 - Sucursal Centro', 'rfc' => 'TRD050630JKL', 'limite_credito' => 5000.00],
            ['id_cliente' => 'CLI-1005', 'razon_social' => 'Tiendas Rápida 24/7 - Sucursal Poniente', 'rfc' => 'TRD050630JKL', 'limite_credito' => 20000.00],
        ]
    ],
    [
        'id' => 3,
        'nombre' => 'Alianza Comercial del Sur',
        'identificador' => 'CAD-003',
        'descripcion' => 'Grupo de abarroteras asociadas del sur del estado.',
        'clientes' => [
            ['id_cliente' => 'CLI-1003', 'razon_social' => 'Minisuper Centro - Matriz', 'rfc' => 'MIC991201GHI', 'limite_credito' => 15000.00],
        ]
    ]
];

// Clientes sueltos que se pueden agregar a cadenas
$mockClientesSueltos = [
    ['id_cliente' => 'CLI-1006', 'razon_social' => 'Distribuidora Monterrey', 'rfc' => 'DMO851104PQR'],
    ['id_cliente' => 'CLI-1007', 'razon_social' => 'Farmacia y Minisuper San José', 'rfc' => 'FMS950412STU'],
    ['id_cliente' => 'CLI-1008', 'razon_social' => 'Carnes y Vinos Premium', 'rfc' => 'CVP920718VWX'],
    ['id_cliente' => 'CLI-1009', 'razon_social' => 'Abarrotes Don Lucho', 'rfc' => 'ADL900101YZA'],
];

state([
    'cadenas' => $mockCadenas,
    'clientesSueltos' => $mockClientesSueltos,
    'selectedCadenaId' => 1,
    'searchCadenas' => '',
    'searchMembers' => '',
    'selectedClienteParaAgregar' => '',
    'nuevaCadenaNombre' => '',
    'nuevaCadenaDesc' => '',
    'showCrearModal' => false,
    'notification' => '',
]);

$selectCadena = function ($id) {
    $this->selectedCadenaId = $id;
    $this->searchMembers = '';
    $this->selectedClienteParaAgregar = '';
};

$agregarClienteACadena = function () {
    if (empty($this->selectedClienteParaAgregar)) {
        return;
    }

    $clienteId = $this->selectedClienteParaAgregar;
    $clienteObj = null;

    // Buscar en clientes sueltos
    foreach ($this->clientesSueltos as $index => $cs) {
        if ($cs['id_cliente'] === $clienteId) {
            $clienteObj = $cs;
            // Remover de la lista de sueltos
            $tempSueltos = $this->clientesSueltos;
            unset($tempSueltos[$index]);
            $this->clientesSueltos = array_values($tempSueltos);
            break;
        }
    }

    if ($clienteObj) {
        // Agregar límite simulado
        $clienteObj['limite_credito'] = 15000.00;

        // Agregar a la cadena seleccionada
        $tempCadenas = $this->cadenas;
        foreach ($tempCadenas as &$c) {
            if ($c['id'] == $this->selectedCadenaId) {
                $c['clientes'][] = $clienteObj;
                $this->notification = "Cliente '{$clienteObj['razon_social']}' agregado con éxito a '{$c['nombre']}'.";
                break;
            }
        }
        $this->cadenas = $tempCadenas;
    }

    $this->selectedClienteParaAgregar = '';
};

$crearCadena = function () {
    if (empty($this->nuevaCadenaNombre)) {
        return;
    }

    $newId = count($this->cadenas) + 1;
    $identificador = 'CAD-' . str_pad($newId, 3, '0', STR_PAD_LEFT);

    $nueva = [
        'id' => $newId,
        'nombre' => $this->nuevaCadenaNombre,
        'identificador' => $identificador,
        'descripcion' => $this->nuevaCadenaDesc ?: 'Sin descripción.',
        'clientes' => []
    ];

    $this->cadenas[] = $nueva;
    $this->selectedCadenaId = $newId;
    $this->notification = "Cadena comercial '{$this->nuevaCadenaNombre}' creada con éxito.";
    
    // Resetear formulario y cerrar modal
    $this->nuevaCadenaNombre = '';
    $this->nuevaCadenaDesc = '';
    $this->showCrearModal = false;
};

$cerrarNotificacion = function () {
    $this->notification = '';
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Cliente</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Cadena de Clientes</span>
            </div>

            {{-- Notification Alert --}}
            @if(!empty($notification))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center justify-between shadow-sm animate-fade-in-down">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium">{{ $notification }}</span>
                    </div>
                    <button wire:click="cerrarNotificacion" class="text-green-600 hover:text-green-800 p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#1f2937] tracking-tight">Agrupación de Cadena de Clientes</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Asociación y gestión de sucursales en grupos corporativos y franquicias</p>
                </div>
                <div>
                    <button wire:click="$set('showCrearModal', true)" 
                        class="flex items-center gap-2 bg-[#004066] hover:bg-[#003352] text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all duration-150 shadow-sm focus:outline-none cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Cadena
                    </button>
                </div>
            </div>

            {{-- Layout 2 columns: Left list, Right details --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left Column: Chain List --}}
                <div class="lg:col-span-1 flex flex-col gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden flex flex-col h-full">
                        <div class="p-4 border-b border-gray-100 bg-[#fafbfc]">
                            <h2 class="font-bold text-sm text-gray-700 uppercase tracking-wider mb-3">Cadenas Comerciales</h2>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    wire:model.live="searchCadenas" 
                                    placeholder="Buscar cadena..." 
                                    class="block w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150"
                                />
                            </div>
                        </div>
                        <div class="flex-1 overflow-y-auto divide-y divide-gray-100 max-h-[500px]">
                            @php
                                $cadenasFiltradas = collect($cadenas);
                                if (!empty($searchCadenas)) {
                                    $searchVal = strtolower(trim($searchCadenas));
                                    $cadenasFiltradas = $cadenasFiltradas->filter(function($c) use ($searchVal) {
                                        return str_contains(strtolower($c['nombre']), $searchVal) ||
                                               str_contains(strtolower($c['identificador']), $searchVal);
                                    });
                                }
                            @endphp

                            @forelse($cadenasFiltradas as $cadena)
                                <button 
                                    wire:click="selectCadena({{ $cadena['id'] }})"
                                    class="w-full text-left p-4 hover:bg-gray-50/70 transition-colors flex items-start gap-3 focus:outline-none cursor-pointer {{ $selectedCadenaId == $cadena['id'] ? 'bg-orange-50/50 border-r-4 border-orange-500' : '' }}"
                                >
                                    <div class="p-2 rounded bg-gray-100 text-[#004066] shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <span class="text-xs font-mono font-bold text-gray-400">{{ $cadena['identificador'] }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[#e6f2ff] text-[#0066cc] border border-[#cce3ff]">
                                                {{ count($cadena['clientes']) }} sucursales
                                            </span>
                                        </div>
                                        <h3 class="font-semibold text-sm text-gray-800 truncate mt-0.5">{{ $cadena['nombre'] }}</h3>
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-1">{{ $cadena['descripcion'] }}</p>
                                    </div>
                                </button>
                            @empty
                                <div class="p-8 text-center text-xs text-gray-400">
                                    No se encontraron cadenas comerciales.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right Column: Chain Details & Members --}}
                <div class="lg:col-span-2">
                    @php
                        $cadenaActiva = collect($cadenas)->firstWhere('id', $selectedCadenaId);
                    @endphp

                    @if($cadenaActiva)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden flex flex-col gap-5 p-5">
                            
                            {{-- Active Chain Info --}}
                            <div class="border-b border-gray-100 pb-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-500">{{ $cadenaActiva['identificador'] }}</span>
                                    <span class="text-xs font-bold text-orange-600 tracking-wide uppercase">Cadena Comercial Seleccionada</span>
                                </div>
                                <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $cadenaActiva['nombre'] }}</h2>
                                <p class="text-sm text-gray-500 mt-1.5">{{ $cadenaActiva['descripcion'] }}</p>
                            </div>

                            {{-- Association Tool --}}
                            <div class="bg-gray-50/70 rounded-lg border border-gray-100 p-4">
                                <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Asociar Cliente a esta Cadena</h3>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <select 
                                            wire:model.live="selectedClienteParaAgregar"
                                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] cursor-pointer"
                                        >
                                            <option value="">-- Seleccionar cliente libre --</option>
                                            @foreach($clientesSueltos as $cs)
                                                <option value="{{ $cs['id_cliente'] }}">{{ $cs['id_cliente'] }} | {{ $cs['razon_social'] }} ({{ $cs['rfc'] }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <button 
                                            wire:click="agregarClienteACadena"
                                            @if(empty($selectedClienteParaAgregar)) disabled @endif
                                            class="w-full sm:w-auto bg-[#004066] hover:bg-[#003352] text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all duration-150 shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                                        >
                                            Asociar
                                        </button>
                                    </div>
                                </div>
                                @if(count($clientesSueltos) === 0)
                                    <p class="text-[11px] text-gray-400 mt-2.5">
                                        * No quedan clientes libres disponibles para asociar.
                                    </p>
                                @endif
                            </div>

                            {{-- Member list table --}}
                            <div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Clientes Asociados ({{ count($cadenaActiva['clientes']) }})</h3>
                                    
                                    {{-- Mini Search within members --}}
                                    <div class="relative w-full sm:w-64">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                        <input 
                                            type="text" 
                                            wire:model.live="searchMembers" 
                                            placeholder="Buscar en asociados..." 
                                            class="block w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066]"
                                        />
                                    </div>
                                </div>

                                <div class="border border-gray-150 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-100 text-left">
                                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                                            <tr>
                                                <th scope="col" class="px-4 py-3">ID Cliente</th>
                                                <th scope="col" class="px-4 py-3">Razón Social</th>
                                                <th scope="col" class="px-4 py-3 text-right">Límite Crédito</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100 text-xs text-gray-700">
                                            @php
                                                $members = collect($cadenaActiva['clientes']);
                                                if (!empty($searchMembers)) {
                                                    $sVal = strtolower(trim($searchMembers));
                                                    $members = $members->filter(function($m) use ($sVal) {
                                                        return str_contains(strtolower($m['id_cliente']), $sVal) ||
                                                               str_contains(strtolower($m['razon_social']), $sVal);
                                                    });
                                                }
                                            @endphp

                                            @forelse($members as $m)
                                                <tr class="hover:bg-gray-50/50 transition-colors">
                                                    <td class="px-4 py-3 font-semibold text-[#004066]">{{ $m['id_cliente'] }}</td>
                                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $m['razon_social'] }}</td>
                                                    <td class="px-4 py-3 text-right font-medium text-gray-800">${{ number_format($m['limite_credito'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">
                                                        No hay clientes asociados que coincidan.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 p-8 text-center text-gray-400">
                            Por favor, seleccione una cadena comercial para ver sus detalles.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Modal para crear nueva cadena --}}
            @if($showCrearModal)
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50 animate-fade-in">
                    <div class="bg-white rounded-xl shadow-xl overflow-hidden max-w-md w-full border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 bg-[#fafbfc] flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Crear Nueva Cadena Comercial</h3>
                            <button wire:click="$set('showCrearModal', false)" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-6 flex flex-col gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre de la Cadena</label>
                                <input 
                                    type="text" 
                                    wire:model="nuevaCadenaNombre" 
                                    placeholder="Ej. Grupo Walmart México" 
                                    class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066]"
                                />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción / Nota</label>
                                <textarea 
                                    wire:model="nuevaCadenaDesc" 
                                    placeholder="Ej. Detalle de logística centralizada." 
                                    rows="3" 
                                    class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] resize-none"
                                ></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-100 bg-[#fafbfc] flex justify-end gap-3">
                            <button 
                                wire:click="$set('showCrearModal', false)" 
                                class="bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 border border-gray-300 rounded-lg text-sm transition-all focus:outline-none cursor-pointer"
                            >
                                Cancelar
                            </button>
                            <button 
                                wire:click="crearCadena" 
                                class="bg-[#004066] hover:bg-[#003352] text-white font-semibold px-4 py-2 rounded-lg text-sm transition-all focus:outline-none cursor-pointer"
                            >
                                Crear Cadena
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
