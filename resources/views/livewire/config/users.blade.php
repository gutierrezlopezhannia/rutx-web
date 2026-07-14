<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$mockUsers = [
    ['id' => 1, 'name' => 'Eduardo Gutierrez', 'email' => 'eduardo@rutx.com', 'role' => 'Administrador', 'status' => 'Activo', 'last_login' => '2026-07-14 10:30 AM'],
    ['id' => 2, 'name' => 'Ana María', 'email' => 'ana@rutx.com', 'role' => 'Vendedor', 'status' => 'Activo', 'last_login' => '2026-07-14 09:15 AM'],
    ['id' => 3, 'name' => 'Carlos Díaz', 'email' => 'carlos@rutx.com', 'role' => 'Vendedor', 'status' => 'Inactivo', 'last_login' => '2026-07-10 04:45 PM'],
    ['id' => 4, 'name' => 'Jorge Pérez', 'email' => 'jorge@rutx.com', 'role' => 'Supervisor', 'status' => 'Activo', 'last_login' => '2026-07-13 11:20 AM'],
];

state([
    'users' => $mockUsers,
    'search' => '',
]);

$filteredUsers = function () {
    if (empty($this->search)) {
        return $this->users;
    }

    $searchQuery = strtolower(trim($this->search));
    return collect($this->users)->filter(function ($user) use ($searchQuery) {
        return str_contains(strtolower($user['name']), $searchQuery) ||
               str_contains(strtolower($user['email']), $searchQuery) ||
               str_contains(strtolower($user['role']), $searchQuery);
    })->toArray();
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Configuración</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Usuarios</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">
                
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Gestión de Usuarios</h2>
                        <p class="text-sm text-gray-500">Administra los usuarios del sistema y su estado.</p>
                    </div>

                    <div class="flex w-full lg:w-auto items-center gap-3">
                        {{-- Search Input --}}
                        <div class="relative w-full lg:w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" 
                                   wire:model.live="search" 
                                   placeholder="Buscar usuario..." 
                                   class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#003859] focus:border-transparent bg-white text-gray-700 font-medium placeholder-gray-400" />
                        </div>

                        <button class="px-4 py-2 bg-[#003859] text-white rounded-lg text-sm font-semibold hover:bg-[#002840] transition duration-150 whitespace-nowrap">
                            + Nuevo Usuario
                        </button>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Usuario</th>
                                <th class="px-6 py-4">Rol</th>
                                <th class="px-6 py-4">Estado</th>
                                <th class="px-6 py-4">Último Acceso</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white text-sm text-gray-700">
                            @forelse($this->filteredUsers() as $user)
                            <tr class="hover:bg-gray-50/30 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mr-3">
                                            {{ substr($user['name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $user['name'] }}</div>
                                            <div class="text-xs text-gray-500">{{ $user['email'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $user['role'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user['status'] === 'Activo')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $user['last_login'] }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    <button class="text-[#003859] hover:text-blue-900 mx-1">Editar</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    No se encontraron usuarios.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
