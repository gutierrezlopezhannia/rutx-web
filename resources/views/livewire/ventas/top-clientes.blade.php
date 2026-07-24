<?php

use function Livewire\Volt\{state, layout, mount};
use Carbon\Carbon;

layout('layouts.app');

state([
    'registrosFiltrados' => [],
    'total_ventas_periodo' => 0.0,
    'total_pedidos_periodo' => 0,
    'avg_ticket_periodo' => 0.0,

    // Filtros
    'filtro_zona' => 'todos',
    'filtro_vendedor' => 'todos',
    'fecha_inicio' => '2018-09-19', // Captura el seeder fijos
    'fecha_fin' => '',
    'top_limit' => 10,
    'ordenar_por' => 'total_sales', // total_sales o total_orders

    // Búsqueda en los resultados
    'search' => '',
]);

$aplicarFiltros = function () {
    $invoicesQuery = \App\Models\Invoice::query();

    // Aplicar filtros
    if ($this->filtro_zona !== 'todos') {
        $invoicesQuery->where('zona_id', $this->filtro_zona);
    }
    if ($this->filtro_vendedor !== 'todos') {
        $invoicesQuery->where('vendedor_id', $this->filtro_vendedor);
    }
    if (!empty($this->fecha_inicio)) {
        $invoicesQuery->where('fecha', '>=', $this->fecha_inicio);
    }
    if (!empty($this->fecha_fin)) {
        $invoicesQuery->where('fecha', '<=', $this->fecha_fin);
    }

    // Clonar para calcular totales consolidados del período
    $grandTotalSales = (double) (clone $invoicesQuery)->sum('total');
    $grandTotalOrders = (int) (clone $invoicesQuery)->count();

    $this->total_ventas_periodo = $grandTotalSales;
    $this->total_pedidos_periodo = $grandTotalOrders;
    $this->avg_ticket_periodo = $grandTotalOrders > 0 ? $grandTotalSales / $grandTotalOrders : 0.0;

    // Agrupar por cliente
    $groupedQuery = $invoicesQuery->selectRaw('customer_id, SUM(total) as total_sales, COUNT(id) as total_orders, AVG(total) as avg_ticket')
        ->groupBy('customer_id');

    if ($this->ordenar_por === 'total_sales') {
        $groupedQuery->orderByDesc('total_sales');
    } else {
        $groupedQuery->orderByDesc('total_orders');
    }

    $groupedData = $groupedQuery->get();

    // Cargar todos los clientes asociados de una sola vez
    $customerIds = $groupedData->pluck('customer_id')->filter()->unique()->toArray();
    $customers = \App\Models\Customer::whereIn('id', $customerIds)->get()->keyBy('id');

    // Mapear registros con detalles del cliente y cálculo de porcentaje
    $allRecords = $groupedData->map(function ($row, $index) use ($grandTotalSales, $customers) {
        $customer = $customers->get($row->customer_id);
        $totalSales = (double) $row->total_sales;
        $percentage = $grandTotalSales > 0 ? ($totalSales / $grandTotalSales) * 100 : 0.0;

        return [
            'rank' => $index + 1,
            'cliente_id' => $row->customer_id,
            'cliente_codigo' => $customer->clave ?? 'N/A',
            'cliente_nombre' => $customer->nombre ?? 'Desconocido',
            'zona' => $customer->zona_id ?? 'N/A',
            'total_sales' => $totalSales,
            'total_orders' => (int) $row->total_orders,
            'avg_ticket' => (double) $row->avg_ticket,
            'percentage' => $percentage,
        ];
    });

    // Filtrar por término de búsqueda en cliente (nombre o clave)
    if (!empty($this->search)) {
        $searchLower = mb_strtolower($this->search);
        $allRecords = $allRecords->filter(function ($item) use ($searchLower) {
            return mb_strpos(mb_strtolower($item['cliente_nombre']), $searchLower) !== false ||
                   mb_strpos(mb_strtolower($item['cliente_codigo']), $searchLower) !== false;
        });
    }

    // Aplicar límite superior
    $this->registrosFiltrados = $this->top_limit === 'todos'
        ? $allRecords->values()->toArray()
        : $allRecords->take((int)$this->top_limit)->values()->toArray();
};

