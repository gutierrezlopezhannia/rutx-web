<?php

use function Livewire\Volt\{state, layout};
use App\Models\Customer;
use App\Models\Zone;
use Carbon\Carbon;

layout('layouts.app');

state([
    'search' => '',
    'filtro_estado' => 'todos',
    'filtro_zona' => 'todos',
]);

// Helper para convertir el plazo en texto a días numéricos
$getDiasPlazo = function ($plazo) {
    if (empty($plazo)) return 0;
    if (stripos($plazo, '15') !== false) return 15;
    if (stripos($plazo, '30') !== false) return 30;
    if (stripos($plazo, '8') !== false) return 8;
    return 0; // Contado o default
};

// Cargar catálogo de zonas para los filtros
$getZonas = function () {
    return Zone::all()->toArray();
};

// Lógica de cálculo y filtrado de cartera
$getCartera = function () use ($getDiasPlazo) {
    $hoy = Carbon::now()->startOfDay();

    // Obtener clientes con facturas pendientes (excluyendo Pedidos Sincronizados y saldos en 0)
    $clientes = Customer::with(['invoices' => function ($query) {
        $query->where('movimiento', '!=', 'Pedido Sincronizado')
              ->where('saldo', '>', 0);
    }])->get();

    $data = $clientes->map(function ($customer) use ($getDiasPlazo, $hoy) {
        $saldoTotal = 0.00;
        $saldoVencido = 0.00;
        $facturasVencidas = [];
        $diasPlazo = $getDiasPlazo($customer->plazo);

        foreach ($customer->invoices as $inv) {
            $saldoTotal += (float)$inv->saldo;

            // Determinar fecha de vencimiento
            $fechaVenc = Carbon::parse($inv->fecha)->addDays($diasPlazo)->startOfDay();
            if ($fechaVenc->isBefore($hoy)) {
                $saldoVencido += (float)$inv->saldo;
                $diasAtraso = $hoy->diffInDays($fechaVenc);

                $facturasVencidas[] = [
                    'folio' => $inv->folio,
                    'fecha' => $inv->fecha,
                    'fecha_vencimiento' => $fechaVenc->format('Y-m-d'),
                    'total' => (float)$inv->total,
                    'saldo' => (float)$inv->saldo,
                    'dias_atraso' => $diasAtraso
                ];
            }
        }

        // Si el cliente no tiene facturas activas cargadas pero la base de datos registra saldo, lo usamos de respaldo
        if ($customer->invoices->isEmpty() && (float)$customer->saldo > 0) {
            $saldoTotal = (float)$customer->saldo;
        }

        $limite = (float)$customer->limite;
        $usoPorcentaje = $limite > 0 ? min(100, round(($saldoTotal / $limite) * 100, 1)) : 0;

        // Determinar Estado del Crédito
        $estado = 'Al corriente';
        if ($saldoTotal > $limite) {
            $estado = 'Excedido';
        } elseif ($saldoVencido > 0) {
            $estado = 'Vencido';
        }

        return [
            'id' => $customer->id,
            'clave' => $customer->clave,
            'nombre' => $customer->nombre,
            'rfc' => $customer->rfc,
            'plazo' => $customer->plazo ?: 'Contado',
            'limite' => $limite,
            'saldo_total' => $saldoTotal,
            'saldo_vencido' => $saldoVencido,
            'uso_porcentaje' => $usoPorcentaje,
            'estado' => $estado,
            'zona_id' => $customer->zona_id,
            'facturas_vencidas' => $facturasVencidas
        ];
    });

    // Aplicar filtros reactivos
    $filtrados = collect($data);

    // Filtro de Zona
    if ($this->filtro_zona !== 'todos') {
        $filtrados = $filtrados->where('zona_id', $this->filtro_zona);
    }

    // Filtro de Estado de Crédito
    if ($this->filtro_estado !== 'todos') {
        $filtrados = $filtrados->where('estado', $this->filtro_estado);
    }

    // Filtro por Buscador (Clave, Nombre, RFC)
    if (!empty($this->search)) {
        $q = strtolower(trim($this->search));
        $filtrados = $filtrados->filter(function ($item) use ($q) {
            return str_contains(strtolower($item['nombre']), $q) ||
                   str_contains(strtolower($item['clave']), $q) ||
                   str_contains(strtolower($item['rfc']), $q);
        });
    }

    return $filtrados->values()->toArray();
};

