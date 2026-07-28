<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockLineasFamilia = [
    // ['id' => 1, 'linea_familia' => 'Ejemplo Familia', 'prefijo' => 'EJF', 'orden' => 1, 'estatus' => 'Activo'],
];

state([
    'lineasFamilia' => $mockLineasFamilia,
    'search' => '',
    'filtro_estado' => 'Borrados',
    'columnas' => [
        'linea_familia' => true,
        'prefijo' => true,
        'orden' => true,
        'estatus' => true,
        'fecha' => false,
    ],
    'isCreateModalOpen' => false,
    'form' => [
        'linea_familia' => '',
        'prefijo' => '',
        'orden' => '',
        'estatus' => 'Activo',
    ]
]);

$filteredLineasFamilia = function () {
    $filtered = collect($this->lineasFamilia);

    if ($this->filtro_estado === 'Borrados') {
        // En este ejemplo, si filtro es Borrados podríamos filtrar por estatus Borrado.
        // Pero el mockup lo deja libre. Dejaremos la logica base.
    }

    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $filtered = $filtered->filter(function ($item) use ($searchQuery) {
            return str_contains(strtolower($item['linea_familia']), $searchQuery);
        });
    }

    return $filtered->toArray();
};

?>

<div class="h-full bg-transparent p-6 font-sans">
    <div class="max-w-[1400px] mx-auto bg-white dark:bg-gray-800 rounded shadow-sm">

        {{-- Header Title --}}
        <div class="px-6 py-5">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Línea Familia</h1>
        </div>

        {{-- Toolbar --}}
        <div class="px-6 py-2 flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 gap-4">
            {{-- Left: Status Dropdown --}}
            <div class="w-full md:w-48 relative border-b border-gray-300 pb-1">
                <select wire:model.live="filtro_estado"
                    class="appearance-none w-full bg-transparent border-0 px-0 py-1 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-0 cursor-pointer">
                    <option value="Borrados">Borrados</option>
                    <option value="Activos">Activos</option>
                    <option value="Inactivos">Inactivos</option>
                    <option value="Todos">Todos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-1 text-gray-400">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </div>
            </div>

            {{-- Right: Actions & Search --}}
            <div class="flex items-center gap-4 text-gray-500">

                {{-- Search Input --}}
                <div
                    class="relative flex items-center w-full md:w-64 border-b border-gray-300 dark:border-gray-600 pb-1 group">
                    <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0 group-focus-within:text-gray-600" fill="none"
                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live="search" placeholder="Buscar ..."
                        class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm text-gray-600 dark:text-gray-300 placeholder-gray-400" />
                    @if($search)
                        <button wire:click="$set('search', '')" class="text-gray-400 hover:text-gray-700 outline-none ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

                {{-- Action Icons --}}
                <div class="flex items-center gap-3 shrink-0 ml-2">
                    <div x-data="{ openColumns: false }" class="relative">
                        <button @click="openColumns = !openColumns"
                            class="p-1 hover:text-gray-800 dark:hover:text-gray-300 transition cursor-pointer"
                            title="Columnas">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M5 4h3v16H5V4zm6 0h3v16h-3V4zm6 0h3v16h-3V4z" />
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="openColumns" @click.away="openColumns = false" x-transition
                            class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-50 text-gray-800 dark:text-gray-200"
                            style="display: none;">
                            <div class="px-5 py-4 font-bold text-sm border-b border-gray-100 dark:border-gray-700">
                                Add or remove columns
                            </div>
                            <div class="p-3 flex flex-col gap-2 max-h-64 overflow-y-auto">
                                <label
                                    class="flex items-center gap-4 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="columnas.linea_familia"
                                        class="text-rose-600 focus:ring-rose-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-4 h-4 cursor-pointer">
                                    <span class="text-[15px]">Línea Familia</span>
                                </label>
                                <label
                                    class="flex items-center gap-4 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="columnas.prefijo"
                                        class="text-rose-600 focus:ring-rose-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-4 h-4 cursor-pointer">
                                    <span class="text-[15px]">Prefijo</span>
                                </label>
                                <label
                                    class="flex items-center gap-4 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="columnas.orden"
                                        class="text-rose-600 focus:ring-rose-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-4 h-4 cursor-pointer">
                                    <span class="text-[15px]">Orden</span>
                                </label>
                                <label
                                    class="flex items-center gap-4 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="columnas.estatus"
                                        class="text-rose-600 focus:ring-rose-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-4 h-4 cursor-pointer">
                                    <span class="text-[15px]">Estatus</span>
                                </label>
                                <label
                                    class="flex items-center gap-4 px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer rounded transition">
                                    <input type="checkbox" wire:model.live="columnas.fecha"
                                        class="text-rose-600 focus:ring-rose-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-4 h-4 cursor-pointer">
                                    <span class="text-[15px]">cat_linea_familia.columnas.fecha</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="p-1 hover:text-gray-800 dark:hover:text-gray-300 transition cursor-pointer"
                        title="Exportar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    <button class="p-1 hover:text-gray-800 dark:hover:text-gray-300 transition cursor-pointer"
                        title="Refrescar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button wire:click="$set('isCreateModalOpen', true)" class="p-1 hover:text-gray-800 dark:hover:text-gray-300 transition cursor-pointer"
                        title="Agregar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-left bg-white dark:bg-gray-800 border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide w-48 border-b border-gray-100">
                            Acciones</th>
                        @if($this->columnas['linea_familia'])
                            <th
                                class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide border-b border-gray-100">
                                Línea Familia</th>
                        @endif
                        @if($this->columnas['prefijo'])
                            <th
                                class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide border-b border-gray-100 text-center">
                                Prefijo</th>
                        @endif
                        @if($this->columnas['orden'])
                            <th
                                class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide border-b border-gray-100 text-center">
                                Orden</th>
                        @endif
                        @if($this->columnas['estatus'])
                            <th
                                class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide border-b border-gray-100 text-center">
                                Estatus</th>
                        @endif
                        @if($this->columnas['fecha'])
                            <th
                                class="px-6 py-3 text-[13px] font-bold text-gray-800 dark:text-gray-200 tracking-wide border-b border-gray-100 text-center">
                                cat_linea_familia.columnas.fecha</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php $filtered = $this->filteredLineasFamilia(); @endphp
                    @forelse($filtered as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group">
                            {{-- Acciones --}}
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <button class="text-blue-600 hover:text-blue-800 transition" title="Editar">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 000-1.41l-2.34-2.34a.996.996 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                        </svg>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700 transition" title="Eliminar">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            {{-- Data --}}
                            @if($this->columnas['linea_familia'])
                                <td class="px-6 py-3 text-[13px] text-gray-800 dark:text-gray-300 whitespace-nowrap">
                                    {{ $item['linea_familia'] }}
                                </td>
                            @endif
                            @if($this->columnas['prefijo'])
                                <td
                                    class="px-6 py-3 text-[13px] text-gray-600 dark:text-gray-400 whitespace-nowrap text-center">
                                    {{ $item['prefijo'] }}
                                </td>
                            @endif
                            @if($this->columnas['orden'])
                                <td
                                    class="px-6 py-3 text-[13px] text-gray-600 dark:text-gray-400 whitespace-nowrap text-center">
                                    {{ $item['orden'] }}
                                </td>
                            @endif
                            @if($this->columnas['estatus'])
                                <td class="px-6 py-3 text-[13px] text-center">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-semibold tracking-wider {{ strtolower($item['estatus']) === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item['estatus'] }}
                                    </span>
                                </td>
                            @endif
                            @if($this->columnas['fecha'])
                                <td
                                    class="px-6 py-3 text-[13px] text-gray-600 dark:text-gray-400 whitespace-nowrap text-center">
                                    {{ $item['fecha'] ?? '-' }}
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-600 text-[14px]">
                                No hay Registros para mostrar
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        <div class="px-6 py-3 flex items-center justify-end text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2">
                    <span>100 Filas por Página</span>
                    <svg class="w-4 h-4 text-gray-500 cursor-pointer" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </div>

                <div class="flex items-center gap-4">
                    {{-- First Page --}}
                    <button class="text-gray-300 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z" />
                        </svg>
                    </button>
                    {{-- Prev Page --}}
                    <button class="text-gray-300 cursor-not-allowed">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z" />
                        </svg>
                    </button>

                    <span class="text-[13px] mx-1">0-0 of 0</span>

                    {{-- Next Page --}}
                    <button class="text-gray-300 cursor-not-allowed transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                        </svg>
                    </button>
                    {{-- Last Page --}}
                    <button class="text-gray-300 cursor-not-allowed transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Form Modal (Nueva Línea Familia) --}}
    @if($isCreateModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity">
        <div class="bg-white dark:bg-gray-800 rounded shadow-xl w-full max-w-2xl mx-4 overflow-hidden relative p-8" @click.away="$wire.set('isCreateModalOpen', false)">
            <h2 class="text-[22px] font-semibold text-gray-800 dark:text-gray-100 mb-8">Nueva Línea Familia</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-7">
                {{-- Línea Familia --}}
                <div class="relative pt-4">
                    <label class="absolute top-0 text-[13px] text-gray-500 dark:text-gray-400 font-medium h-4">Línea Familia</label>
                    <input type="text" wire:model="form.linea_familia" class="w-full bg-transparent border-0 border-b border-gray-400 hover:border-gray-800 dark:border-gray-600 focus:ring-0 focus:border-[#1976D2] text-[15px] py-1.5 px-0 text-gray-800 dark:text-gray-200 transition-colors" />
                </div>

                {{-- Prefijo --}}
                <div class="relative pt-4">
                    <label class="absolute top-0 text-[13px] text-gray-500 dark:text-gray-400 font-medium h-4">Prefijo</label>
                    <input type="text" wire:model="form.prefijo" class="w-full bg-transparent border-0 border-b border-gray-400 hover:border-gray-800 dark:border-gray-600 focus:ring-0 focus:border-[#1976D2] text-[15px] py-1.5 px-0 text-gray-800 dark:text-gray-200 transition-colors" />
                </div>

                {{-- Orden --}}
                <div class="relative pt-4">
                    <label class="absolute top-0 text-[13px] text-gray-500 dark:text-gray-400 font-medium h-4">Orden</label>
                    <input type="text" wire:model="form.orden" class="w-full bg-transparent border-0 border-b border-gray-400 hover:border-gray-800 dark:border-gray-600 focus:ring-0 focus:border-[#1976D2] text-[15px] py-1.5 px-0 text-gray-800 dark:text-gray-200 transition-colors" />
                </div>

                {{-- Estatus --}}
                <div class="relative pt-4">
                    <label class="absolute top-0 text-[13px] text-gray-500 dark:text-gray-400 font-medium h-4">Estatus</label>
                    <select wire:model="form.estatus" class="w-full bg-transparent border-0 border-b border-gray-400 hover:border-gray-800 dark:border-gray-600 focus:ring-0 focus:border-[#1976D2] text-[15px] py-1.5 px-0 text-gray-800 dark:text-gray-200 cursor-pointer appearance-none transition-colors">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 bottom-0 right-0 flex items-center pt-4 text-gray-500">
                        <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                            <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="mt-10 flex justify-end gap-3 items-center">
                <button type="button" wire:click="$set('isCreateModalOpen', false)" class="text-[#1976D2] hover:bg-blue-50 dark:hover:bg-blue-900/30 text-[14px] font-semibold px-4 py-2 rounded transition">
                    Cancelar
                </button>
                <div class="px-5 py-2 rounded bg-[#e0e0e0] dark:bg-gray-700">
                    <span class="text-gray-400 dark:text-gray-500 text-[14px] font-semibold cursor-not-allowed select-none">
                        Guardar
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>