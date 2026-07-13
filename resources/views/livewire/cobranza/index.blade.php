<?php

use function Livewire\Volt\{state, mount};
use Livewire\Volt\Layout;

layout('layouts.app');

state([
    'facturas' => [],
    'facturasFiltradas' => [],
    'filtro_estado' => 'todos',
    'total_cobrado' => 0,
    'total_pendiente' => 0,
    'total_general' => 0,
    'total_facturas' => 0,
]);

$mount = function () {
    $this->facturas = [
        ['id' => 'FAC-001', 'cliente' => 'Abarrotes Garcia SA', 'fecha' => 'Hoy, 10:42 AM', 'monto_total' => 56200.00, 'monto_pagado' => 56200.00, 'estado' => 'Completado', 'metodo_pago' => 'Efectivo', 'vendedor' => 'Carlos Ruiz'],
        ['id' => 'FAC-002', 'cliente' => 'Minimarket Luna', 'fecha' => 'Hoy, 09:15 AM', 'monto_total' => 8750.50, 'monto_pagado' => 8750.50, 'estado' => 'Procesando', 'metodo_pago' => 'Transferencia', 'vendedor' => 'Ana Lopez'],
        ['id' => 'FAC-003', 'cliente' => 'La Esquina del Sabor', 'fecha' => 'Ayer, 16:30 PM', 'monto_total' => 23100.00, 'monto_pagado' => 0.00, 'estado' => 'Pendiente', 'metodo_pago' => 'Credito', 'vendedor' => 'Miguel Torres'],
        ['id' => 'FAC-004', 'cliente' => 'Distribuidora Norte', 'fecha' => 'Ayer, 11:20 AM', 'monto_total' => 45800.00, 'monto_pagado' => 0.00, 'estado' => 'Cancelado', 'metodo_pago' => 'Credito', 'vendedor' => 'Laura Garcia'],
        ['id' => 'FAC-005', 'cliente' => 'Bodega Central', 'fecha' => '18 Jun, 14:00 PM', 'monto_total' => 12450.75, 'monto_pagado' => 12450.75, 'estado' => 'Completado', 'metodo_pago' => 'Efectivo', 'vendedor' => 'Pedro Sanchez'],
        ['id' => 'FAC-006', 'cliente' => 'Super Todo', 'fecha' => '18 Jun, 09:30 AM', 'monto_total' => 67200.00, 'monto_pagado' => 40000.00, 'estado' => 'Procesando', 'metodo_pago' => 'Transferencia', 'vendedor' => 'Carlos Ruiz'],
        ['id' => 'FAC-007', 'cliente' => 'Abarrotes El Greco', 'fecha' => '17 Jun, 15:45 PM', 'monto_total' => 9800.00, 'monto_pagado' => 0.00, 'estado' => 'Pendiente', 'metodo_pago' => 'Credito', 'vendedor' => 'Ana Lopez'],
        ['id' => 'FAC-008', 'cliente' => 'Carniceria Don Jose', 'fecha' => '17 Jun, 10:00 AM', 'monto_total' => 34500.00, 'monto_pagado' => 34500.00, 'estado' => 'Completado', 'metodo_pago' => 'Efectivo', 'vendedor' => 'Miguel Torres'],
        ['id' => 'FAC-009', 'cliente' => 'Fruteria La Fresita', 'fecha' => '16 Jun, 12:15 PM', 'monto_total' => 18900.25, 'monto_pagado' => 10000.00, 'estado' => 'Procesando', 'metodo_pago' => 'Cheque', 'vendedor' => 'Laura Garcia'],
        ['id' => 'FAC-010', 'cliente' => 'Papeleria Imperial', 'fecha' => '16 Jun, 08:45 AM', 'monto_total' => 5600.00, 'monto_pagado' => 5600.00, 'estado' => 'Completado', 'metodo_pago' => 'Transferencia', 'vendedor' => 'Pedro Sanchez'],
    ];

    $this->aplicarFiltros();
};

