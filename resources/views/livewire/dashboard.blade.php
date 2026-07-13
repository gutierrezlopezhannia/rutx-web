<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    // Definición de datos simulados (Mocks)
    public int $visitasTotales = 45;
    public int $ctesNoVisitados = 12;
    public string $ventasTotales = '$15,420.00';
    public int $articulosVendidos = 340;
    public int $pedidosTotales = 28;
};
?>

<div class="font-sans">
    <!-- Breadcrumbs -->
    <div class="flex items-center text-sm text-gray-500 mb-6">
        <span>Cpanel</span>
        <span class="mx-2">/</span>
        <span>Venta</span>
        <span class="mx-2">/</span>
        <span class="font-semibold text-gray-700">Reportes y Gráficas</span>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-center h-28">
            <div class="flex items-center text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <h3 class="text-xs font-medium tracking-wide">Visitas Totales</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $visitasTotales }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-center h-28">
            <div class="flex items-center text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                <h3 class="text-xs font-medium tracking-wide">Clientes No Visitados</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $ctesNoVisitados }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-center h-28">
            <div class="flex items-center text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-xs font-medium tracking-wide">$ Ventas Totales</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $ventasTotales }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-center h-28">
            <div class="flex items-center text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-xs font-medium tracking-wide">Artículos Vendidos</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $articulosVendidos }}</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col justify-center h-28">
            <div class="flex items-center text-gray-400 mb-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <h3 class="text-xs font-medium tracking-wide">Pedidos Totales</h3>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $pedidosTotales }}</p>
        </div>

    </div>
</div>
