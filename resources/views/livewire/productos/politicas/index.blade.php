<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state([
    'politicas' => [],
    'politicasFiltradas' => [],
    'search' => '',
]);

$aplicarFiltros = function () {
    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $this->politicasFiltradas = collect($this->politicas)->filter(function ($politica) use ($searchQuery) {
            return str_contains(strtolower($politica['nombre'] ?? ''), $searchQuery);
        })->values()->toArray();
    } else {
        $this->politicasFiltradas = $this->politicas;
    }
};

$updatedSearch = function () {
    $this->aplicarFiltros();
};

$clearSearch = function () {
    $this->search = '';
    $this->aplicarFiltros();
};

?>

<div class="h-full bg-white flex flex-col pt-4">
    <div class="w-full px-6 flex flex-col flex-1">

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-gray-800">Políticas Producto</h1>
        </div>

        {{-- Top Filters Row (Toolbar 1) --}}
        <div class="flex flex-wrap items-end gap-6 mb-4 px-1">
            <div class="w-48 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Zona</label>
                <select class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer">
                    <option>1Z - Zona 1</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="w-48 relative border-b border-gray-300 pb-1 mt-auto">
                <select class="w-full appearance-none bg-transparent text-xs text-gray-500 outline-none cursor-pointer">
                    <option>Vendedor</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
            
            <div class="w-48 relative border-b border-gray-300 pb-1 mt-auto">
                <select class="w-full appearance-none bg-transparent text-xs text-gray-500 outline-none cursor-pointer">
                    <option>Producto</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="w-36 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Desde</label>
                <div class="flex items-center">
                    <input type="text" value="24/07/2026"
                        class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-text p-0 border-none focus:ring-0">
                    <div class="text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="w-36 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Hasta</label>
                <div class="flex items-center">
                    <input type="text" value="24/07/2026"
                        class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-text p-0 border-none focus:ring-0">
                    <div class="text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="ml-auto mb-1">
                <button
                    class="bg-[#1f5ce6] hover:bg-blue-700 text-white text-xs font-semibold py-1.5 px-4 rounded transition-colors shadow-sm cursor-pointer">
                    Consultar
                </button>
            </div>
        </div>

        {{-- Toolbar Row (Toolbar 2) --}}
        <div class="flex items-center justify-between mb-4">
            <div class="w-48 relative border-b border-gray-300 pb-1">
                <select
                    class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer py-0.5">
                    <option>Activos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Search --}}
                <div class="relative flex items-center border-b border-gray-300 pb-1 w-64 group">
                    <svg class="w-3.5 h-3.5 text-gray-400 mr-2 shrink-0 group-focus-within:text-gray-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live="search" placeholder=" Buscar ..."
                        class="w-full bg-transparent text-xs text-gray-700 outline-none placeholder-gray-400 focus:ring-0 p-0 border-none" />
                    @if($search)
                        <button wire:click="clearSearch" class="text-gray-400 hover:text-gray-700 outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Action Icons --}}
                <div class="flex items-center gap-3 text-gray-500">
                    <button class="hover:text-gray-800 transition-colors cursor-pointer" title="Columnas">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 5h4v14H4V5zm6 0h4v14h-4V5zm6 0h4v14h-4V5z" />
                        </svg>
                    </button>
                    <button class="hover:text-gray-800 transition-colors cursor-pointer" title="Exportar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    <button class="hover:text-gray-800 transition-colors cursor-pointer" title="Recargar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button class="hover:text-gray-800 transition-colors cursor-pointer" title="Nuevo">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Grid --}}
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white">
                    <tr>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Acciones</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Nombre</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Zona</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Vendedor</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Cliente</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Producto</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6] text-right">
                            Lista Precio</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6]">
                            Línea</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-100 shadow-[0_1px_0_0_#f3f4f6] text-right">
                            Cantidad</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-700">
                    @forelse($politicasFiltradas as $politica)
                        {{-- Data Rows --}}
                    @empty
                        <tr>
                            <td colspan="9"
                                class="px-3 py-6 text-center text-gray-500 text-[11px] font-medium border-b-2 border-gray-300">
                                No hay Registros para mostrar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        <div class="py-2.5 flex items-center justify-end text-xs text-gray-500 gap-4 mt-1 border-t border-transparent">
            <div class="flex items-center gap-1">
                <span>10 Filas por Página</span>
                <svg class="w-3.5 h-3.5 text-gray-400 cursor-pointer" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="flex items-center gap-3">
                <button class="text-gray-300 hover:text-gray-400 cursor-not-allowed"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z" />
                    </svg></button>
                <button class="text-gray-300 hover:text-gray-400 cursor-not-allowed"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z" />
                    </svg></button>
                <span class="tracking-wide text-[10px]">0-0 of 0</span>
                <button class="text-gray-400 hover:text-gray-500 cursor-not-allowed"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                    </svg></button>
                <button class="text-gray-400 hover:text-gray-500 cursor-not-allowed"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z" />
                    </svg></button>
            </div>
        </div>

    </div>
</div>