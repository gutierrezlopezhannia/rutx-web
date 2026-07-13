<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div x-data="{ collapsed: false }" 
     :class="collapsed ? 'w-16' : 'w-64'" 
     class="bg-[#003859] text-gray-200 flex flex-col justify-between transition-all duration-300 h-full border-r border-[#002b45] select-none z-20 shrink-0 relative">
    
    <!-- Top Section (Header + Menu) -->
    <div class="flex flex-col overflow-y-auto">
        <!-- Sidebar Header (Module Title & Collapse Button) -->
        <div class="flex items-center justify-between px-4 py-4 border-b border-[#002d48]">
            <span x-show="!collapsed" class="text-xs font-bold text-gray-300 tracking-widest uppercase" x-transition>
                VENTA
            </span>
            <button @click="collapsed = !collapsed" class="text-gray-400 hover:text-white transition duration-150 p-1 rounded hover:bg-[#002d48] cursor-pointer">
                <!-- Collapse Arrow Icon (left / right) -->
                <svg x-show="!collapsed" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                <svg x-show="collapsed" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu Links -->
        <nav class="flex-1 py-3 px-2 space-y-1">
            <!-- Levantamiento -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Document/List -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span x-show="!collapsed" x-transition>Levantamiento</span>
            </a>

            <!-- Pedidos -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Order/Tag -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span x-show="!collapsed" x-transition>Pedidos</span>
            </a>

            <!-- Cobranza -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Currency/Cash -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="!collapsed" x-transition>Cobranza</span>
            </a>

            <!-- Utilidad -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Chart -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                </svg>
                <span x-show="!collapsed" x-transition>Utilidad</span>
            </a>

            <!-- Depósito Venta -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Bank -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span x-show="!collapsed" x-transition>Depósito Venta</span>
            </a>

            <!-- Nuevo Gasto Operativo -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Receipt -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2 4 4m0-7v3h-3" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span x-show="!collapsed" x-transition>Nuevo Gasto Operativo</span>
            </a>

            <!-- Reporte de Ventas por Cliente -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Users Chart -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="!collapsed" x-transition>Reporte de Ventas por Cliente</span>
            </a>

            <!-- Clientes con Mayor Venta -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Star -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.969 0 1.371 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.17 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <span x-show="!collapsed" x-transition>Clientes con Mayor Venta</span>
            </a>

            <!-- Productos Rechazados -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Ban / Cancel -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
                <span x-show="!collapsed" x-transition>Productos Rechazados</span>
            </a>

            <!-- Clientes Pendientes -->
            <a href="#" class="flex items-center gap-3 px-3 py-2 text-xs font-medium text-gray-300 hover:text-white hover:bg-[#002d48] rounded transition duration-150">
                <!-- Icon: Exclamation -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span x-show="!collapsed" x-transition>Clientes Pendientes</span>
            </a>

            <!-- Reportes y Gráficas (Active) -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-xs font-semibold rounded transition duration-150 bg-[#004f7c] text-white border-l-4 border-orange-500 shadow-sm">
                <!-- Icon: Presentation Chart -->
                <svg class="w-4 h-4 shrink-0 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 13v-1m4 1v-3m4 3V8M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <span x-show="!collapsed" x-transition>Reportes y Gráficas</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Section (User & Settings Dropdown) -->
    <div class="p-3 border-t border-[#002d48] relative" x-data="{ open: false }">
        <!-- Trigger -->
        <button @click="open = !open" 
                class="flex items-center justify-between w-full p-2 rounded-lg hover:bg-[#002d48] transition-colors duration-150 text-left focus:outline-none cursor-pointer">
            <div class="flex items-center gap-3">
                <!-- User Icon / Avatar -->
                <div class="w-8 h-8 rounded-full bg-[#00273f] flex items-center justify-center border border-[#002b45] text-gray-300 shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                    </svg>
                </div>
                <!-- User Name -->
                <div x-show="!collapsed" class="flex flex-col min-w-0" x-transition>
                    <span class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Usuario RUTX' }}</span>
                    <span class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email ?? 'admin@rutx.test' }}</span>
                </div>
            </div>
            
            <!-- Chevron Up Icon (only visible when not collapsed) -->
            <svg x-show="!collapsed" class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
            </svg>
        </button>

        <!-- Dropdown Menu (opens upwards or side-aligned when collapsed) -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
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