$aplicarFiltros = function () {
    $filtradas = collect($this->facturas);

    if ($this->filtro_estado !== 'todos') {
        $filtradas = $filtradas->where('estado', $this->filtro_estado);
    }

    $this->facturasFiltradas = $filtradas->values()->toArray();

    $this->total_cobrado = collect($this->facturas)->where('estado', 'Completado')->sum('monto_pagado');
    $this->total_pendiente = collect($this->facturas)
        ->filter(fn($f) => !in_array($f['estado'], ['Completado', 'Cancelado']))
        ->sum(fn($f) => $f['monto_total'] - $f['monto_pagado']);
    $this->total_general = collect($this->facturas)->sum('monto_total');
    $this->total_facturas = count($this->facturas);
};

$filtrarPorEstado = function ($estado) {
    $this->filtro_estado = $estado;
    $this->aplicarFiltros();
};
?>

<x-app-layout>
    <x-slot name="header">
    </x-slot>

    <div class="py-0">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb + Action Bar --}}
            <div class="flex items-center justify-between py-4 px-1">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <span class="font-medium text-gray-700">Ventas</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="font-semibold text-[#1f2937]">Dashboard de Analisis</span>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Date filter toggle --}}
                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden text-sm">
                        <button class="px-4 py-2 bg-[#003859] text-white font-medium flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Ultimos 30 dias
                        </button>
                        <button class="px-4 py-2 text-gray-600 hover:bg-gray-50 font-medium">
                            Este Mes
                        </button>
                    </div>

                    {{-- Filtros --}}
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtros
                    </button>

                    {{-- Exportar --}}
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar
                    </button>

                    {{-- Sincronizar ERP --}}
                    <button class="px-4 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded-lg text-sm font-medium flex items-center gap-2 transition duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Sincronizar ERP
                    </button>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
                {{-- Total Cobrado --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-teal-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Cobrado</p>
                        <p class="text-xl font-bold text-[#1f2937]">${{ number_format($total_cobrado, 2) }}</p>
                    </div>
                </div>

                {{-- Total Pendiente --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Pendiente</p>
                        <div class="flex items-center gap-2">
                            <p class="text-xl font-bold text-[#1f2937]">${{ number_format($total_pendiente, 2) }}</p>
                            <span class="text-xs font-semibold text-red-500 flex items-center">
                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                12.5%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Facturas Activas --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Facturas Activas</p>
                        <div class="flex items-center gap-2">
                            <p class="text-xl font-bold text-[#1f2937]">{{ $total_facturas }}</p>
                            <span class="text-xs font-semibold text-green-500 flex items-center">
                                <svg class="w-3 h-3 mr-0.5 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                8.3%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tasa de Cobro --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tasa de Cobro</p>
                        <div class="flex items-center gap-2">
                            <p class="text-xl font-bold text-[#1f2937]">{{ $total_general > 0 ? number_format(($total_cobrado / $total_general) * 100, 1) : '0.0' }}%</p>
                            <span class="text-xs font-semibold text-green-500 flex items-center">
                                <svg class="w-3 h-3 mr-0.5 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                2.1%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Cobranza --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Table Header --}}
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-[#1f2937]">Cobranza Reciente</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID Factura</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Monto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Accion</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($facturasFiltradas as $factura)
                            <tr class="hover:bg-gray-50/50 transition duration-150 {{ $loop->even ? 'bg-gray-50/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-[#1f2937]">
                                    {{ $factura['id'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $factura['cliente'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#1f2937]">
                                    ${{ number_format($factura['monto_total'], 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($factura['estado'] === 'Completado')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $factura['estado'] }}
                                        </span>
                                    @elseif($factura['estado'] === 'Procesando')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $factura['estado'] }}
                                        </span>
                                    @elseif($factura['estado'] === 'Pendiente')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $factura['estado'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            {{ $factura['estado'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $factura['fecha'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="#" class="font-semibold text-[#003859] hover:text-[#004f7c] hover:underline transition duration-150">
                                        Ver detalles
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No hay facturas para el filtro seleccionado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- --}}
        </div>
    </div>
</x-app-layout>
