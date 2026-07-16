<x-app-layout>
    @php
        // ── KPI Mocks (se sustituirán con datos de Firebird) ──────────────
        $kpiTotal          = 15420.00;   // Venta total del período
        $kpiTotalVenta     = 13800.00;   // Sub-línea "Venta" del total
        $kpiContado        = 9250.00;    // Ventas al contado
        $kpiContadoVenta   = 8100.00;
        $kpiCredito        = 6170.00;    // Ventas a crédito
        $kpiCreditoVenta   = 5700.00;
        $kpiCobranza       = 3750.00;    // Cobranza cobrada
        $kpiNoVentas       = 7;          // Visitas sin venta (entero)
        $kpiEntrega        = 11200.00;   // Entregas totales
        $kpiEntregaContado = 7400.00;
        $kpiEntregaCredito = 3800.00;
        $kpiGastos         = 1350.00;
        $monedaId          = 1;       // 3720 = USD

        // Mock: ventas por ruta (se sustituirá con datos de Firebird)
        $rutasMock = [
            ['nombre' => 'Ruta Norte',    'contado' => 4200,  'credito' => 1800],
            ['nombre' => 'Ruta Sur',      'contado' => 3100,  'credito' => 2400],
            ['nombre' => 'Ruta Centro',   'contado' => 5300,  'credito' => 900 ],
            ['nombre' => 'Ruta Oriente',  'contado' => 1800,  'credito' => 3200],
            ['nombre' => 'Ruta Poniente', 'contado' => 2900,  'credito' => 1100],
            ['nombre' => 'Ruta Express',  'contado' => 6100,  'credito' => 500 ],
        ];

        // Mock: tabla de movimientos
        $movimientosMock = [
            ['tipo' => 'Preventa',            'total' => 8400.00,  'productos' => 210],
            ['tipo' => 'Entrega Directa',     'total' => 5200.00,  'productos' => 98 ],
            ['tipo' => 'Devolución Contado',  'total' => -320.00,  'productos' => 8  ],
            ['tipo' => 'Devolución Crédito',  'total' => -180.00,  'productos' => 4  ],
            ['tipo' => 'Cobranza',            'total' => 3750.00,  'productos' => '-'],
        ];

        // Derivados via CurrencyService -- única fuente de verdad
        $curr             = currency($monedaId);
        $currSymbol       = $curr->symbol();
        $totalMovimientos = collect($movimientosMock)->sum('total');
    @endphp

    {{-- ========================================================
         Chart.js — importado desde CDN (solo una vez por página)
    ========================================================= --}}
    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        @endpush
    @endonce

    <div class="font-sans space-y-6">

        {{-- ── Breadcrumbs ─────────────────────────────────────────────── --}}
        <div class="flex items-center text-sm text-gray-500">
            <span>Cpanel</span>
            <span class="mx-2">/</span>
            <span>Venta</span>
            <span class="mx-2">/</span>
            <span class="font-semibold text-gray-700">Reportes y Gráficas</span>
        </div>

        {{-- ── KPI Cards (7 métricas, igual a VeMobile) ─────────────────── --}}

        {{-- Fila 1: Total · Contado · Crédito · Cobranza (4 columnas en lg) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Total
                        <span class="text-xs font-normal text-gray-400 ml-1"><x-currency-format :moneda-id="$monedaId" type="code" /></span>
                    </h3>
                    <span class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiTotal" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiTotal" :moneda-id="$monedaId" type="conversion" />
                </p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-400">Venta</span>
                    <span class="text-xs text-gray-500 font-medium">
                        <x-currency-format :amount="$kpiTotalVenta" :moneda-id="$monedaId" type="amount" />
                    </span>
                </div>
            </div>

            {{-- Contado --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Contado</h3>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#4caf50;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiContado" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiContado" :moneda-id="$monedaId" type="conversion" />
                </p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-400">Venta</span>
                    <span class="text-xs text-gray-500 font-medium">
                        <x-currency-format :amount="$kpiContadoVenta" :moneda-id="$monedaId" type="amount" />
                    </span>
                </div>
            </div>

            {{-- Crédito --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Crédito</h3>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#7ead4b;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiCredito" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiCredito" :moneda-id="$monedaId" type="conversion" />
                </p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-400">Venta</span>
                    <span class="text-xs text-gray-500 font-medium">
                        <x-currency-format :amount="$kpiCreditoVenta" :moneda-id="$monedaId" type="amount" />
                    </span>
                </div>
            </div>

            {{-- Cobranza --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Cobranza</h3>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#578cdd;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 18.54l-7.37-5.73L3 14.07l9 7 9-7-1.63-1.27-7.38 5.74zM12 16l7.36-5.73L21 9l-9-7-9 7 1.63 1.27L12 16z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiCobranza" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiCobranza" :moneda-id="$monedaId" type="conversion" />
                </p>
            </div>

        </div>

        {{-- Fila 2: No Ventas · Entrega · Gastos (3 columnas en lg) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- No Ventas --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">No Ventas</h3>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#f44336;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $kpiNoVentas }}</p>
            </div>

            {{-- Entrega --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Entrega</h3>
                    <span class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color:#f57c00;">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5 10c-.03 0-.05.01-.08.01L13.41 6H9v2h3.59l2 2h-8.1C4.01 10 2 12.02 2 14.5 2 16.99 4.01 19 6.5 19c2.22 0 4.06-1.62 4.42-3.73L13.04 14c-.02.17-.04.33-.04.5 0 2.49 2.01 4.5 4.5 4.5s4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm-8.66 5.26C8.52 16.27 7.58 17 6.47 17c-1.38 0-2.5-1.12-2.5-2.5S5.09 12 6.47 12c1.12 0 2.05.74 2.37 1.75H6v1.5l2.84.01zM17.47 17c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiEntrega" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiEntrega" :moneda-id="$monedaId" type="conversion" />
                </p>
                <div class="mt-2 space-y-0.5">
                    <p class="text-xs text-gray-500">Contado:&nbsp;<span class="font-medium"><x-currency-format :amount="$kpiEntregaContado" :moneda-id="$monedaId" type="amount" /></span></p>
                    <p class="text-xs text-gray-500">Crédito:&nbsp;<span class="font-medium"><x-currency-format :amount="$kpiEntregaCredito" :moneda-id="$monedaId" type="amount" /></span></p>
                </div>
            </div>

            {{-- Gastos --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">Gastos</h3>
                    <span class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">
                    <x-currency-format :amount="$kpiGastos" :moneda-id="$monedaId" type="amount" />
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <x-currency-format :amount="$kpiGastos" :moneda-id="$monedaId" type="conversion" />
                </p>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════
             SECCIÓN PRINCIPAL: Gráfica (2/3) + Movimientos (1/3)
        ══════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- ── Panel izquierdo: Gráfica de Ventas por Ruta (2/3) ───── --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                {{-- Encabezado del panel --}}
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800">Ventas por Ruta</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Contado vs Crédito — datos simulados</p>
                    </div>
                    {{-- Leyenda de colores --}}
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block w-3 h-3 rounded-sm" style="background-color:#2065d1;"></span>
                            Contado
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block w-3 h-3 rounded-sm" style="background-color:#a5c4f7;"></span>
                            Crédito
                        </span>
                    </div>
                </div>

                {{--
                    wire:ignore  → Livewire no tocará este DOM al re-renderizar,
                                   así Chart.js no pierde su instancia de canvas.
                    x-data / x-init → Alpine.js controla el ciclo de vida del chart.
                --}}
                <div
                    wire:ignore
                    x-data="{
                        chart: null,
                        labels:  {{ json_encode(array_column($rutasMock, 'nombre')) }},
                        contado: {{ json_encode(array_column($rutasMock, 'contado')) }},
                        credito: {{ json_encode(array_column($rutasMock, 'credito')) }},
                        init() {
                            this.chart = new Chart(this.$refs.ventasCanvas, {
                                type: 'bar',
                                data: {
                                    labels: this.labels,
                                    datasets: [
                                        {
                                            label: 'Contado',
                                            data: this.contado,
                                            backgroundColor: '#2065d1',
                                            borderRadius: 6,
                                            borderSkipped: false,
                                        },
                                        {
                                            label: 'Crédito',
                                            data: this.credito,
                                            backgroundColor: '#a5c4f7',
                                            borderRadius: 6,
                                            borderSkipped: false,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            callbacks: {
                                                label: ctx => ' {{ $currSymbol }}' + ctx.parsed.y.toLocaleString('es-MX')
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: { display: false },
                                            ticks: { font: { size: 11 }, color: '#6b7280' }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: '#f3f4f6' },
                                            ticks: {
                                                font: { size: 11 },
                                                color: '#6b7280',
                                                callback: val => '{{ $currSymbol }}' + val.toLocaleString('es-MX')
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }"
                    class="relative"
                    style="height: 320px;"
                >
                    <canvas x-ref="ventasCanvas" id="ventasChart" aria-label="Gráfica de ventas por ruta"></canvas>
                </div>
            </div>

            {{-- ── Panel derecho: Tabla de Movimientos (1/3) ───────────── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">

                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-gray-800">Movimientos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Resumen del período</p>
                </div>

                <div class="flex-1 overflow-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 uppercase tracking-wide">
                                <th class="text-left px-3 py-2 rounded-l-lg font-medium">Tipo</th>
                                <th class="text-right px-3 py-2 font-medium">Total</th>
                                <th class="text-right px-3 py-2 rounded-r-lg font-medium">Piezas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($movimientosMock as $mov)
                            <tr class="hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-3 py-2.5 text-gray-700 font-medium">{{ $mov['tipo'] }}</td>
                                <td class="px-3 py-2.5 text-right text-gray-900 font-semibold tabular-nums">
                                    <x-currency-format :amount="(float) $mov['total']" :moneda-id="$monedaId" type="amount" />
                                </td>
                                <td class="px-3 py-2.5 text-right text-gray-500 tabular-nums">{{ $mov['productos'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mini resumen al pie --}}
                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">Total período</span>
                    <span class="text-sm font-bold" style="color:#2065d1;">
                        <x-currency-format :amount="$totalMovimientos" :moneda-id="$monedaId" type="amount" />
                    </span>
                </div>
            </div>

        </div>{{-- fin grid principal --}}

    </div>
</x-app-layout>
