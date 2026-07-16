<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockUtilidad = [
    ['zona' => '1Z - Zona 1', 'ruta' => '3983 - RUTA01', 'cliente' => 'EVEN0001 - CLIENTE EVENTUAL R1 - 1', 'fecha' => '2026-07-15', 'ventas_totales' => 450000.00, 'costo_ventas' => 280000.00, 'gastos_operativos' => 35000.00, 'impuestos' => 72000.00],
    ['zona' => '1Z - Zona 1', 'ruta' => '3983 - RUTA01', 'cliente' => 'EVEN0002 - ABARROTES LA ESPERANZA', 'fecha' => '2026-07-15', 'ventas_totales' => 125000.00, 'costo_ventas' => 82000.00, 'gastos_operativos' => 12000.00, 'impuestos' => 20000.00],
    ['zona' => '2Z - Zona 2', 'ruta' => '3984 - RUTA02', 'cliente' => 'EVEN0003 - MINI SUPER EL SOL', 'fecha' => '2026-07-15', 'ventas_totales' => 89000.00, 'costo_ventas' => 61000.00, 'gastos_operativos' => 8500.00, 'impuestos' => 14240.00],
    ['zona' => '2Z - Zona 2', 'ruta' => '3984 - RUTA02', 'cliente' => 'EVEN0004 - TIENDA LA PRINCIPAL', 'fecha' => '2026-07-14', 'ventas_totales' => 210000.00, 'costo_ventas' => 135000.00, 'gastos_operativos' => 18000.00, 'impuestos' => 33600.00],
    ['zona' => '3Z - Zona 3', 'ruta' => '3985 - RUTA03', 'cliente' => 'EVEN0005 - FARMACIA BENAVIDES', 'fecha' => '2026-07-14', 'ventas_totales' => 320000.00, 'costo_ventas' => 205000.00, 'gastos_operativos' => 28000.00, 'impuestos' => 51200.00],
    ['zona' => '3Z - Zona 3', 'ruta' => '3985 - RUTA03', 'cliente' => 'EVEN0006 - FERRETERIA CENTRAL', 'fecha' => '2026-07-13', 'ventas_totales' => 52000.00, 'costo_ventas' => 38000.00, 'gastos_operativos' => 5500.00, 'impuestos' => 8320.00],
];

$registrosCollection = collect($mockUtilidad)->map(function ($r) {
    $r['utilidad_bruta'] = $r['ventas_totales'] - $r['costo_ventas'];
    $r['utilidad_neta'] = $r['ventas_totales'] - $r['costo_ventas'] - $r['gastos_operativos'] - $r['impuestos'];
    $r['margen'] = $r['ventas_totales'] > 0 ? (($r['ventas_totales'] - $r['costo_ventas']) / $r['ventas_totales']) * 100 : 0;
    return $r;
});

state([
    'registros' => $mockUtilidad,
    'registrosFiltrados' => $registrosCollection->toArray(),
    'filtro_zona' => 'todos',
    'filtro_ruta' => 'todos',
    'filtro_cliente' => 'todos',
    'fecha_inicio' => '2026-07-15',
    'fecha_fin' => '2026-07-15',
]);

$aplicarFiltros = function () {
    $filtradas = collect($this->registros)->map(function ($r) {
        $r['utilidad_bruta'] = $r['ventas_totales'] - $r['costo_ventas'];
        $r['utilidad_neta'] = $r['ventas_totales'] - $r['costo_ventas'] - $r['gastos_operativos'] - $r['impuestos'];
        $r['margen'] = $r['ventas_totales'] > 0 ? (($r['ventas_totales'] - $r['costo_ventas']) / $r['ventas_totales']) * 100 : 0;
        return $r;
    });

    if ($this->filtro_zona !== 'todos') {
        $filtradas = $filtradas->where('zona', $this->filtro_zona);
    }

    if ($this->filtro_ruta !== 'todos') {
        $filtradas = $filtradas->where('ruta', $this->filtro_ruta);
    }

    if ($this->filtro_cliente !== 'todos') {
        $filtradas = $filtradas->where('cliente', $this->filtro_cliente);
    }

    if (!empty($this->fecha_inicio)) {
        try {
            $inicio = \Carbon\Carbon::parse($this->fecha_inicio)->startOfDay();
            $filtradas = $filtradas->filter(function ($item) use ($inicio) {
                return \Carbon\Carbon::parse($item['fecha']) >= $inicio;
            });
        } catch (\Exception $e) {
        }
    }

    if (!empty($this->fecha_fin)) {
        try {
            $fin = \Carbon\Carbon::parse($this->fecha_fin)->endOfDay();
            $filtradas = $filtradas->filter(function ($item) use ($fin) {
                return \Carbon\Carbon::parse($item['fecha']) <= $fin;
            });
        } catch (\Exception $e) {
        }
    }

    $this->registrosFiltrados = $filtradas->values()->toArray();
};

