<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockUsers = [
    ['id' => 1, 'nombre' => 'Usuario Prueba', 'correo' => 'prueba@gmail.com', 'estatus' => 'Habilitado', 'usuario' => 'UPrueba', 'nombre_rol' => 'Administrador'],
    ['id' => 2, 'nombre' => 'Eduardo Gutierrez', 'correo' => 'eduardo@rutx.com', 'estatus' => 'Habilitado', 'usuario' => 'EGutierrez', 'nombre_rol' => 'Administrador'],
    ['id' => 3, 'nombre' => 'Ana María', 'correo' => 'ana@rutx.com', 'estatus' => 'Habilitado', 'usuario' => 'AMaria', 'nombre_rol' => 'Vendedor'],
    ['id' => 4, 'nombre' => 'Carlos Díaz', 'correo' => 'carlos@rutx.com', 'estatus' => 'Deshabilitado', 'usuario' => 'CDiaz', 'nombre_rol' => 'Vendedor'],
];

state([
    'users' => $mockUsers,
    'search' => '',
    'filtro_estado' => 'Activos',
]);

$filteredUsers = function () {
    $filtered = collect($this->users);

    if ($this->filtro_estado === 'Activos') {
        $filtered = $filtered->where('estatus', 'Habilitado');
    } elseif ($this->filtro_estado === 'Inactivos') {
        $filtered = $filtered->where('estatus', 'Deshabilitado');
    }

    if (!empty($this->search)) {
        $searchQuery = strtolower(trim($this->search));
        $filtered = $filtered->filter(function ($user) use ($searchQuery) {
            return str_contains(strtolower($user['nombre']), $searchQuery) ||
                   str_contains(strtolower($user['correo']), $searchQuery) ||
                   str_contains(strtolower($user['usuario']), $searchQuery) ||
                   str_contains(strtolower($user['nombre_rol']), $searchQuery);
        });
    }

    return $filtered->toArray();
};

?>

<div class="h-full bg-transparent p-6 font-sans">
    <div class="max-w-[1400px] mx-auto bg-white dark:bg-gray-800 rounded shadow-sm">
        
        {{-- Header Title --}}
        <div class="px-6 py-5">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Usuarios</h1>
        </div>

        {{-- Toolbar --}}
        <div class="px-6 py-2 flex flex-col md:flex-row md:items-center justify-between border-b border-gray-100 gap-4">
            {{-- Left: Status Dropdown --}}
            <div class="w-full md:w-48 relative">
                <select wire:model.live="filtro_estado" class="appearance-none w-full bg-transparent border-0 border-b border-gray-300 dark:border-gray-600 px-0 py-2 text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-0 focus:border-gray-500 cursor-pointer">
                    <option value="Activos">Activos</option>
                    <option value="Inactivos">Inactivos</option>
                    <option value="Todos">Todos</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </div>
            </div>

            {{-- Right: Actions & Search --}}
            <div class="flex items-center gap-4 text-gray-500">
                
                {{-- Search Input --}}
                <div class="relative flex items-center w-full md:w-64 border-b border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5 text-gray-400 absolute left-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="Buscar ..." 
                           class="w-full bg-transparent border-none focus:ring-0 pl-7 pr-7 py-2 text-sm text-gray-600 dark:text-gray-300 placeholder-gray-400" />
                    @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-0 p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full cursor-pointer">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>

                {{-- Action Icons --}}
                <div class="flex items-center gap-3 shrink-0">
                    <button class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-400 transition" title="Columnas">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 4h3v16H5V4zm6 0h3v16h-3V4zm6 0h3v16h-3V4z" />
                        </svg>
                    </button>
                    <button class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-400 transition" title="Exportar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </button>
                    <button class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-400 transition" title="Refrescar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-gray-600 dark:text-gray-400 transition" title="Agregar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-left bg-white dark:bg-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide w-32">Acciones</th>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide">Nombre</th>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide">Correo</th>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide">Estatus</th>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide">Usuario</th>
                        <th class="px-6 py-4 text-[13px] font-bold text-gray-700 dark:text-gray-200 tracking-wide">Nombre de Rol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($this->filteredUsers() as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                        {{-- Acciones --}}
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                {{-- Compass/Permissions icon --}}
                                <button class="text-gray-500 hover:text-gray-700 transition" title="Permisos">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3.36 7.47l-1.39 5.86c-.15.65-.77 1.11-1.44 1.11H12l-5.86-1.39a1.49 1.49 0 01-1.11-1.44L6.42 7.75c.15-.65.77-1.11 1.44-1.11H12l5.86 1.39c.63.15 1.09.77 1.11 1.44zM12 10.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" />
                                    </svg>
                                </button>
                                {{-- Edit icon --}}
                                <button class="text-blue-600 hover:text-blue-800 transition" title="Editar">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a.996.996 0 000-1.41l-2.34-2.34a.996.996 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                    </svg>
                                </button>
                                {{-- Delete icon --}}
                                <button class="text-red-500 hover:text-red-700 transition" title="Eliminar">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                        
                        {{-- Data --}}
                        <td class="px-6 py-3 text-[13px] text-gray-800 dark:text-gray-300 whitespace-nowrap">{{ $user['nombre'] }}</td>
                        <td class="px-6 py-3 text-[13px] text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $user['correo'] }}</td>
                        <td class="px-6 py-3 text-[13px] text-gray-800 dark:text-gray-300 whitespace-nowrap">{{ $user['estatus'] }}</td>
                        <td class="px-6 py-3 text-[13px] text-gray-800 dark:text-gray-300 whitespace-nowrap">{{ $user['usuario'] }}</td>
                        <td class="px-6 py-3 text-[13px] text-gray-800 dark:text-gray-300 whitespace-nowrap">{{ $user['nombre_rol'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                            No se encontraron registros.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer / Pagination --}}
        <div class="px-6 py-3 flex items-center justify-end text-sm text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 gap-6">
            <div class="flex items-center gap-2">
                <span>100 Filas por Página</span>
                <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
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
                
                <span class="text-[13px] mx-1">1-{{ count($this->filteredUsers()) }} of {{ count($this->filteredUsers()) }}</span>
                
                {{-- Next Page --}}
                <button class="text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6-1.41-1.41z" />
                    </svg>
                </button>
                {{-- Last Page --}}
                <button class="text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z" />
                    </svg>
                </button>
            </div>
        </div>

    </div>
</div>
