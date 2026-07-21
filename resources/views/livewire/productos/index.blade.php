<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockProductos = [
    ['codigo' => 'PROD-001', 'descripcion' => 'Bebida Energética 500ml', 'estatus' => 'Activo', 'linea' => 'Bebidas', 'precio' => 25.00, 'existencia' => 1500],
    ['codigo' => 'PROD-002', 'descripcion' => 'Agua Purificada 1L', 'estatus' => 'Activo', 'linea' => 'Bebidas', 'precio' => 12.00, 'existencia' => 3200],
    ['codigo' => 'PROD-003', 'descripcion' => 'Galletas de Chocolate 120g', 'estatus' => 'Activo', 'linea' => 'Abarrotes', 'precio' => 18.50, 'existencia' => 850],
    ['codigo' => 'PROD-004', 'descripcion' => 'Papas Fritas Clásicas 50g', 'estatus' => 'Inactivo', 'linea' => 'Botanas', 'precio' => 15.00, 'existencia' => 2100],
    ['codigo' => 'PROD-005', 'descripcion' => 'Refresco de Cola 600ml', 'estatus' => 'Activo', 'linea' => 'Bebidas', 'precio' => 18.00, 'existencia' => 4500],
    ['codigo' => 'PROD-006', 'descripcion' => 'Jugo de Naranja 1L', 'estatus' => 'Activo', 'linea' => 'Bebidas', 'precio' => 22.00, 'existencia' => 1000],
    ['codigo' => 'PROD-007', 'descripcion' => 'Cacahuate Japonés 100g', 'estatus' => 'Activo', 'linea' => 'Botanas', 'precio' => 10.00, 'existencia' => 5000],
];

state([
    'productos' => $mockProductos,
    'productosFiltrados' => $mockProductos,
    'search' => '',
]);

$aplicarFiltros = function () {
    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $this->productosFiltrados = collect($this->productos)->filter(function ($producto) use ($searchQuery) {
            return str_contains(strtolower($producto['codigo']), $searchQuery) ||
                str_contains(strtolower($producto['descripcion']), $searchQuery);
        })->values()->toArray();
    } else {
        $this->productosFiltrados = $this->productos;
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
            <h1 class="text-xl font-semibold text-gray-800">Productos</h1>
        </div>

        {{-- Top Filters Row (Material Style) --}}
        <div class="flex flex-wrap items-end gap-6 mb-6 px-1">
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

            <div class="w-48 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Linea</label>
                <select class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer">
                    <option></option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="w-48 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Ordenar por</label>
                <select class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer">
                    <option></option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="w-48 relative border-b border-gray-300 pb-1">
                <label class="block text-[10px] text-gray-400 mb-0.5">Linea Familia</label>
                <select class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer">
                    <option></option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>

            <div class="w-48 relative border-b border-gray-300 pb-1 ml-auto">
                <label class="block text-[10px] text-gray-400 mb-0.5">Estatus</label>
                <select class="w-full appearance-none bg-transparent text-xs text-gray-700 outline-none cursor-pointer">
                    <option>Todos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-end pb-1.5 text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Toolbar Row --}}
        <div class="flex items-center justify-between mb-2">
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
        <div class="flex-1 overflow-auto border-t border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-white">
                    <tr>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 w-16 text-center shadow-sm">
                            Acciones</th>
                        <th class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm">
                            Clave</th>
                        <th class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm">
                            Nombre</th>
                        <th class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm">
                            Estatus</th>
                        <th class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm">
                            Línea</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm text-right">
                            Precio Unitario</th>
                        <th
                            class="px-3 py-2 text-[10px] font-bold text-gray-800 border-b border-gray-200 shadow-sm text-right">
                            Existencia</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-700">
                    @forelse($productosFiltrados as $producto)
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
                            <td class="px-3 py-2 uppercase text-[11px] truncate max-w-[120px]">{{ $producto['codigo'] }}
                            </td>
                            <td class="px-3 py-2 uppercase text-[11px] truncate max-w-[200px]">
                                {{ $producto['descripcion'] }}</td>
                            <td class="px-3 py-2 text-[11px] capitalize">{{ $producto['estatus'] }}</td>
                            <td class="px-3 py-2 uppercase text-[11px] truncate max-w-[120px]">{{ $producto['linea'] }}</td>
                            <td class="px-3 py-2 text-right text-[11px]">${{ number_format($producto['precio'], 2) }}</td>
                            <td class="px-3 py-2 text-right text-[11px]">{{ $producto['existencia'] }} pz</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-8 text-center text-gray-400 text-sm">
                                No se encontraron productos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Pagination --}}
        <div class="py-2.5 flex items-center justify-end text-xs text-gray-600 gap-4 mt-1 border-t border-gray-200">
            <div class="flex items-center gap-1">
                <span>25 Filas por Página</span>
                <svg class="w-3.5 h-3.5 text-gray-500 cursor-pointer" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="flex items-center gap-3">
                <button class="text-gray-400 hover:text-gray-600 cursor-pointer"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z" />
                    </svg></button>
                <button class="text-gray-400 hover:text-gray-600 cursor-pointer"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6 1.41-1.41z" />
                    </svg></button>
                <span class="tracking-wide text-[10px]">1-25 of 46</span>
                <button class="text-gray-600 hover:text-gray-800 cursor-pointer"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                    </svg></button>
                <button class="text-gray-600 hover:text-gray-800 cursor-pointer"><svg class="w-3.5 h-3.5"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z" />
                    </svg></button>
            </div>
        </div>

    </div>
</div>