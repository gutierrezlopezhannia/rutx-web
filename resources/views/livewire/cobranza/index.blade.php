<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

// Inicialización inicial desde base de datos
$iniciales = \App\Models\Invoice::where('customer_id', '999001 - CLIENTE PRUEBA 01')
    ->where('movimiento', '!=', 'Pedido Sincronizado')
    ->orderBy('fecha')
    ->get()
    ->map(fn($inv) => [
        'folio' => $inv->folio,
        'fecha' => $inv->fecha,
        'total' => $inv->total,
        'abono' => $inv->abono,
        'saldo' => $inv->saldo,
        'cobranza' => 0.00,
        'checked' => false
    ])
    ->toArray();

state([
    // Filtros superiores
    'filtro_zona' => '99-PRUEBA',
    'filtro_vendedor' => 'todos', // 'todos' es el placeholder inicial que se muestra en rojo
    'filtro_cliente' => '999001 - CLIENTE PRUEBA 01',
    
    // Campos del Formulario
    'tipo_cobranza' => 'Liquidar por antigüedad',
    'tipo_pago' => 'Efectivo',
    'monto' => '',
    
    // Pedidos de crédito asociados
    'pedidos' => $iniciales,
    'selectAll' => false,
    
    // Mensajes de respuesta reactivos
    'mensaje_exito' => '',
    'mensaje_error' => '',
]);

// Lógica de actualización al cambiar de cliente
$actualizarCliente = function() {
    $cliente = \App\Models\Customer::find($this->filtro_cliente);
    if ($cliente) {
        $this->filtro_zona = $cliente->zona_id;
        $this->monto = '';
        $this->selectAll = false;
        
        $this->pedidos = \App\Models\Invoice::where('customer_id', $this->filtro_cliente)
            ->where('movimiento', '!=', 'Pedido Sincronizado')
            ->orderBy('fecha')
            ->get()
            ->map(fn($inv) => [
                'folio' => $inv->folio,
                'fecha' => $inv->fecha,
                'total' => $inv->total,
                'abono' => $inv->abono,
                'saldo' => $inv->saldo,
                'cobranza' => 0.00,
                'checked' => false
            ])
            ->toArray();
    } else {
        $this->pedidos = [];
    }
};

$updatedFiltroCliente = function() {
    $this->mensaje_exito = '';
    $this->mensaje_error = '';
    $this->actualizarCliente();
};

$updatedFiltroZona = function() {
    $this->mensaje_exito = '';
    $this->mensaje_error = '';
    // Al cambiar la zona, buscamos el primer cliente de esa zona y lo cargamos
    $firstCli = \App\Models\Customer::where('zona_id', $this->filtro_zona)->first();
    if ($firstCli) {
        $this->filtro_cliente = $firstCli->id;
        $this->actualizarCliente();
    }
};

// Seleccionar todos los pedidos de crédito
$updatedSelectAll = function() {
    $val = $this->selectAll;
    $pedidos = $this->pedidos;
    foreach ($pedidos as &$p) {
        $p['checked'] = $val;
        if ($val) {
            $p['cobranza'] = $p['saldo'];
        } else {
            $p['cobranza'] = 0.00;
        }
    }
    $this->pedidos = $pedidos;
    $this->calcularMontoTotal();
};

// Alternar la selección de un pedido individual
$togglePedido = function($index) {
    if (isset($this->pedidos[$index])) {
        $pedidos = $this->pedidos;
        $pedidos[$index]['checked'] = !$pedidos[$index]['checked'];
        if ($pedidos[$index]['checked']) {
            $pedidos[$index]['cobranza'] = $pedidos[$index]['saldo'];
        } else {
            $pedidos[$index]['cobranza'] = 0.00;
        }
        $this->pedidos = $pedidos;
        $this->calcularMontoTotal();
    }
};

