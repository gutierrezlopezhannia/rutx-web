<?php

use function Livewire\Volt\{state, layout, mount, computed};
use App\Models\Zone;
use App\Models\Seller;

layout('layouts.app');

state([
    'zonas' => [],
    'vendedores' => [],
    'selectedZona' => '',
    'selectedOrigen' => '',
    'selectedDestino' => '',
    'searchOrigen' => '',
    'searchDestino' => '',
    'checkedOrigen' => [],
    'checkedDestino' => [],
    'clientesPorRuta' => [],
    'initialClientesPorRuta' => [],
    'notification' => '',
    'warning' => '',
    'mostrarModalConfirmacion' => false,
]);

mount(function () {
    // 1. Cargar Zonas desde BD o fallback de alta fidelidad
    $zonas = Zone::all()->toArray();
    if (empty($zonas)) {
        $zonas = [
            ['id' => '1Z - Zona 1', 'name' => 'Zona 1'],
            ['id' => '2Z - Zona 2', 'name' => 'Zona 2'],
        ];
    } else {
        // Forzar la existencia de la zona que sale en la captura
        if (!collect($zonas)->contains('id', '1Z - Zona 1')) {
            array_unshift($zonas, ['id' => '1Z - Zona 1', 'name' => 'Zona 1']);
        }
    }
    $this->zonas = $zonas;
    $this->selectedZona = $zonas[0]['id'];

    // 2. Cargar Vendedores/Rutas desde BD o fallback
    $vendedores = Seller::where('oculto', 'N')->get()->toArray();
    if (empty($vendedores)) {
        $vendedores = [
            ['id' => '3983 - RUTA01', 'name' => 'RUTA01'],
            ['id' => '4683 - RUTA03', 'name' => 'RUTA03'],
            ['id' => '3984 - RUTA02', 'name' => 'RUTA02'],
        ];
    } else {
        // Forzar las rutas de la captura de pantalla
        if (!collect($vendedores)->contains('id', '3983 - RUTA01')) {
            array_unshift($vendedores, ['id' => '3983 - RUTA01', 'name' => 'RUTA01']);
        }
        if (!collect($vendedores)->contains('id', '4683 - RUTA03')) {
            $vendedores[] = ['id' => '4683 - RUTA03', 'name' => 'RUTA03'];
        }
    }
    $this->vendedores = $vendedores;
    $this->selectedOrigen = '3983 - RUTA01';
    $this->selectedDestino = '4683 - RUTA03';

    // 3. Estructurar clientes por ruta (con fallback exacto del mockup)
    $inicialClientes = [
        '3983 - RUTA01' => [
            ['id' => 'R1001', 'nombre' => 'CLIENTE RUTA 01 EVENTUAL', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0004', 'nombre' => 'CLIENTE EVENTUAL R1 - 4', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0005', 'nombre' => 'CLIENTE EVENTUAL R1 - 5', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0006', 'nombre' => 'CLIENTE EVENTUAL R1 - 6', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0007', 'nombre' => 'CLIENTE EVENTUAL R1 - 7', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0008', 'nombre' => 'CLIENTE EVENTUAL R1 - 8', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0009', 'nombre' => 'CLIENTE EVENTUAL R1 - 9', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0010', 'nombre' => 'CLIENTE EVENTUAL R1 - 10', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0161', 'nombre' => 'CLIENTE EVENTUAL MAR R1 - 1', 'detalle' => '. . - . .'],
        ],
        '4683 - RUTA03' => [
            ['id' => 'EVEN0001', 'nombre' => 'CLIENTE EVENTUAL R1 - 1', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0002', 'nombre' => 'CLIENTE EVENTUAL R1 - 2', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0003', 'nombre' => 'CLIENTE EVENTUAL R1 - 3', 'detalle' => '. . - . .'],
        ],
        '3984 - RUTA02' => [
            ['id' => 'EVEN0015', 'nombre' => 'CLIENTE EVENTUAL R2 - 1', 'detalle' => '. . - . .'],
            ['id' => 'EVEN0016', 'nombre' => 'CLIENTE EVENTUAL R2 - 2', 'detalle' => '. . - . .'],
        ]
    ];

    // Cargar clientes de base de datos si existen
    $dbCustomers = \App\Models\Customer::all();
    if ($dbCustomers->isNotEmpty()) {
        $inicialClientes['999001 - VENDEDOR PRUEBA'] = $dbCustomers->map(fn($c) => [
            'id' => $c->clave,
            'nombre' => $c->nombre,
            'detalle' => $c->direccion ?: 'Sin dirección registrada'
        ])->toArray();
    }

    $this->clientesPorRuta = session('clientes_por_ruta', $inicialClientes);
    $this->initialClientesPorRuta = $this->clientesPorRuta;
});

