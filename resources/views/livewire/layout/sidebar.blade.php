<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div x-data="{ collapsed: false }" :class="collapsed ? 'w-16' : 'w-64'"
    class="bg-[#003859] dark:bg-[#0a0a0a] text-gray-200 flex flex-col justify-between transition-all duration-300 h-full border-r border-[#002b45] dark:border-gray-800 select-none z-20 shrink-0 relative">

    <!-- Top Section (Header + Menu) -->
    <div class="flex flex-col overflow-y-auto">
        <!-- Sidebar Header (Module Title & Collapse Button) -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-[#002d48]">
            <span x-show="!collapsed" class="text-xs font-bold text-gray-300 tracking-widest uppercase" x-transition>
                {{ request()->routeIs('config.*') ? 'CONFIGURACIÓN' : (request()->routeIs('ruta.*') ? 'RUTA' : (request()->routeIs('clientes.*') ? 'CLIENTE' : 'VENTA')) }}
            </span>
            <button @click="collapsed = !collapsed"
                class="text-gray-400 hover:text-white transition duration-150 p-1 rounded hover:bg-[#002d48] cursor-pointer">
                <!-- Collapse Arrow Icon (left / right) -->
                <svg x-show="!collapsed" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <svg x-show="collapsed" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu Links -->
        <nav class="flex-1 py-3 px-2 space-y-1">
            @if (request()->routeIs('config.*'))
                <!-- Usuarios -->
                <a href="{{ route('config.users') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.users') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Usuarios</span>
                </a>

                <!-- Roles -->
                <a href="{{ route('config.roles') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.roles') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Roles</span>
                </a>

                <!-- Zonas -->
                <a href="{{ route('config.zonas') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.zonas') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Zonas</span>
                </a>

                <!-- Campos Adicionales -->
                <a href="{{ route('config.campos-adicionales') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.campos-adicionales') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Campos Adicionales</span>
                </a>

                <!-- Ajustes -->
                <a href="{{ route('config.ajustes') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.ajustes') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Ajustes</span>
                </a>

                <!-- Ticket -->
                <a href="{{ route('config.ticket') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('config.ticket') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Ticket</span>
                </a>
            @elseif(request()->routeIs('ruta.*'))
                <!-- Rutas -->
                <a href="{{ route('ruta.rutas') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ruta.rutas') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Rutas</span>
                </a>

                <!-- Static Mocks per requested layout -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Unidades de Reparto</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Agenda</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Agenda de Entregas</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Preventa Entrega</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Kilometraje</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Gastos Operativos</span>
                </a>
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <span x-show="!collapsed" class="pl-7" x-transition>Clientes Sincronizados</span>
                </a>
            @elseif(request()->routeIs('clientes.*'))
                <!-- Clientes -->
                <a href="{{ route('clientes.index') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('clientes.index') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Clientes</span>
                </a>

                <!-- Cadena de Clientes -->
                <a href="{{ route('clientes.cadena') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('clientes.cadena') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Cadena de Clientes</span>
                </a>

                <!-- Crédito -->
                <a href="{{ route('clientes.credito') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('clientes.credito') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('clientes.credito') ? 'text-orange-400' : '' }}" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />

                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />

                    </svg>
                    <span x-show="!collapsed" x-transition>Crédito</span>
                </a>

                <!-- Pedidos Crédito -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Pedidos Crédito</span>
                </a>

                <!-- Traspaso de Cliente -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Traspaso de Cliente</span>
                </a>
            @else
                <!-- Levantamiento -->
                <a href="{{ route('ventas.levantamiento') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.levantamiento') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Levantamiento</span>
                </a>

                <!-- Pedidos -->
                <a href="{{ route('ventas.pedidos') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.pedidos') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Pedidos</span>
                </a>

                <!-- Cobranza -->
                <a href="{{ route('ventas.cobranza') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.cobranza') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('ventas.cobranza') ? 'text-orange-400' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Cobranza</span>
                </a>

                <!-- Utilidad -->
                <a href="{{ route('ventas.utilidad') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.utilidad') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('ventas.utilidad') ? 'text-orange-400' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Utilidad</span>
                </a>

                <!-- Depósito Venta -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Depósito Venta</span>
                </a>

                <!-- Nuevo Gasto Operativo -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2 4 4m0-7v3h-3" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Nuevo Gasto Operativo</span>
                </a>

                <!-- Reporte de Ventas por Cliente -->
                <a href="{{ route('ventas.ventas-cliente') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.ventas-cliente') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('ventas.ventas-cliente') ? 'text-orange-400' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Reporte de Ventas por Cliente</span>
                </a>

                <!-- Clientes con Mayor Venta -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.969 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Clientes con Mayor Venta</span>
                </a>

                <!-- Productos Rechazados -->
                <a href="#"
                    class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Productos Rechazados</span>
                </a>

                <!-- Clientes Pendientes -->
                <!-- Clientes Pendientes -->
                <a href="{{ route('ventas.clientes-pendientes') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.clientes-pendientes') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Clientes Pendientes</span>
                </a>

                <!-- Reportes y Gráficas -->
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('dashboard') ? 'text-orange-400' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 13v-1m4 1v-3m4 3V8M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Reportes y Gráficas</span>
                </a>

                <!-- Reportes Globales -->
                <a href="{{ route('ventas.reportes-globales') }}" wire:navigate
                    class="flex items-center gap-3 px-3 py-2 text-xs rounded transition duration-150 {{ request()->routeIs('ventas.reportes-globales') ? 'bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm font-semibold' : 'font-medium text-gray-300 hover:text-white hover:bg-[#002d48]' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('ventas.reportes-globales') ? 'text-orange-400' : '' }}"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13h2.243a2 2 0 011.956 1.566l1.206 5.433a2 2 0 003.905-.333l1.83-11.895a2 2 0 013.918-.112l1.378 4.606A2 2 0 0019.345 14H21" />
                    </svg>
                    <span x-show="!collapsed" x-transition>Reportes Globales</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- Bottom Section (User & Settings Dropdown) -->
    <div class="p-3 border-t border-[#002d48] relative" x-data="{ open: false }">
        <!-- Trigger -->
        <button @click="open = !open"
            class="flex items-center justify-between w-full p-2 rounded-lg hover:bg-[#002d48] transition-colors duration-150 text-left focus:outline-none cursor-pointer">
            <div class="flex items-center gap-3">
                <!-- User Icon / Avatar -->
                <div
                    class="w-8 h-8 rounded-full bg-[#00273f] flex items-center justify-center border border-[#002b45] text-gray-300 shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <!-- User Name -->
                <div x-show="!collapsed" class="flex flex-col min-w-0" x-transition>
                    <span
                        class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Usuario RUTX' }}</span>
                    <span
                        class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email ?? 'admin@rutx.test' }}</span>
                </div>
            </div>

            <!-- Chevron Up Icon (only visible when not collapsed) -->
            <svg x-show="!collapsed" class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            </svg>
        </button>

        <!-- Dropdown Menu (opens upwards or side-aligned when collapsed) -->
        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
            :class="collapsed ? 'absolute bottom-2 left-16 w-48' : 'absolute bottom-16 left-3 right-3 w-auto'"
            class="bg-white rounded-lg shadow-xl border border-gray-200 py-1.5 z-50 text-gray-700"
            style="display: none;">

            <!-- Profile Link -->
            <x-dropdown-link :href="route('profile')" wire:navigate>
                {{ __('Profile') }}
            </x-dropdown-link>

            <!-- Logout Action -->
            <button wire:click="logout" class="w-full text-start">
                <x-dropdown-link>
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </button>
        </div>
    </div>
</div>