$consultar = function () {
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
                <span class="text-[#003859] font-bold">Utilidad</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">

                {{-- Title --}}
                <h2 class="text-lg font-bold text-[#1f2937] mb-4">Utilidad</h2>

                {{-- Filter Section --}}
                <div class="flex flex-col lg:flex-row lg:items-end gap-4 mb-5">
                    {{-- Zona --}}
                    <div class="flex flex-col w-full lg:w-44">
                        <label class="text-xs text-gray-500 font-medium mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">1Z - Zona 1</option>
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                        </select>
                    </div>

                    {{-- Ruta --}}
                    <div class="flex flex-col w-full lg:w-44">
                        <label class="text-xs text-gray-500 font-medium mb-1">Ruta</label>
                        <select wire:model.live="filtro_ruta" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">3983 - RUTA01</option>
                            <option value="3983 - RUTA01">3983 - RUTA01</option>
                            <option value="3984 - RUTA02">3984 - RUTA02</option>
                        </select>
                    </div>

                    {{-- Cliente --}}
                    <div class="flex flex-col w-full lg:w-64">
                        <label class="text-xs text-gray-500 font-medium mb-1">Cliente</label>
                        <select wire:model.live="filtro_cliente" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">EVEN0001 - CLIENTE EVENTUAL R1 - 1</option>
                            <option value="EVEN0001 - CLIENTE EVENTUAL R1 - 1">EVEN0001 - CLIENTE EVENTUAL R1 - 1</option>
                            <option value="EVEN0002 - ABARROTES LA ESPERANZA">EVEN0002 - ABARROTES LA ESPERANZA</option>
                            <option value="EVEN0003 - MINI SUPER EL SOL">EVEN0003 - MINI SUPER EL SOL</option>
                            <option value="EVEN0004 - TIENDA LA PRINCIPAL">EVEN0004 - TIENDA LA PRINCIPAL</option>
                            <option value="EVEN0005 - FERRETERIA CENTRAL">EVEN0005 - FERRETERIA CENTRAL</option>
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-full lg:w-40">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha Inicial</label>
                        <div class="flex items-center border-0 border-b border-gray-300 rounded-none px-0 py-1">
                            <input type="date" wire:model.live="fecha_inicio" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full lg:w-40">
                        <label class="text-xs text-gray-500 font-medium mb-1">Fecha final</label>
                        <div class="flex items-center border-0 border-b border-gray-300 rounded-none px-0 py-1">
                            <input type="date" wire:model.live="fecha_fin" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm" />
                        </div>
                    </div>
                </div>

                {{-- Consultar Button --}}
                <div class="mb-5">
                    <button wire:click="consultar" class="px-6 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded-lg text-sm font-semibold transition duration-150 cursor-pointer">
                        Consultar
                    </button>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Ruta</th>
                                <th class="px-6 py-4">Vendedor</th>
                                <th class="px-6 py-4">Cliente</th>
                                <th class="px-6 py-4 text-right">Cant. Artículos</th>
                                <th class="px-6 py-4 text-right">Venta ($)</th>
                                <th class="px-6 py-4 text-right">Costo ($)</th>
                                <th class="px-6 py-4 text-right">Utilidad ($)</th>
                                <th class="px-6 py-4 text-right">Margen (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white text-xs sm:text-sm text-gray-700">
                            @forelse($this->registrosFiltrados as $registro)
                            <tr class="hover:bg-gray-50/30 transition duration-150">
                                <td class="px-6 py-4 text-[#003859] font-medium">{{ $registro['ruta'] }}</td>
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ $registro['vendedor'] ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-900 font-medium">{{ $registro['cliente'] }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-700">{{ number_format($registro['ventas_totales'], 0) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-[#1f2937]">${{ number_format($registro['ventas_totales'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-[#1f2937]">${{ number_format($registro['costo_ventas'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-[#1f2937]">${{ number_format($registro['utilidad_bruta'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold">
                                    @if($registro['margen'] > 0)
                                        <span class="text-emerald-600">{{ number_format($registro['margen'], 1) }}%</span>
                                    @elseif($registro['margen'] < 0)
                                        <span class="text-red-500">{{ number_format($registro['margen'], 1) }}%</span>
                                    @else
                                        <span class="text-orange-500">{{ number_format($registro['margen'], 1) }}%</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    No se encontraron registros para el filtro seleccionado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