// Reactivo al cambiar select de origen
$updatedSelectedOrigen = function ($val) {
    $this->checkedOrigen = [];
    $this->notification = '';
    $this->warning = '';
};

// Reactivo al cambiar select de destino
$updatedSelectedDestino = function ($val) {
    $this->checkedDestino = [];
    $this->notification = '';
    $this->warning = '';
};

// Transferir clientes de Izquierda a Derecha (Origen -> Destino)
$transferirA = function () {
    if (empty($this->checkedOrigen)) return;

    $originId = $this->selectedOrigen;
    $destId = $this->selectedDestino;

    $clientsOrigin = collect($this->clientesPorRuta[$originId] ?? []);
    $clientsDest = collect($this->clientesPorRuta[$destId] ?? []);

    $toMove = $clientsOrigin->whereIn('id', $this->checkedOrigen);
    $remaining = $clientsOrigin->whereNotIn('id', $this->checkedOrigen);

    $this->clientesPorRuta[$originId] = $remaining->values()->toArray();
    $this->clientesPorRuta[$destId] = $clientsDest->concat($toMove)->values()->toArray();

    $this->checkedOrigen = [];
    $this->notification = '';
    $this->warning = '';
    session(['clientes_por_ruta' => $this->clientesPorRuta]);
};

// Transferir clientes de Derecha a Izquierda (Destino -> Origen)
$transferirB = function () {
    if (empty($this->checkedDestino)) return;

    $originId = $this->selectedOrigen;
    $destId = $this->selectedDestino;

    $clientsOrigin = collect($this->clientesPorRuta[$originId] ?? []);
    $clientsDest = collect($this->clientesPorRuta[$destId] ?? []);

    $toMove = $clientsDest->whereIn('id', $this->checkedDestino);
    $remaining = $clientsDest->whereNotIn('id', $this->checkedDestino);

    $this->clientesPorRuta[$destId] = $remaining->values()->toArray();
    $this->clientesPorRuta[$originId] = $clientsOrigin->concat($toMove)->values()->toArray();

    $this->checkedDestino = [];
    $this->notification = '';
    $this->warning = '';
    session(['clientes_por_ruta' => $this->clientesPorRuta]);
};

// Seleccionar/Deseleccionar todo Origen
$toggleAllOrigen = function () {
    $filtered = $this->getFilteredOrigen();
    $filteredIds = collect($filtered)->pluck('id')->toArray();
    
    // Si todos los filtrados están marcados, los desmarcamos
    if (collect($filteredIds)->every(fn($id) => in_array($id, $this->checkedOrigen))) {
        $this->checkedOrigen = array_values(array_diff($this->checkedOrigen, $filteredIds));
    } else {
        // De lo contrario, los marcamos todos
        $this->checkedOrigen = array_values(array_unique(array_merge($this->checkedOrigen, $filteredIds)));
    }
};

// Seleccionar/Deseleccionar todo Destino
$toggleAllDestino = function () {
    $filtered = $this->getFilteredDestino();
    $filteredIds = collect($filtered)->pluck('id')->toArray();
    
    if (collect($filteredIds)->every(fn($id) => in_array($id, $this->checkedDestino))) {
        $this->checkedDestino = array_values(array_diff($this->checkedDestino, $filteredIds));
    } else {
        $this->checkedDestino = array_values(array_unique(array_merge($this->checkedDestino, $filteredIds)));
    }
};

// Validaciones reactivas como propiedad computada
$validationErrors = computed(function () {
    $errors = [];
    if (empty($this->selectedOrigen) || empty($this->selectedDestino)) {
        $errors[] = 'Debe seleccionar tanto la ruta de origen como la ruta de destino.';
    }
    if ($this->selectedOrigen === $this->selectedDestino) {
        $errors[] = 'La ruta de origen y la ruta de destino no pueden ser la misma.';
    }
    return $errors;
});

// Simulación de guardado con validaciones
$guardar = function () {
    $errors = $this->validationErrors;
    if (!empty($errors)) {
        $this->notification = '';
        $this->warning = implode(' ', $errors);
        return;
    }

    // Verificar si hay cambios reales respecto al estado inicial
    if (serialize($this->clientesPorRuta) === serialize($this->initialClientesPorRuta)) {
        $this->notification = '';
        $this->warning = 'No se han detectado cambios para guardar. Realice un traspaso de cliente primero.';
        return;
    }

    // Si todo está correcto, abrimos el modal de confirmación
    $this->mostrarModalConfirmacion = true;
};