// Calcular KPIs Consolidados globales
$getKpis = function ($carteraFiltrada) {
    $limiteTotal = 0.00;
    $carteraTotal = 0.00;
    $carteraVencida = 0.00;

    $antiguedad = [
        '1_15' => 0.00,
        '16_30' => 0.00,
        '31_60' => 0.00,
        'mas_60' => 0.00,
    ];

    $conteoEstados = [
        'Al corriente' => 0,
        'Vencido' => 0,
        'Excedido' => 0,
    ];

    foreach ($carteraFiltrada as $c) {
        $limiteTotal += $c['limite'];
        $carteraTotal += $c['saldo_total'];
        $carteraVencida += $c['saldo_vencido'];

        // Sumar al conteo de estados
        if (isset($conteoEstados[$c['estado']])) {
            $conteoEstados[$c['estado']]++;
        }

        // Clasificar facturas vencidas por antigüedad de saldos
        foreach ($c['facturas_vencidas'] as $fac) {
            $dias = $fac['dias_atraso'];
            if ($dias <= 15) {
                $antiguedad['1_15'] += $fac['saldo'];
            } elseif ($dias <= 30) {
                $antiguedad['16_30'] += $fac['saldo'];
            } elseif ($dias <= 60) {
                $antiguedad['31_60'] += $fac['saldo'];
            } else {
                $antiguedad['mas_60'] += $fac['saldo'];
            }
        }
    }

    $creditoDisponible = max(0.00, $limiteTotal - $carteraTotal);
    $porcentajeUsoCartera = $limiteTotal > 0 ? round(($carteraTotal / $limiteTotal) * 100, 1) : 0;

    return [
        'limite_total' => $limiteTotal,
        'cartera_total' => $carteraTotal,
        'cartera_vencida' => $carteraVencida,
        'credito_disponible' => $creditoDisponible,
        'uso_porcentaje' => $porcentajeUsoCartera,
        'antiguedad' => $antiguedad,
        'conteo_estados' => $conteoEstados
    ];
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1 select-none">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Cliente</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Cartera y Crédito</span>
            </div>

            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-[#1f2937] tracking-tight">Panel de Control de Cartera y Crédito</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Visión global de saldos deudores, plazos e indicadores de riesgo financiero</p>
                </div>
            </div>

            @php
                $carteraFiltrada = $this->getCartera();
                $kpis = $this->getKpis($carteraFiltrada);
                $zonas = $this->getZonas();
            @endphp

            {{-- KPIs Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                {{-- KPI: Cartera Total --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
                    <div class="p-3 bg-blue-50 text-[#004f7c] rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block">Cartera Total</span>
                        <span class="text-2xl font-bold text-[#003859] block mt-0.5">${{ number_format($kpis['cartera_total'], 2) }}</span>
                        <span class="text-[11px] text-gray-500 block mt-0.5">Saldos pendientes vigentes + vencidos</span>
                    </div>
                </div>

                {{-- KPI: Cartera Vencida --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
                    <div class="p-3 {{ $kpis['cartera_vencida'] > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-500' }} rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block">Cartera Vencida</span>
                        <span class="text-2xl font-bold {{ $kpis['cartera_vencida'] > 0 ? 'text-red-600' : 'text-gray-700' }} block mt-0.5">
                            ${{ number_format($kpis['cartera_vencida'], 2) }}
                        </span>
                        <span class="text-[11px] font-semibold {{ $kpis['cartera_vencida'] > 0 ? 'text-red-500' : 'text-green-600' }} block mt-0.5">
                            @if($kpis['cartera_total'] > 0)
                                {{ round(($kpis['cartera_vencida'] / $kpis['cartera_total']) * 100, 1) }}% de la cartera total
                            @else
                                0% de la cartera total
                            @endif
                        </span>
                    </div>
                </div>

                {{-- KPI: Límite de Crédito Otorgado --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block">Crédito Otorgado</span>
                        <span class="text-2xl font-bold text-gray-800 block mt-0.5">${{ number_format($kpis['limite_total'], 2) }}</span>
                        <span class="text-[11px] text-gray-500 block mt-0.5">Límite global acumulado de clientes</span>
                    </div>
                </div>

                {{-- KPI: Crédito Disponible --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block">Crédito Disponible</span>
                        <span class="text-2xl font-bold text-emerald-600 block mt-0.5">${{ number_format($kpis['credito_disponible'], 2) }}</span>
                        <span class="text-[11px] text-gray-500 block mt-0.5">Límite no utilizado por clientes</span>
                    </div>
                </div>
            </div>

            {{-- Secondary Dash Panels --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {{-- Panel 1: Antigüedad de Saldos Vencidos --}}
                <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm lg:col-span-2">
                    <h3 class="text-sm font-bold text-gray-800 tracking-tight mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Distribución y Antigüedad de la Cartera Vencida
                    </h3>
                    <div class="space-y-4">
                        @php
                            $maxVencido = max(0.01, array_sum($kpis['antiguedad']));
                        @endphp
                        {{-- Rango 1: 1 a 15 días --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-600">1 a 15 días de retraso</span>
                                <span class="text-gray-700">${{ number_format($kpis['antiguedad']['1_15'], 2) }} <span class="text-gray-400 font-normal">({{ round(($kpis['antiguedad']['1_15'] / $maxVencido) * 100, 1) }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-yellow-400 h-full rounded-full transition-all duration-500" style="width: {{ ($kpis['antiguedad']['1_15'] / $maxVencido) * 100 }}%"></div>
                            </div>
                        </div>

                        {{-- Rango 2: 16 a 30 días --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-600">16 a 30 días de retraso</span>
                                <span class="text-gray-700">${{ number_format($kpis['antiguedad']['16_30'], 2) }} <span class="text-gray-400 font-normal">({{ round(($kpis['antiguedad']['16_30'] / $maxVencido) * 100, 1) }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-orange-400 h-full rounded-full transition-all duration-500" style="width: {{ ($kpis['antiguedad']['16_30'] / $maxVencido) * 100 }}%"></div>
                            </div>
                        </div>

                        {{-- Rango 3: 31 a 60 días --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-600">31 a 60 días de retraso</span>
                                <span class="text-gray-700">${{ number_format($kpis['antiguedad']['31_60'], 2) }} <span class="text-gray-400 font-normal">({{ round(($kpis['antiguedad']['31_60'] / $maxVencido) * 100, 1) }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full transition-all duration-500" style="width: {{ ($kpis['antiguedad']['31_60'] / $maxVencido) * 100 }}%"></div>
                            </div>
                        </div>

                        {{-- Rango 4: Más de 60 días --}}
                        <div>
                            <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                <span class="text-gray-600">Más de 60 días (Riesgo Crítico)</span>
                                <span class="text-red-600 font-bold">${{ number_format($kpis['antiguedad']['mas_60'], 2) }} <span class="text-gray-400 font-normal">({{ round(($kpis['antiguedad']['mas_60'] / $maxVencido) * 100, 1) }}%)</span></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-[#990000] h-full rounded-full transition-all duration-500" style="width: {{ ($kpis['antiguedad']['mas_60'] / $maxVencido) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel 2: Estatus de Clientes y Uso Consolidado --}}
                <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 tracking-tight mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                            </svg>
                            Uso y Estado del Crédito Otorgado
                        </h3>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500 font-medium">Uso general del límite global:</span>
                            <span class="text-xs font-bold text-[#004f7c]">{{ $kpis['uso_porcentaje'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 mb-6 overflow-hidden">
                            <div class="bg-[#004f7c] h-full rounded-full transition-all" style="width: {{ $kpis['uso_porcentaje'] }}%"></div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <span class="text-xs font-bold text-gray-500 block mb-3 uppercase tracking-wider">Estado Comercial de Clientes</span>
                        <div class="grid grid-cols-3 gap-2 text-center select-none">
                            <div class="bg-green-50 border border-green-100 rounded-xl p-2">
                                <span class="text-lg font-bold text-green-700 block">{{ $kpis['conteo_estados']['Al corriente'] }}</span>
                                <span class="text-[10px] text-green-600 font-semibold block">Al Corriente</span>
                            </div>
                            <div class="bg-red-50 border border-red-100 rounded-xl p-2">
                                <span class="text-lg font-bold text-red-700 block">{{ $kpis['conteo_estados']['Vencido'] }}</span>
                                <span class="text-[10px] text-red-600 font-semibold block">Con Vencido</span>
                            </div>
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-2">
                                <span class="text-lg font-bold text-amber-700 block">{{ $kpis['conteo_estados']['Excedido'] }}</span>
                                <span class="text-[10px] text-amber-600 font-semibold block">Excedido</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/90 overflow-hidden">

                {{-- Filters Area --}}
                <div class="p-5 border-b border-gray-100 bg-[#fafbfc]">
                    <div class="flex flex-col lg:flex-row gap-3">
                        {{-- Search Input --}}
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                wire:model.live="search"
                                placeholder="Buscar por clave de cliente, razón social o RFC..."
                                class="block w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150 shadow-inner"
                            />
                        </div>

                        {{-- Filter Dropdown: Estado --}}
                        <div class="w-full sm:w-56">
                            <select
                                wire:model.live="filtro_estado"
                                class="block w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150 cursor-pointer shadow-inner"
                            >
                                <option value="todos">Todos los estados</option>
                                <option value="Al corriente">Al corriente</option>
                                <option value="Vencido">Con Saldo Vencido</option>
                                <option value="Excedido">Excedido de Límite</option>
                            </select>
                        </div>

                        {{-- Filter Dropdown: Zona --}}
                        <div class="w-full sm:w-56">
                            <select
                                wire:model.live="filtro_zona"
                                class="block w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition-all duration-150 cursor-pointer shadow-inner"
                            >
                                <option value="todos">Todas las zonas</option>
                                @foreach($zonas as $zona)
                                    <option value="{{ $zona['id'] }}">{{ $zona['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Interactive Table & Alpine.js Accordion --}}
                <div class="overflow-x-auto" x-data="{ activeCliente: null }">
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead class="bg-[#f8fafc] text-xs font-semibold text-gray-500 uppercase tracking-wider select-none">
                            <tr>
                                <th scope="col" class="px-6 py-4">Cliente</th>
                                <th scope="col" class="px-6 py-4 text-center">Plazo</th>
                                <th scope="col" class="px-6 py-4">Uso del Límite de Crédito</th>
                                <th scope="col" class="px-6 py-4 text-right">Límite Autorizado</th>
                                <th scope="col" class="px-6 py-4 text-right">Saldo deudor</th>
                                <th scope="col" class="px-6 py-4 text-right">Saldo Vencido</th>
                                <th scope="col" class="px-6 py-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($carteraFiltrada as $cliente)
                                {{-- Main Row --}}
                                <tr
                                    @click="activeCliente = (activeCliente === '{{ $cliente['id'] }}' ? null : '{{ $cliente['id'] }}')"
                                    class="hover:bg-gray-50/70 transition-colors duration-100 cursor-pointer"
                                    :class="activeCliente === '{{ $cliente['id'] }}' ? 'bg-[#004f7c]/5' : ''"
                                >
                                    {{-- Cliente ID / Name --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-[#004066] text-sm">{{ $cliente['clave'] }}</span>
                                            <span class="font-medium text-gray-900 mt-0.5">{{ $cliente['nombre'] }}</span>
                                            <span class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $cliente['rfc'] }}</span>
                                        </div>
                                    </td>
                                    {{-- Plazo --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap font-medium text-gray-600">
                                        {{ $cliente['plazo'] }}
                                    </td>
                                    {{-- Uso de Límite (Barra) --}}
                                    <td class="px-6 py-4 min-w-[180px]">
                                        <div class="flex flex-col">
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-500 font-semibold">{{ $cliente['uso_porcentaje'] }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300 {{ $cliente['uso_porcentaje'] >= 100 ? 'bg-red-500' : ($cliente['uso_porcentaje'] >= 80 ? 'bg-amber-500' : 'bg-[#004f7c]') }}"
                                                     style="width: {{ $cliente['uso_porcentaje'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Límite --}}
                                    <td class="px-6 py-4 text-right font-medium text-gray-900 whitespace-nowrap">
                                        ${{ number_format($cliente['limite'], 2) }}
                                    </td>
                                    {{-- Saldo --}}
                                    <td class="px-6 py-4 text-right font-semibold text-gray-950 whitespace-nowrap">
                                        ${{ number_format($cliente['saldo_total'], 2) }}
                                    </td>
                                    {{-- Saldo Vencido --}}
                                    <td class="px-6 py-4 text-right font-semibold whitespace-nowrap {{ $cliente['saldo_vencido'] > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                        ${{ number_format($cliente['saldo_vencido'], 2) }}
                                    </td>
                                    {{-- Estado --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($cliente['estado'] === 'Al corriente')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200 select-none">
                                                Al corriente
                                            </span>
                                        @elseif($cliente['estado'] === 'Excedido')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 select-none">
                                                Excedido
                                            </span>
                                        @elseif($cliente['estado'] === 'Vencido')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200 select-none animate-pulse">
                                                Vencido
                                            </span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Sub-Row: Detalle de Facturas Vencidas (Desplegable) --}}
                                <tr x-show="activeCliente === '{{ $cliente['id'] }}'" x-transition style="display: none;">
                                    <td colspan="7" class="px-6 py-4 bg-[#fbfcfd] border-l-4 border-[#004f7c]">
                                        <div>
                                            <div class="flex items-center justify-between pb-3 border-b border-gray-200/60 mb-3">
                                                <h4 class="text-xs font-bold text-[#003859] uppercase tracking-wider">
                                                    Desglose de Facturas Vencidas y Pendientes
                                                </h4>
                                                <span class="text-xs text-gray-400">Haga clic en la fila principal para cerrar</span>
                                            </div>

                                            @if(!empty($cliente['facturas_vencidas']))
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                                    @foreach($cliente['facturas_vencidas'] as $fac)
                                                        <div class="bg-white border border-red-100 rounded-lg p-3 shadow-xs">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <span class="text-xs font-bold text-red-700 uppercase tracking-wide bg-red-50 px-2 py-0.5 rounded">
                                                                    Vencida ({{ $fac['dias_atraso'] }} días)
                                                                </span>
                                                                <span class="font-mono text-xs font-bold text-gray-600">{{ $fac['folio'] }}</span>
                                                            </div>
                                                            <div class="space-y-1 text-xs text-gray-600">
                                                                <div class="flex justify-between">
                                                                    <span>Fecha factura:</span>
                                                                    <span class="font-medium text-gray-800">{{ $fac['fecha'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span>Vencimiento:</span>
                                                                    <span class="font-medium text-gray-850">{{ $fac['fecha_vencimiento'] }}</span>
                                                                </div>
                                                                <div class="flex justify-between border-t border-gray-100 pt-1.5 mt-1">
                                                                    <span>Importe Total:</span>
                                                                    <span class="font-medium text-gray-800">${{ number_format($fac['total'], 2) }}</span>
                                                                </div>
                                                                <div class="flex justify-between font-bold text-red-600">
                                                                    <span>Saldo Pendiente:</span>
                                                                    <span>${{ number_format($fac['saldo'], 2) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-400 py-2 flex items-center gap-2">
                                                    <svg class="w-4.5 h-4.5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    El cliente no tiene facturas con días de retraso activos. Su deuda actual está dentro de los plazos autorizados de crédito.
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="text-sm">No se encontraron registros de cartera que coincidan con la búsqueda o filtros aplicados.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-[#fafbfc] flex items-center justify-between text-xs text-gray-500 select-none">
                    <div>
                        Mostrando <span class="font-semibold text-gray-700">{{ count($carteraFiltrada) }}</span> clientes de cartera
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