// Actualizar cobranza ingresando monto manual en la fila
$actualizarCobranzaIndividual = function($index, $val) {
    if (isset($this->pedidos[$index])) {
        $pedidos = $this->pedidos;
        $cobranzaVal = floatval($val);
        $pedidos[$index]['cobranza'] = $cobranzaVal;
        if ($cobranzaVal > 0) {
            $pedidos[$index]['checked'] = true;
        } else {
            $pedidos[$index]['checked'] = false;
        }
        $this->pedidos = $pedidos;
        $this->calcularMontoTotal();
    }
};

// Calcular la suma de los cobros activos
$calcularMontoTotal = function() {
    $total = 0;
    foreach ($this->pedidos as $p) {
        if ($p['checked']) {
            $total += $p['cobranza'];
        }
    }
    $this->monto = $total > 0 ? $total : '';
};

// Lógica de distribución al escribir en el campo de Monto total
$updatedMonto = function() {
    $montoTotal = floatval($this->monto);
    if ($this->tipo_cobranza === 'Liquidar por antigüedad') {
        $resto = $montoTotal;
        $pedidos = $this->pedidos;
        foreach ($pedidos as &$p) {
            if ($resto >= $p['saldo']) {
                $p['checked'] = true;
                $p['cobranza'] = $p['saldo'];
                $resto -= $p['saldo'];
            } elseif ($resto > 0) {
                $p['checked'] = true;
                $p['cobranza'] = $resto;
                $resto = 0;
            } else {
                $p['checked'] = false;
                $p['cobranza'] = 0.00;
            }
        }
        $this->pedidos = $pedidos;
    }
};

// Restaurar filtros predeterminados
$refrescarFiltros = function() {
    $this->filtro_zona = '99-PRUEBA';
    $this->filtro_vendedor = 'todos';
    $this->filtro_cliente = '999001 - CLIENTE PRUEBA 01';
    $this->actualizarCliente();
    $this->mensaje_exito = 'Filtros y datos restaurados.';
    $this->mensaje_error = '';
};