// Confirmación final del guardado
$confirmarGuardar = function () {
    session(['clientes_por_ruta' => $this->clientesPorRuta]);
    $this->initialClientesPorRuta = $this->clientesPorRuta;
    $this->notification = 'El traspaso de clientes ha sido guardado de forma exitosa.';
    $this->warning = '';
    $this->mostrarModalConfirmacion = false;
};

// Cancelar modal
$cancelarGuardar = function () {
    $this->mostrarModalConfirmacion = false;
};

$cerrarNotificacion = function () {
    $this->notification = '';
    $this->warning = '';
};

// Filtro computado de Origen
$getFilteredOrigen = function () {
    $clients = collect($this->clientesPorRuta[$this->selectedOrigen] ?? []);
    if (!empty($this->searchOrigen)) {
        $search = strtolower(trim($this->searchOrigen));
        $clients = $clients->filter(function ($c) use ($search) {
            return str_contains(strtolower($c['id']), $search) || str_contains(strtolower($c['nombre']), $search);
        });
    }
    return $clients->values()->toArray();
};

// Filtro computado de Destino
$getFilteredDestino = function () {
    $clients = collect($this->clientesPorRuta[$this->selectedDestino] ?? []);
    if (!empty($this->searchDestino)) {
        $search = strtolower(trim($this->searchDestino));
        $clients = $clients->filter(function ($c) use ($search) {
            return str_contains(strtolower($c['id']), $search) || str_contains(strtolower($c['nombre']), $search);
        });
    }
    return $clients->values()->toArray();
};

?>

@php
    $validationErrors = $this->validationErrors;
    $hasValidationErrors = count($validationErrors) > 0;