// Mount inicial
mount(function () {
    $this->fecha_fin = Carbon::now()->format('Y-m-d');
    $this->aplicarFiltros();
});

// Listeners de actualización de variables reactivas
$updatedFiltroZona = function () { $this->aplicarFiltros(); };
$updatedFiltroVendedor = function () { $this->aplicarFiltros(); };
$updatedFechaInicio = function () { $this->aplicarFiltros(); };
$updatedFechaFin = function () { $this->aplicarFiltros(); };
$updatedTopLimit = function () { $this->aplicarFiltros(); };
$updatedOrdenarPor = function () { $this->aplicarFiltros(); };
$updatedSearch = function () { $this->aplicarFiltros(); };

$limpiarFiltros = function () {
    $this->filtro_zona = 'todos';
    $this->filtro_vendedor = 'todos';
    $this->fecha_inicio = '2018-09-19';
    $this->fecha_fin = Carbon::now()->format('Y-m-d');
    $this->top_limit = 10;
    $this->ordenar_por = 'total_sales';
    $this->search = '';
    $this->aplicarFiltros();
};

$descargarCSV = function () {
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=top_clientes_" . date('Ymd_His') . ".csv",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Ranking', 'Clave Cliente', 'Nombre Cliente', 'Zona', 'Total Ventas ($)', 'Pedidos (#)', 'Ticket Promedio ($)', 'Participación (%)'];

    $callback = function() use($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($this->registrosFiltrados as $row) {
            fputcsv($file, [
                $row['rank'],
                $row['cliente_codigo'],
                $row['cliente_nombre'],
                $row['zona'],
                number_format($row['total_sales'], 2, '.', ''),
                $row['total_orders'],
                number_format($row['avg_ticket'], 2, '.', ''),
                number_format($row['percentage'], 2, '.', '')
            ]);
        }
        fclose($file);
    };

    session()->flash('mensaje_exito', 'El archivo CSV de Top Clientes se ha exportado correctamente.');
    return response()->stream($callback, 200, $headers);
};

?>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endpush
@endonce