// Registrar cobro real en Base de Datos SQLite
$enviarCobranza = function() {
    if (empty($this->monto) || floatval($this->monto) <= 0) {
        return;
    }
    
    if ($this->filtro_vendedor === 'todos') {
        $this->mensaje_error = 'Debe seleccionar un Vendedor para procesar la cobranza.';
        $this->mensaje_exito = '';
        return;
    }
    
    $montoCobrado = floatval($this->monto);
    $cliente = \App\Models\Customer::find($this->filtro_cliente);
    if (!$cliente) {
        return;
    }
    
    \Illuminate\Support\Facades\DB::transaction(function() use ($cliente, $montoCobrado) {
        $resto = $montoCobrado;
        foreach ($this->pedidos as $p) {
            if ($p['checked'] || $p['cobranza'] > 0) {
                $deduccion = min($resto, $p['cobranza']);
                
                $inv = \App\Models\Invoice::where('folio', $p['folio'])->first();
                if ($inv) {
                    $inv->saldo = max(0.00, $inv->saldo - $deduccion);
                    $inv->abono = $inv->abono + $deduccion;
                    $inv->save();
                }
                
                $resto -= $deduccion;
            }
        }
        
        $cliente->saldo = max(0.00, $cliente->saldo - $montoCobrado);
        $cliente->save();
    });
    
    $this->mensaje_exito = "¡Cobranza enviada con éxito! Se aplicó un cobro de $" . number_format($montoCobrado, 2) . " al cliente " . $cliente->nombre . " por medio de " . $this->tipo_pago . ".";
    $this->mensaje_error = '';
    
    $this->monto = '';
    $this->selectAll = false;
    $this->actualizarCliente();
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-700 transition duration-150">Cpanel</a>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500 font-medium">Cobranza</span>
            </div>

            {{-- Alertas de Sesión --}}
            @if ($mensaje_exito)
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg flex items-center justify-between shadow-sm">
                    <span>{{ $mensaje_exito }}</span>
                    <button class="text-green-500 hover:text-green-700 font-bold focus:outline-none" wire:click="$set('mensaje_exito', '')">&times;</button>
                </div>
            @endif

            @if ($mensaje_error)
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg flex items-center justify-between shadow-sm">
                    <span>{{ $mensaje_error }}</span>
                    <button class="text-red-500 hover:text-red-700 font-bold focus:outline-none" wire:click="$set('mensaje_error', '')">&times;</button>
                </div>
            @endif

            {{-- Main Container Card --}}
            <div class="bg-white rounded-sm shadow-sm border border-gray-200 p-6">

                {{-- Título --}}
                <h2 class="text-lg font-bold text-[#1f2937] mb-6">Cobranza</h2>

                {{-- Sección de Filtros (Estilo Underline de alta fidelidad) --}}
                <div class="flex flex-col lg:flex-row lg:items-end gap-6 mb-8 w-full border-b border-gray-100 pb-5">
                    
                    {{-- Filtro Zona --}}
                    <div class="flex flex-col w-full lg:w-[25%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-medium cursor-pointer w-full">
                            @foreach(\App\Models\Zone::pluck('id')->sort() as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filtro Vendedor (Rojo si está en "todos") --}}
                    <div class="flex flex-col w-full lg:w-[35%]">
                        <label class="text-[11px] font-medium mb-1 {{ $filtro_vendedor === 'todos' ? 'text-red-500' : 'text-gray-400' }}">Vendedor</label>
                        <select wire:model.live="filtro_vendedor" class="border-0 border-b rounded-none px-0 py-1 text-sm focus:outline-none focus:ring-0 bg-transparent font-medium cursor-pointer w-full {{ $filtro_vendedor === 'todos' ? 'border-red-500 text-red-500 font-semibold' : 'border-gray-300 text-gray-700' }}">
                            <option value="todos">Vendedor</option>
                            <option value="695 - VENDEDOR">695 - VENDEDOR</option>
                            <option value="3345 - VDOS">3345 - VDOS</option>
                            <option value="7621 - RUTA_ALE">7621 - RUTA_ALE</option>
                            <option value="7853 - MIGUEL ANGEL">7853 - MIGUEL ANGEL</option>
                            <option value="8364 - RUTA ZONA SUR">8364 - RUTA ZONA SUR</option>
                            <option value="9448 - URIEL">9448 - URIEL</option>
                        </select>
                    </div>

                    {{-- Filtro Cliente --}}
                    <div class="flex flex-col w-full lg:w-[35%]">
                        <label class="text-[11px] text-gray-400 font-medium mb-1">Cliente</label>
                        <select wire:model.live="filtro_cliente" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-medium cursor-pointer w-full">
                            @foreach(\App\Models\Customer::where('zona_id', $filtro_zona)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Botón Restaurar / Refrescar --}}
                    <div class="flex justify-start lg:justify-end py-1">
                        <button wire:click="refrescarFiltros" class="text-gray-500 hover:text-[#004f7c] p-1 rounded transition duration-150 focus:outline-none cursor-pointer" title="Restaurar Filtros">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5" />
                            </svg>
                        </button>
                    </div>

                </div>

                {{-- Cuerpo del Módulo: Dos Columnas (Formulario vs Ficha Cliente) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-6 pb-6 border-b border-gray-100">
                    
                    {{-- Formulario de Cobranza (Izquierda, Inline) --}}
                    <div class="space-y-6 self-start">
                        
                        {{-- Tipo de Cobranza --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-xs text-gray-500 font-medium whitespace-nowrap">Tipo de Cobranza:</span>
                            <svg class="text-gray-400 hover:text-[#004f7c] cursor-pointer flex-shrink-0" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Selecciona la forma de aplicar el pago a las facturas pendientes">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <select wire:model.live="tipo_cobranza" class="border-0 border-b border-gray-300 rounded-none px-0 py-0.5 text-xs font-semibold focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 cursor-pointer w-auto min-w-[170px] ml-1">
                                <option value="Liquidar por antigüedad">Liquidar por antigüedad</option>
                                <option value="Registro manual">Registro manual</option>
                            </select>
                        </div>

                        {{-- Tipo de Pago --}}
                        <div class="flex items-center gap-1.5">
                            <label class="text-xs text-gray-500 font-medium whitespace-nowrap">Tipo de pago:</label>
                            <select wire:model.live="tipo_pago" class="border-0 border-b border-gray-300 rounded-none px-0 py-0.5 text-xs font-semibold focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 cursor-pointer w-auto min-w-[100px] ml-1">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta bancaria">Tarjeta bancaria</option>
                            </select>
                        </div>

                        {{-- Monto e Botón Enviar --}}
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <label class="text-xs text-gray-500 font-medium whitespace-nowrap">Monto:</label>
                            <div class="flex items-center border-0 border-b border-gray-300 py-0.5 ml-1">
                                <span class="text-xs text-gray-600 mr-1 font-mono">$</span>
                                <input type="number" step="0.01" wire:model.live="monto" placeholder="0.00" class="border-none outline-none p-0 w-28 focus:ring-0 bg-transparent text-gray-700 font-mono text-xs" />
                            </div>
                            <button wire:click="enviarCobranza" class="ml-4 px-4 py-2 text-xs font-bold rounded shadow-sm transition duration-150 cursor-pointer {{ (empty($monto) || floatval($monto) <= 0) ? 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 hover:shadow' }}">
                                Enviar Cobranza
                            </button>
                        </div>

                    </div>

                    {{-- Ficha del Cliente (Derecha, bien rellenada desde base de datos, estilo vertical) --}}
                    @php
                        $clienteActive = \App\Models\Customer::find($filtro_cliente);
                    @endphp
                    @if($clienteActive)
                        <div class="space-y-4 lg:border-l lg:border-gray-200 lg:pl-10 text-xs text-gray-700">
                            <h3 class="text-sm font-bold text-gray-900 mb-2">Cliente</h3>
                            
                            <div class="space-y-1.5">
                                <div><span class="font-semibold text-gray-800">Clave:</span> <span class="font-mono text-gray-600">{{ $clienteActive->clave }}</span></div>
                                <div><span class="font-semibold text-gray-800">Nombre:</span> <span class="text-gray-900">{{ $clienteActive->nombre }}</span></div>
                                <div><span class="font-semibold text-gray-800">Dirección:</span> <span class="text-gray-600">{{ $clienteActive->direccion ?: '—' }}</span></div>
                                <div><span class="font-semibold text-gray-800">RFC:</span> <span class="font-mono text-gray-600">{{ $clienteActive->rfc ?: '—' }}</span></div>
                                <div><span class="font-semibold text-gray-800">Teléfono:</span> <span class="text-gray-600">{{ $clienteActive->telefono ?: '—' }}</span></div>
                                <div><span class="font-semibold text-gray-800">Plazo Crédito:</span> <span class="text-gray-600">{{ $clienteActive->plazo ?: '—' }}</span></div>
                                <div><span class="font-semibold text-gray-800">Límite de Crédito:</span> <span class="font-mono text-gray-600">${{ number_format($clienteActive->limite, 2) }}</span></div>
                                <div><span class="font-semibold text-gray-800">Saldo:</span> <span class="font-mono font-bold text-red-600">${{ number_format($clienteActive->saldo, 2) }}</span></div>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Sección Inferior: Pedidos de Crédito --}}
                @if(!empty($pedidos))
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-[#1f2937] mb-4">Pedidos de crédito</h3>

                        <div class="overflow-x-auto border border-gray-200 rounded-sm">
                            <table class="min-w-full text-left text-xs text-gray-600 border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/75 text-gray-500 font-bold border-b border-gray-200">
                                        <th class="px-4 py-3 w-12 text-center">
                                            <input type="checkbox" wire:model.live="selectAll" class="rounded text-[#004f7c] focus:ring-[#004f7c] border-gray-300 w-3.5 h-3.5 cursor-pointer" />
                                        </th>
                                        <th class="px-4 py-3 font-semibold">Folio</th>
                                        <th class="px-4 py-3 font-semibold">Fecha</th>
                                        <th class="px-4 py-3 text-right font-semibold">Total de la Venta</th>
                                        <th class="px-4 py-3 text-right font-semibold">Abono</th>
                                        <th class="px-4 py-3 text-right font-semibold">Saldo</th>
                                        <th class="px-4 py-3 text-right font-semibold">Cobranza</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($pedidos as $index => $pedido)
                                        <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                            {{-- Checkbox --}}
                                            <td class="px-4 py-3.5 text-center">
                                                <input type="checkbox" 
                                                       wire:click="togglePedido({{ $index }})" 
                                                       @if($pedido['checked']) checked @endif
                                                       class="rounded text-[#004f7c] focus:ring-[#004f7c] border-gray-300 w-3.5 h-3.5 cursor-pointer" />
                                            </td>
                                            {{-- Folio --}}
                                            <td class="px-4 py-3.5 font-mono font-medium text-gray-900">{{ $pedido['folio'] }}</td>
                                            {{-- Fecha --}}
                                            <td class="px-4 py-3.5 text-gray-500 font-mono">
                                                {{ \Carbon\Carbon::parse($pedido['fecha'])->format('d/m/Y') }}
                                            </td>
                                            {{-- Total Venta --}}
                                            <td class="px-4 py-3.5 text-right font-mono font-medium">${{ number_format($pedido['total'], 2) }}</td>
                                            {{-- Abono --}}
                                            <td class="px-4 py-3.5 text-right font-mono font-medium text-green-600">${{ number_format($pedido['abono'], 2) }}</td>
                                            {{-- Saldo --}}
                                            <td class="px-4 py-3.5 text-right font-mono font-bold text-gray-800">${{ number_format($pedido['saldo'], 2) }}</td>
                                            {{-- Cobranza Input --}}
                                            <td class="px-4 py-2 w-36 text-right">
                                                <div class="flex items-center justify-end border-0 border-b border-gray-300 py-0.5 max-w-[120px] ml-auto">
                                                    <span class="text-[10px] text-gray-400 mr-1 font-mono">$</span>
                                                    <input type="number" step="0.01" max="{{ $pedido['saldo'] }}"
                                                           value="{{ $pedido['cobranza'] > 0 ? $pedido['cobranza'] : '' }}"
                                                           wire:input="actualizarCobranzaIndividual({{ $index }}, $event.target.value)" 
                                                           placeholder="0.00" 
                                                           class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-right text-gray-700 font-mono text-xs" />
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-gray-400 font-medium">
                                                Sin Registros
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación Ficticia --}}
                        <div class="flex justify-end mt-4 text-[11px] text-gray-500 font-medium font-mono items-center gap-2">
                            <span>
                                @if(count($pedidos) > 0)
                                    1-{{ count($pedidos) }} of {{ count($pedidos) }}
                                @else
                                    0-0 of 0
                                @endif
                            </span>
                            <div class="flex gap-1.5 ml-2">
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 cursor-not-allowed" disabled>
                                    &lt;
                                </button>
                                <button class="p-1 hover:bg-gray-100 rounded text-gray-400 cursor-not-allowed" disabled>
                                    &gt;
                                </button>
                            </div>
                        </div>

                    </div>
                @endif

            </div>

            {{-- Footer de Copyright --}}
            <div class="mt-8 text-center text-xs text-gray-400 font-medium">
                Copyright © JB VEMOBILE SA DE CV 2026.
            </div>

        </div>
    </div>
</div>
