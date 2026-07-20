<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav class="bg-white dark:bg-[#1a202c] border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-6 z-30 select-none w-full transition-colors duration-300">
    <!-- Left: Logo & Status -->
    <div class="flex items-center gap-4">
        <!-- Logo -->
        <a href="{{ route('dashboard') }}" wire:navigate class="text-2xl font-extrabold text-[#004066] dark:text-gray-100 tracking-wider">
            RUTX
        </a>

        <!-- Status Pill -->
        <div class="flex items-center gap-1.5 border border-gray-200 dark:border-gray-600 rounded-full px-3 py-1 bg-[#f9fafb] dark:bg-gray-800">
            <span class="text-xs text-gray-500 dark:text-gray-300 font-medium">Sincronización</span>
            <span class="w-2 h-2 rounded-full bg-green-500 inline-block animate-pulse"></span>
            <span class="text-[10px] font-bold text-green-600 tracking-wider">CONECTADO</span>
        </div>
    </div>

    <!-- Center/Right-Center: Module Tabs -->
    <div class="flex items-center h-full gap-2">
        <!-- Cliente -->
        <a href="{{ route('clientes.index') }}" wire:navigate class="flex flex-col items-center justify-center h-full px-5 border-b-2 {{ request()->routeIs('clientes.*') ? 'border-[#004066] dark:border-blue-400 text-[#004066] dark:text-blue-400 font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600' }} transition-all duration-150">
            <!-- User Group Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[11px] {{ request()->routeIs('clientes.*') ? '' : 'font-medium' }} tracking-wide">Cliente</span>
        </a>

        <!-- Producto -->
        <a href="#" class="flex flex-col items-center justify-center h-full px-5 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-150">
            <!-- Box Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="text-[11px] font-medium tracking-wide">Producto</span>
        </a>

        <!-- Inventario -->
        <a href="#" class="flex flex-col items-center justify-center h-full px-5 border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-150">
            <!-- Clipboard Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[11px] font-medium tracking-wide">Inventario</span>
        </a>

        <!-- Venta -->
        <a href="{{ route('dashboard') }}" wire:navigate class="flex flex-col items-center justify-center h-full px-5 border-b-2 {{ (request()->routeIs('dashboard') || request()->routeIs('ventas.*')) ? 'border-[#004066] dark:border-blue-400 text-[#004066] dark:text-blue-400 font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600' }} transition-all duration-150">
            <!-- Shopping Cart Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="text-[11px] {{ (request()->routeIs('dashboard') || request()->routeIs('ventas.*')) ? '' : 'font-medium' }} tracking-wide">Venta</span>
        </a>

        <!-- Ruta -->
        <a href="{{ route('ruta.rutas') }}" wire:navigate class="flex flex-col items-center justify-center h-full px-5 border-b-2 {{ request()->routeIs('ruta.*') ? 'border-[#004066] dark:border-blue-400 text-[#004066] dark:text-blue-400 font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600' }} transition-all duration-150">
            <!-- Map Pin Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-[11px] {{ request()->routeIs('ruta.*') ? '' : 'font-medium' }} tracking-wide">Ruta</span>
        </a>

        <!-- Configuración -->
        <a href="{{ route('config.users') }}" class="flex flex-col items-center justify-center h-full px-5 border-b-2 {{ request()->routeIs('config.*') ? 'border-[#004066] dark:border-blue-400 text-[#004066] dark:text-blue-400 font-semibold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-[#004066] dark:hover:text-blue-300 hover:border-gray-200 dark:hover:border-gray-600' }} transition-all duration-150">
            <!-- Cog Icon -->
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-[11px] {{ request()->routeIs('config.*') ? '' : 'font-medium' }} tracking-wide">Configuración</span>
        </a>
    </div>
</nav>