<div>
    <div class="py-4" x-data="topClientsReports()" x-init="initCharts()">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-700 transition duration-150">Cpanel</a>
                <span class="mx-2 text-gray-400">/</span>
                <span>Venta</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500 font-medium">Clientes con Mayor Venta</span>
            </div>

            {{-- Alertas --}}
            @if (session()->has('mensaje_exito'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-xs rounded-lg flex items-center justify-between shadow-sm">
                    <span>{{ session('mensaje_exito') }}</span>
                    <button class="text-green-500 hover:text-green-700 font-bold focus:outline-none" onclick="this.parentElement.style.display='none'">&times;</button>
                </div>
            @endif

            {{-- Resumen de Métricas (Tarjetas superiores premium) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Total Ventas --}}
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80 flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] text-gray-400 font-bold uppercase tracking-wider">Total Ventas (Período)</span>
                        <span class="text-2xl font-bold text-gray-800 font-mono mt-1 block">${{ number_format($total_ventas_periodo, 2) }}</span>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 text-[#003859]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Total Pedidos --}}
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80 flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] text-gray-400 font-bold uppercase tracking-wider">Pedidos / Tickets</span>
                        <span class="text-2xl font-bold text-gray-800 font-mono mt-1 block">{{ number_format($total_pedidos_periodo) }}</span>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 text-[#003859]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>

                {{-- Ticket Promedio --}}
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80 flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] text-gray-400 font-bold uppercase tracking-wider">Ticket Promedio General</span>
                        <span class="text-2xl font-bold text-gray-800 font-mono mt-1 block">${{ number_format($avg_ticket_periodo, 2) }}</span>
                    </div>
                    <div class="p-3 rounded-full bg-blue-50 text-[#003859]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-6 mb-6">
                
                {{-- Título y Ordenamiento --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h2 class="text-lg font-bold text-[#1f2937]">Ranking de Clientes con Mayor Venta</h2>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-gray-400 font-medium">Ordenar por:</span>
                        <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-gray-50/50">
                            <button wire:click="$set('ordenar_por', 'total_sales')" 
                                class="px-3 py-1 rounded-md text-xs font-semibold transition cursor-pointer {{ $ordenar_por === 'total_sales' ? 'bg-[#003859] text-white shadow-sm' : 'text-gray-500 hover:text-[#003859]' }}">
                                Ventas ($)
                            </button>
                            <button wire:click="$set('ordenar_por', 'total_orders')" 
                                class="px-3 py-1 rounded-md text-xs font-semibold transition cursor-pointer {{ $ordenar_por === 'total_orders' ? 'bg-[#003859] text-white shadow-sm' : 'text-gray-500 hover:text-[#003859]' }}">
                                Pedidos (#)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Sección de Filtros (Estilo Underline) --}}
                <div class="flex flex-wrap items-end gap-6 mb-6 w-full">
                    
                    {{-- Zona --}}
                    <div class="flex flex-col w-full sm:w-[15%]">
                        <label class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Zona</label>
                        <select wire:model.live="filtro_zona" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todas las Zonas</option>
                            @foreach(\App\Models\Zone::pluck('id')->sort() as $z)
                                <option value="{{ $z }}">{{ $z }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Vendedor --}}
                    <div class="flex flex-col w-full sm:w-[22%]">
                        <label class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Vendedor</label>
                        <select wire:model.live="filtro_vendedor" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="todos">Todos los Vendedores</option>
                            @foreach(\App\Models\Seller::pluck('id')->sort() as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha Inicial --}}
                    <div class="flex flex-col w-full sm:w-[14%]">
                        <label class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Fecha inicial</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model.live="fecha_inicio" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm w-full cursor-pointer" />
                        </div>
                    </div>

                    {{-- Fecha Final --}}
                    <div class="flex flex-col w-full sm:w-[14%]">
                        <label class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Fecha final</label>
                        <div class="flex items-center justify-between border-0 border-b border-gray-300 rounded-none px-0 py-0.5 w-full">
                            <input type="date" wire:model.live="fecha_fin" class="border-none outline-none p-0 focus:ring-0 bg-transparent text-gray-700 font-semibold text-sm w-full cursor-pointer" />
                        </div>
                    </div>

                    {{-- Top Limit --}}
                    <div class="flex flex-col w-full sm:w-[12%]">
                        <label class="text-[11px] text-gray-400 font-bold uppercase tracking-wider mb-1">Mostrar Top</label>
                        <select wire:model.live="top_limit" class="border-0 border-b border-gray-300 rounded-none px-0 py-1 text-sm focus:outline-none focus:border-[#003859] focus:ring-0 bg-transparent text-gray-700 font-semibold cursor-pointer w-full">
                            <option value="5">Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="20">Top 20</option>
                            <option value="50">Top 50</option>
                            <option value="todos">Todos</option>
                        </select>
                    </div>

                </div>

                {{-- Barra de Acción y Búsqueda --}}
                <div class="flex justify-between items-center mb-4 gap-3">
                    {{-- Limpiar filtros --}}
                    <div>
                        <button wire:click="limpiarFiltros" class="text-xs text-red-500 hover:text-red-700 font-semibold hover:underline focus:outline-none flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Restablecer Filtros
                        </button>
                    </div>

                    {{-- Buscador y Exportación --}}
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border-0 border-b border-gray-300 rounded-none py-1 w-64">
                            <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" wire:model.live="search" placeholder="Filtrar por cliente..." class="border-none outline-none p-0 w-full focus:ring-0 bg-transparent text-gray-700 text-xs placeholder-gray-400" />
                        </div>

                        <span class="border-l border-gray-200 h-5 my-1"></span>

                        {{-- Exportar CSV --}}
                        <button wire:click="descargarCSV" class="text-gray-500 hover:text-[#004f7c] p-1.5 rounded transition duration-150 focus:outline-none cursor-pointer" title="Descargar CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tabla de Ranking --}}
                @if(!empty($registrosFiltrados))
                    <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                        <table class="min-w-full text-xs text-left whitespace-nowrap">
                            <thead class="bg-gray-50/70 text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-center w-12">Posición</th>
                                    <th class="px-4 py-3">Clave</th>
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Zona</th>
                                    <th class="px-4 py-3 text-right">Total Ventas</th>
                                    <th class="px-4 py-3 text-center">Pedidos</th>
                                    <th class="px-4 py-3 text-right">Ticket Promedio</th>
                                    <th class="px-4 py-3 w-40">% Part.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white text-gray-700">
                                @forelse($registrosFiltrados as $row)
                                    <tr class="hover:bg-gray-50/40 transition duration-150">
                                        {{-- Ranking Medallas Premium --}}
                                        <td class="px-4 py-3 text-center font-bold">
                                            @if($row['rank'] == 1)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-100 text-yellow-800 text-xs border border-yellow-300">1º</span>
                                            @elseif($row['rank'] == 2)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-150 text-gray-800 text-xs border border-gray-300">2º</span>
                                            @elseif($row['rank'] == 3)
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-100 text-orange-800 text-xs border border-orange-300">3º</span>
                                            @else
                                                <span class="text-gray-500 font-mono font-medium">{{ $row['rank'] }}</span>
                                            @endif
                                        </td>
                                        {{-- Clave --}}
                                        <td class="px-4 py-3 font-mono font-medium text-gray-500">{{ $row['cliente_codigo'] }}</td>
                                        {{-- Cliente --}}
                                        <td class="px-4 py-3 text-[#003859] font-bold">{{ $row['cliente_nombre'] }}</td>
                                        {{-- Zona --}}
                                        <td class="px-4 py-3 text-gray-500">{{ $row['zona'] }}</td>
                                        {{-- Total Ventas --}}
                                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">${{ number_format($row['total_sales'], 2) }}</td>
                                        {{-- Pedidos --}}
                                        <td class="px-4 py-3 text-center font-mono font-semibold">{{ $row['total_orders'] }}</td>
                                        {{-- Ticket Promedio --}}
                                        <td class="px-4 py-3 text-right font-mono text-gray-600">${{ number_format($row['avg_ticket'], 2) }}</td>
                                        {{-- Barra de progreso % --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-full bg-gray-100 rounded-full h-2">
                                                    <div class="bg-[#004f7c] h-2 rounded-full" style="width: {{ $row['percentage'] }}%"></div>
                                                </div>
                                                <span class="font-mono font-bold text-gray-700 min-w-8 text-right">{{ number_format($row['percentage'], 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 font-medium">
                                            No hay registros de clientes para mostrar en el rango e intereses seleccionados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        No hay datos que coincidan con los filtros aplicados.
                    </div>
                @endif

            </div>

            {{-- Panel de Gráficos Integrado --}}
            @if(!empty($registrosFiltrados))
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    {{-- Gráfico 1: Ventas en dinero --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-sm text-gray-700">Monto Facturado por Cliente</h3>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Gráfico de Barras</span>
                        </div>
                        <div class="relative h-72 w-full">
                            <canvas id="chartTopSales" wire:ignore></canvas>
                        </div>
                    </div>

                    {{-- Gráfico 2: Distribución de participación --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200/80">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-bold text-sm text-gray-700">Distribución de Participación de Ventas</h3>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Gráfico de Dona</span>
                        </div>
                        <div class="relative h-72 w-full">
                            <canvas id="chartSalesDistribution" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Footer Copyright --}}
            <div class="mt-12 text-center text-xs text-gray-400 font-medium">
                Copyright © JB VEMOBILE SA DE CV 2026.
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('topClientsReports', () => ({
        chartBar: null,
        chartPie: null,

        getChartData() {
            const rows = this.$wire.registrosFiltrados || [];
            return {
                labels: rows.map(r => r.cliente_nombre.substring(0, 15) + (r.cliente_nombre.length > 15 ? '...' : '')),
                sales: rows.map(r => parseFloat(r.total_sales)),
                percentages: rows.map(r => parseFloat(r.percentage)),
            };
        },

        initCharts() {
            // Construir al iniciar
            this.$nextTick(() => {
                this.buildCharts(this.getChartData());
            });

            // Escuchar hooks de Livewire para actualizar los gráficos dinámicamente
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    this.$nextTick(() => {
                        const data = this.getChartData();
                        const canvasBar = document.getElementById('chartTopSales');
                        const canvasPie = document.getElementById('chartSalesDistribution');

                        // Si Livewire recrea el DOM y cambian los canvas, destruimos referencias antiguas
                        if (this.chartBar && this.chartBar.canvas !== canvasBar) {
                            this.chartBar.destroy();
                            this.chartBar = null;
                        }
                        if (this.chartPie && this.chartPie.canvas !== canvasPie) {
                            this.chartPie.destroy();
                            this.chartPie = null;
                        }

                        if (!this.chartBar || !this.chartPie) {
                            this.buildCharts(data);
                        } else {
                            this.updateCharts(data);
                        }
                    });
                });
            });
        },

        buildCharts(data) {
            const barCtx = document.getElementById('chartTopSales');
            const pieCtx = document.getElementById('chartSalesDistribution');

            if (this.chartBar) this.chartBar.destroy();
            if (this.chartPie) this.chartPie.destroy();

            if (barCtx && data.sales.length > 0) {
                this.chartBar = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ventas Acumuladas ($)',
                            data: data.sales,
                            backgroundColor: '#004f7c',
                            borderColor: '#003859',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Ventas: $' + context.raw.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            if (pieCtx && data.percentages.length > 0) {
                const colors = [
                    '#003859', '#004f7c', '#007cc0', '#3b82f6', '#60a5fa', 
                    '#93c5fd', '#bfdbfe', '#dbeafe', '#eff6ff', '#f8fafc'
                ];
                
                // Si hay "todos" los clientes, tal vez sumamos el resto como "Otros" si excede el límite
                let chartLabels = [...data.labels];
                let chartData = [...data.percentages];

                if (chartData.length > 8) {
                    const topData = chartData.slice(0, 7);
                    const topLabels = chartLabels.slice(0, 7);
                    const othersSum = chartData.slice(7).reduce((a, b) => a + b, 0);
                    
                    topData.push(othersSum);
                    topLabels.push('Otros');
                    
                    chartData = topData;
                    chartLabels = topLabels;
                }

                this.chartPie = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartData,
                            backgroundColor: colors.slice(0, chartLabels.length),
                            borderWidth: 1,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    boxWidth: 10,
                                    font: { size: 9, family: 'Inter, sans-serif' }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.raw.toFixed(1) + '%';
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        },

        updateCharts(data) {
            if (this.chartBar) {
                this.chartBar.data.labels = data.labels;
                this.chartBar.data.datasets[0].data = data.sales;
                this.chartBar.update();
            }
            if (this.chartPie) {
                let chartLabels = [...data.labels];
                let chartData = [...data.percentages];

                if (chartData.length > 8) {
                    const topData = chartData.slice(0, 7);
                    const topLabels = chartLabels.slice(0, 7);
                    const othersSum = chartData.slice(7).reduce((a, b) => a + b, 0);
                    
                    topData.push(othersSum);
                    topLabels.push('Otros');
                    
                    chartData = topData;
                    chartLabels = topLabels;
                }
                
                this.chartPie.data.labels = chartLabels;
                this.chartPie.data.datasets[0].data = chartData;
                this.chartPie.update();
            }
        }
    }));
});
</script>
@endpush