@endphp

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Cliente</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Traspaso de Cliente</span>
            </div>

            {{-- Notification Alert --}}
            @if(!empty($notification))
                <div class="mb-5 bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 flex items-center justify-between shadow-sm animate-fade-in-down">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-semibold">{{ $notification }}</span>
                    </div>
                    <button wire:click="cerrarNotificacion" class="text-green-600 hover:text-green-800 p-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            {{-- Header principal --}}
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[#1f2937] tracking-tight">Traspaso de Clientes entre Rutas</h1>
                <p class="text-sm text-gray-500 mt-0.5">Mueve clientes seleccionados de una ruta de origen a una ruta de destino en la misma zona</p>
            </div>

            {{-- Faja Superior de Parámetros y Botón Guardar --}}
            <div class="bg-white p-5 border border-gray-200/90 rounded-xl shadow-sm mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div class="w-full sm:w-80">
                    <label class="text-xs text-gray-500 font-bold mb-1.5 block">Zona</label>
                    <div class="relative">
                        <select wire:model.live="selectedZona" 
                            class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all cursor-pointer shadow-inner pr-8 appearance-none"
                        >
                            @foreach($zonas as $z)
                                <option value="{{ $z['id'] }}">{{ $z['id'] }} - {{ $z['name'] }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <button wire:click="guardar" @disabled($hasValidationErrors)
                        class="w-full sm:w-auto bg-[#004f7c] hover:bg-[#003859] active:scale-95 text-white font-semibold px-6 py-2.5 rounded-lg text-sm transition-all duration-150 shadow-sm focus:outline-none cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#004f7c] disabled:scale-100"
                    >
                        <svg wire:loading.remove wire:target="guardar" class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        <svg wire:loading wire:target="guardar" class="animate-spin h-4.5 w-4.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Guardar</span>
                    </button>
                </div>
            </div>

            {{-- Warning / Validation Alerts --}}
            @if($hasValidationErrors || !empty($warning))
                <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 flex items-start gap-3 shadow-sm animate-fade-in">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1 text-sm font-medium">
                        @if($hasValidationErrors)
                            <ul class="list-disc pl-4 space-y-1">
                                @foreach($validationErrors as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span>{{ $warning }}</span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Bloque de Traspaso (Origen - Botones - Destino) --}}
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_80px_1fr] gap-6 items-center">
                
                {{-- Columna Izquierda: Ruta de Origen --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden flex flex-col h-[580px]">
                    {{-- Cabecera de la Tarjeta --}}
                    <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col gap-2">
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Ruta de Origen</span>
                        <div class="relative">
                            <select wire:model.live="selectedOrigen" 
                                class="block w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-800 font-bold focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all cursor-pointer shadow-inner pr-8 appearance-none"
                            >
                                @foreach($vendedores as $v)
                                    <option value="{{ $v['id'] }}">{{ $v['id'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Caja de Búsqueda y Selección Total --}}
                    <div class="p-3 border-b border-gray-50 bg-[#fafbfc]/40 flex items-center gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live="searchOrigen" 
                                placeholder="Filtrar..." 
                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all bg-white"
                            />
                        </div>

                        {{-- Botón Seleccionar Todo --}}
                        @php
                            $filteredOrigen = $this->getFilteredOrigen();
                            $allFilteredOrigenIds = collect($filteredOrigen)->pluck('id')->toArray();
                            $allOrigenChecked = count($allFilteredOrigenIds) > 0 && collect($allFilteredOrigenIds)->every(fn($id) => in_array($id, $checkedOrigen));
                        @endphp
                        <button 
                            type="button"
                            wire:click="toggleAllOrigen"
                            class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-[#004066] bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors cursor-pointer select-none"
                        >
                            {{ $allOrigenChecked ? 'Desmarcar todo' : 'Marcar todo' }}
                        </button>
                    </div>

                    {{-- Lista de Clientes --}}
                    <div class="flex-1 overflow-y-auto p-3 divide-y divide-gray-100">
                        @forelse($filteredOrigen as $c)
                            @php
                                $isChecked = in_array($c['id'], $checkedOrigen);
                            @endphp
                            <label class="flex items-start gap-3 p-3 hover:bg-gray-50/60 rounded-xl cursor-pointer transition-all duration-75 select-none {{ $isChecked ? 'bg-blue-50/30' : '' }}">
                                <input 
                                    type="checkbox" 
                                    value="{{ $c['id'] }}" 
                                    wire:model.live="checkedOrigen" 
                                    class="mt-1 rounded border-gray-300 text-[#004f7c] focus:ring-[#004f7c] h-4.5 w-4.5 cursor-pointer" 
                                />
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-semibold text-gray-800 break-words block leading-snug">
                                        {{ $c['id'] }} - {{ $c['nombre'] }}
                                    </span>
                                    <span class="block text-xs text-gray-400 mt-1 font-medium font-mono select-none">
                                        {{ $c['detalle'] }}
                                    </span>
                                </div>
                            </label>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center py-12 px-4 text-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A9.342 9.342 0 0012.24 21m0 0a9.34 9.34 0 01-2.997-.872M12.24 21c.018-.08.03-.161.03-.245v-2.136c0-1.114-.285-2.16-.786-3.07M9.74 21a9.38 9.38 0 01-2.625.372 9.337 9.337 0 01-4.121-.952 4.125 4.125 0 017.533-2.493M9.74 21v-.003c0-1.113.285-2.16.786-3.07m0 0h.002m-2.29-6.333a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm10.5 0a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm-3 7.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-400">Sin clientes en esta ruta</span>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer de Tarjeta --}}
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center justify-between select-none">
                        <span>Total: {{ count($filteredOrigen) }} clientes</span>
                        @if(count($checkedOrigen) > 0)
                            <span class="text-[#004f7c]">{{ count($checkedOrigen) }} seleccionados</span>
                        @endif
                    </div>
                </div>

                {{-- Controles Centrales (Botones de Traspaso) --}}
                <div class="flex flex-row lg:flex-col items-center justify-center gap-3">
                    @php
                        $hasCheckedOrigen = count($checkedOrigen) > 0;
                        $hasCheckedDestino = count($checkedDestino) > 0;
                    @endphp
                    
                    {{-- Botón Transferir a la derecha (Origen -> Destino) --}}
                    <button 
                        type="button"
                        wire:click="transferirA" 
                        @disabled(!$hasCheckedOrigen || $hasValidationErrors) 
                        class="flex items-center justify-center w-12 h-12 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-[#004f7c] font-bold shadow-sm transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 cursor-pointer hover:scale-105 active:scale-95"
                        title="Transferir clientes seleccionados a la ruta de destino"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- Botón Transferir a la izquierda (Destino -> Origen) --}}
                    <button 
                        type="button"
                        wire:click="transferirB" 
                        @disabled(!$hasCheckedDestino || $hasValidationErrors) 
                        class="flex items-center justify-center w-12 h-12 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-[#004f7c] font-bold shadow-sm transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 cursor-pointer hover:scale-105 active:scale-95"
                        title="Transferir clientes seleccionados a la ruta de origen"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                {{-- Columna Derecha: Ruta de Destino --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden flex flex-col h-[580px]">
                    {{-- Cabecera de la Tarjeta --}}
                    <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-col gap-2">
                        <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Ruta de Destino</span>
                        <div class="relative">
                            <select wire:model.live="selectedDestino" 
                                class="block w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-800 font-bold focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all cursor-pointer shadow-inner pr-8 appearance-none"
                            >
                                @foreach($vendedores as $v)
                                    <option value="{{ $v['id'] }}">{{ $v['id'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Caja de Búsqueda y Selección Total --}}
                    <div class="p-3 border-b border-gray-50 bg-[#fafbfc]/40 flex items-center gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live="searchDestino" 
                                placeholder="Filtrar..." 
                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all bg-white"
                            />
                        </div>

                        {{-- Botón Seleccionar Todo --}}
                        @php
                            $filteredDestino = $this->getFilteredDestino();
                            $allFilteredDestinoIds = collect($filteredDestino)->pluck('id')->toArray();
                            $allDestinoChecked = count($allFilteredDestinoIds) > 0 && collect($allFilteredDestinoIds)->every(fn($id) => in_array($id, $checkedDestino));
                        @endphp
                        <button 
                            type="button"
                            wire:click="toggleAllDestino"
                            class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-[#004066] bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors cursor-pointer select-none"
                        >
                            {{ $allDestinoChecked ? 'Desmarcar todo' : 'Marcar todo' }}
                        </button>
                    </div>

                    {{-- Lista de Clientes --}}
                    <div class="flex-1 overflow-y-auto p-3 divide-y divide-gray-100">
                        @forelse($filteredDestino as $c)
                            @php
                                $isChecked = in_array($c['id'], $checkedDestino);
                            @endphp
                            <label class="flex items-start gap-3 p-3 hover:bg-gray-50/60 rounded-xl cursor-pointer transition-all duration-75 select-none {{ $isChecked ? 'bg-blue-50/30' : '' }}">
                                <input 
                                    type="checkbox" 
                                    value="{{ $c['id'] }}" 
                                    wire:model.live="checkedDestino" 
                                    class="mt-1 rounded border-gray-300 text-[#004f7c] focus:ring-[#004f7c] h-4.5 w-4.5 cursor-pointer" 
                                />
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-semibold text-gray-800 break-words block leading-snug">
                                        {{ $c['id'] }} - {{ $c['nombre'] }}
                                    </span>
                                    <span class="block text-xs text-gray-400 mt-1 font-medium font-mono select-none">
                                        {{ $c['detalle'] }}
                                    </span>
                                </div>
                            </label>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center py-12 px-4 text-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A9.342 9.342 0 0012.24 21m0 0a9.34 9.34 0 01-2.997-.872M12.24 21c.018-.08.03-.161.03-.245v-2.136c0-1.114-.285-2.16-.786-3.07M9.74 21a9.38 9.38 0 01-2.625.372 9.337 9.337 0 01-4.121-.952 4.125 4.125 0 017.533-2.493M9.74 21v-.003c0-1.113.285-2.16.786-3.07m0 0h.002m-2.29-6.333a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm10.5 0a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm-3 7.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-400">Sin clientes en esta ruta</span>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer de Tarjeta --}}
                    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center justify-between select-none">
                        <span>Total: {{ count($filteredDestino) }} clientes</span>
                        @if(count($checkedDestino) > 0)
                            <span class="text-[#004f7c]">{{ count($checkedDestino) }} seleccionados</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Modal de Confirmación --}}
            @if($mostrarModalConfirmacion)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto outline-none">
                    {{-- Backdrop --}}
                    <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity animate-fade-in" wire:click="cancelarGuardar"></div>
                    
                    {{-- Card del Modal --}}
                    <div class="relative bg-white rounded-lg shadow-2xl z-10 border border-gray-100 overflow-hidden w-full" style="max-width: 500px;">
                        <div class="p-6 flex flex-col gap-4">
                            {{-- Header --}}
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-800 tracking-wide uppercase">TRASPASO CLIENTE</h3>
                                <button type="button" wire:click="cancelarGuardar" class="text-gray-400 hover:text-gray-600 transition-colors p-1 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="py-2">
                                <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                    ¿Está seguro que desea actualizar la ruta de los Clientes seleccionados?
                                </p>
                            </div>

                            {{-- Footer --}}
                            <div class="flex justify-end gap-6 pt-2">
                                <button type="button" wire:click="confirmarGuardar" class="text-sm font-bold text-[#2196F3] hover:text-[#0b7dda] transition-colors cursor-pointer select-none">
                                    CONFIRMAR
                                </button>
                                <button type="button" wire:click="cancelarGuardar" class="text-sm font-bold text-gray-900 hover:text-gray-700 transition-colors cursor-pointer select-none">
                                    CANCELAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
