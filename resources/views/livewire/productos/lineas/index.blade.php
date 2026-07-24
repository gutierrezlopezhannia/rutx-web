<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockLineas = [
    ['codigo' => 'LIN-001', 'nombre' => 'Bebidas', 'estatus' => 'Activo'],
    ['codigo' => 'LIN-002', 'nombre' => 'Abarrotes', 'estatus' => 'Activo'],
    ['codigo' => 'LIN-003', 'nombre' => 'Botanas', 'estatus' => 'Activo'],
];

state([
    'lineas' => $mockLineas,
    'lineasFiltradas' => $mockLineas,
    'search' => '',
]);

$aplicarFiltros = function () {
    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $this->lineasFiltradas = collect($this->lineas)->filter(function ($linea) use ($searchQuery) {
            return str_contains(strtolower($linea['codigo']), $searchQuery) ||
                str_contains(strtolower($linea['nombre']), $searchQuery);
        })->values()->toArray();
    } else {
        $this->lineasFiltradas = $this->lineas;
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
            <h1 class="text-xl font-semibold text-gray-800">Líneas</h1>
        </div>

        {{-- Toolbar Row --}}
        <div class="flex items-center justify-between mb-4">
            <div class="w-48 relative border-b border-gray-300 pb-1">
                <select
                    class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer py-0.5">
                    <option>Activos</option>
                    <option>Inactivos</option>
                    <option>Todos</option>
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
        <div class="flex-1 overflow-auto border-t border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white">
                    <tr>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 w-16 text-center shadow-sm">
                            Acciones</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm w-32">
                            Clave</th>
                        <th class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm">
                            Nombre</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm w-32">
                            Estatus</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-700">
                    @forelse($lineasFiltradas as $linea)
                        <tr class="hover:bg-blue-50/50 transition-colors border-b border-gray-100">
                            <td class="px-3 py-2 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="text-blue-600 hover:text-blue-800" title="Editar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700" title="Eliminar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-3 py-2 uppercase text-[11px] truncate max-w-[120px]">{{ $linea['codigo'] }}
                            </td>
                            <td class="px-3 py-2 uppercase text-[11px] truncate max-w-[200px]">
                                {{ $linea['nombre'] }}
                            </td>
                            <td class="px-3 py-2 text-[11px] capitalize">
                                <span
                                    class="px-2 py-1 rounded-full text-[9px] font-semibold tracking-wider {{ strtolower($linea['estatus']) === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $linea['estatus'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-400 text-sm">
                                No se encontraron líneas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>