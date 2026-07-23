<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state([
    'filtro_zona'  => '1Z - Zona 1',
    'filtro_ruta'  => '4682 - RUTA02',
    'fecha'        => date('Y-m-d'),
    
    'data_loaded' => false,

    'stats' => [
        'ruta_nombre' => '(4682) RUTA02',
        'porcentaje' => 22,
        'sincronizados' => 23,
        'visitados' => 5,
        'no_visitados' => 18,
        'no_visitados_pct' => 78.26,
        'ventas' => 4,
        'causas_no_venta' => 1,
        'preventas' => 1,
        'entregas' => 0,
        'clientes_nuevos' => 0,
        'total_cobrado' => 0,
        'total_vendido' => 15765,
        'fim' => '',
        'ffm' => '',
        'pv' => '23-01 10:53',
        'uv' => '23-01 11:21'
    ]
]);

$consultar = function () {
    // In a real scenario, we would fetch data based on filters.
    // For now, just set data_loaded to true to show the dashboard.
    $this->data_loaded = true;
};

?>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endpush
@endonce

<div>
    <div class="py-4" x-data="visorDashboard()" x-init="initChart()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-4 px-1">
                <span>Cpanel</span>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Visor</span>
            </div>

            {{-- Main Filters Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 mb-6">
                {{-- Title --}}
                <div class="flex items-center gap-2 mb-6">
                    <h2 class="text-lg font-bold text-[#1f2937]">Rentabilidad por Ruta</h2>
                    <svg class="w-4 h-4 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 24 24" title="Rentabilidad de la ruta seleccionada">
                        <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>

                {{-- Filtros --}}
                <div class="flex flex-wrap items-end gap-6">
                    {{-- Zona --}}
                    <div class="flex flex-col w-48">
                        <label class="text-xs text-gray-400 mb-1">Zona</label>
                        <select wire:model="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-medium cursor-pointer">
                            <option value="">Seleccione Zona</option>
                            <option value="1Z - Zona 1">1Z - Zona 1</option>
                            <option value="2Z - Zona 2">2Z - Zona 2</option>
                        </select>
                    </div>

                    {{-- Ruta --}}
                    <div class="flex flex-col w-48">
                        <label class="text-xs text-gray-400 mb-1">Ruta</label>
                        <select wire:model="filtro_ruta" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] bg-transparent text-gray-700 font-medium cursor-pointer">
                            <option value="">Seleccione Ruta</option>
                            <option value="4682 - RUTA02">4682 - RUTA02</option>
                        </select>
                    </div>

                    {{-- Fecha --}}
                    <div class="flex flex-col w-40">
                        <label class="text-xs text-gray-400 mb-1">Fecha</label>
                        <div class="flex items-center border-0 border-b border-gray-300 py-1">
                            <input type="date" wire:model="fecha" class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 font-medium text-sm" />
                        </div>
                    </div>

                    {{-- Botón Consultar --}}
                    <button wire:click="consultar" class="px-6 py-2 bg-[#003859] hover:bg-[#002d48] text-white rounded text-sm font-semibold transition duration-150 cursor-pointer mb-0.5">
                        Consultar
                    </button>
                </div>
            </div>

            {{-- Dashboard Card --}}
            @if($data_loaded)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5 w-full md:w-80 relative">
                {{-- Title --}}
                <h3 class="font-bold text-gray-800 text-sm mb-4">{{ $stats['ruta_nombre'] }}</h3>

                {{-- Gauge Chart --}}
                <div class="relative w-full h-32 flex justify-center mb-6">
                    <canvas id="gaugeChart" wire:ignore></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-end pb-2 pointer-events-none">
                        <span class="text-2xl font-bold text-gray-800">{{ $stats['porcentaje'] }}%</span>
                    </div>
                </div>

                {{-- Stats List --}}
                <div class="text-xs text-gray-600 space-y-0 divide-y divide-gray-100">
                    <div class="flex justify-between py-2">
                        <span>Sincronizados</span>
                        <span class="font-medium">{{ $stats['sincronizados'] }}</span>
                    </div>
                    
                    {{-- Visitados (Expandable) --}}
                    <div class="flex flex-col">
                        <div @click="activeAccordion = activeAccordion === 'visitados' ? null : 'visitados'" class="flex justify-between py-2 items-center cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': activeAccordion === 'visitados'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                <span>Visitados</span>
                            </div>
                            <span class="font-medium">{{ $stats['visitados'] }}</span>
                        </div>
                        <div x-show="activeAccordion === 'visitados'" x-transition class="pl-4 pb-2 space-y-1">
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Agendados</span>
                                <span class="text-green-600 font-medium bg-green-50 px-1 rounded">5 (22%)</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-500">
                                <span>No Agendados</span>
                                <span class="text-red-500 font-medium bg-red-50 px-1 rounded">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between py-2">
                        <span>No Visitados Ag</span>
                        <span class="font-medium">{{ $stats['no_visitados'] }} ({{ $stats['no_visitados_pct'] }} %)</span>
                    </div>

                    {{-- Ventas (Expandable) --}}
                    <div class="flex flex-col">
                        <div @click="activeAccordion = activeAccordion === 'ventas' ? null : 'ventas'" class="flex justify-between py-2 items-center cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': activeAccordion === 'ventas'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                <span>Ventas</span>
                            </div>
                            <span class="font-medium">{{ $stats['ventas'] }}</span>
                        </div>
                        <div x-show="activeAccordion === 'ventas'" x-transition class="pl-4 pb-2 space-y-1 text-gray-500">
                            <div class="flex justify-between"><span>Efectivo</span><span>4</span></div>
                        </div>
                    </div>

                    {{-- Causas No Venta (Expandable) --}}
                    <div class="flex flex-col">
                        <div @click="activeAccordion = activeAccordion === 'causas' ? null : 'causas'" class="flex justify-between py-2 items-center cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': activeAccordion === 'causas'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                <span>Causas No Venta</span>
                            </div>
                            <span class="font-medium">{{ $stats['causas_no_venta'] }}</span>
                        </div>
                        <div x-show="activeAccordion === 'causas'" x-transition class="pl-4 pb-2 space-y-1 text-gray-500">
                            <div class="flex justify-between"><span>Local cerrado</span><span>1</span></div>
                        </div>
                    </div>

                    {{-- Preventas (Expandable) --}}
                    <div class="flex flex-col">
                        <div @click="activeAccordion = activeAccordion === 'preventas' ? null : 'preventas'" class="flex justify-between py-2 items-center cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': activeAccordion === 'preventas'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                <span>Preventas</span>
                            </div>
                            <span class="font-medium">{{ $stats['preventas'] }}</span>
                        </div>
                        <div x-show="activeAccordion === 'preventas'" x-transition class="pl-4 pb-2 space-y-1 text-gray-500">
                            <div class="flex justify-between"><span>Confirmadas</span><span>1</span></div>
                        </div>
                    </div>

                    {{-- Entregas (Expandable) --}}
                    <div class="flex flex-col">
                        <div @click="activeAccordion = activeAccordion === 'entregas' ? null : 'entregas'" class="flex justify-between py-2 items-center cursor-pointer hover:bg-gray-50 transition">
                            <div class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-gray-400 transform transition-transform duration-200" :class="{'rotate-180': activeAccordion === 'entregas'}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                <span>Entregas</span>
                            </div>
                            <span class="font-medium">{{ $stats['entregas'] }}</span>
                        </div>
                        <div x-show="activeAccordion === 'entregas'" x-transition class="pl-4 pb-2 space-y-1 text-gray-500">
                            <div class="flex justify-between"><span>Completadas</span><span>0</span></div>
                        </div>
                    </div>

                    <div class="flex justify-between py-2">
                        <span>Clientes nuevos</span>
                        <span class="font-medium">{{ $stats['clientes_nuevos'] }}</span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span>Total cobrado</span>
                        <span class="font-medium">${{ number_format($stats['total_cobrado'], 0) }}</span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span>Total vendido al día</span>
                        <span class="font-medium">${{ number_format($stats['total_vendido'], 0) }}</span>
                    </div>

                    <div class="flex justify-between py-2">
                        <span>Merma (%)</span>
                        <span class="font-medium"></span>
                    </div>
                </div>

                {{-- Footer stats --}}
                <div class="mt-4 text-[10px] text-gray-400 flex flex-col items-center border-t border-gray-100 pt-3">
                    <div class="flex justify-center w-full gap-8 mb-1">
                        <span>fim:: {{ $stats['fim'] }}</span>
                        <span>ffm:: {{ $stats['ffm'] }}</span>
                    </div>
                    <div class="flex justify-center w-full gap-8">
                        <span>pv: {{ $stats['pv'] }}</span>
                        <span>uv: {{ $stats['uv'] }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('visorDashboard', () => ({
        activeAccordion: null,
        chart: null,
        porcentaje: @entangle('stats.porcentaje'),

        initChart() {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    this.$nextTick(() => {
                        const ctx = document.getElementById('gaugeChart');
                        if (!ctx) return;

                        const val = this.porcentaje || 0;
                        const remainder = 100 - val;

                        // Si el DOM recreó el canvas, destruimos la gráfica anterior
                        if (this.chart && this.chart.canvas !== ctx) {
                            this.chart.destroy();
                            this.chart = null;
                        }

                        if (!this.chart) {
                            this.chart = new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Completado', 'Restante'],
                                    datasets: [{
                                        data: [val, remainder],
                                        backgroundColor: ['#f97316', '#e5e7eb'], // orange-500 and gray-200
                                        borderWidth: 0,
                                        cutout: '75%'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    rotation: -90,
                                    circumference: 180,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { enabled: false }
                                    },
                                    animation: {
                                        animateRotate: true,
                                        animateScale: false
                                    }
                                }
                            });
                        } else {
                            // Actualizar la gráfica si ya existe
                            this.chart.data.datasets[0].data = [val, remainder];
                            this.chart.update();
                        }
                    });
                });
            });
        }
    }));
});
</script>
@endpush
