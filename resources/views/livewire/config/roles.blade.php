<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

$roles = ['Administrador', 'Supervisor', 'Vendedor', 'Cobrador'];

$modulos = [
    'Dashboard' => ['Ver resumen', 'Ver métricas avanzadas'],
    'Ventas' => ['Ver ventas', 'Crear venta', 'Editar venta', 'Eliminar venta', 'Aprobar descuentos'],
    'Cobranza' => ['Ver cobranza', 'Registrar pago', 'Anular recibo'],
    'Inventario' => ['Ver stock', 'Ajuste de inventario', 'Transferencias'],
    'Configuración' => ['Gestión de usuarios', 'Asignación de roles', 'Configuración general'],
];

// Mock de permisos asignados [Modulo][Permiso][Rol] => boolean
$permisosMock = [];
foreach ($modulos as $modulo => $permisos) {
    foreach ($permisos as $permiso) {
        foreach ($roles as $rol) {
            // Administrador tiene todo por defecto, otros aleatorio o especifico para la demo
            if ($rol === 'Administrador') {
                $permisosMock[$modulo][$permiso][$rol] = true;
            } else if ($rol === 'Vendedor' && in_array($modulo, ['Ventas', 'Dashboard'])) {
                $permisosMock[$modulo][$permiso][$rol] = true;
            } else if ($rol === 'Cobrador' && $modulo === 'Cobranza') {
                $permisosMock[$modulo][$permiso][$rol] = true;
            } else if ($rol === 'Supervisor' && in_array($modulo, ['Dashboard', 'Ventas', 'Cobranza', 'Inventario'])) {
                $permisosMock[$modulo][$permiso][$rol] = true;
            } else {
                $permisosMock[$modulo][$permiso][$rol] = false;
            }
        }
    }
}

state([
    'roles' => $roles,
    'modulos' => $modulos,
    'permisos' => $permisosMock,
]);

$togglePermiso = function ($modulo, $permiso, $rol) {
    $this->permisos[$modulo][$permiso][$rol] = !$this->permisos[$modulo][$permiso][$rol];
};

?>

<div>
    <div class="py-4">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="flex items-center text-xs text-gray-500 mb-6 px-1">
                <span>Configuración</span>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-[#003859] font-bold">Asignación de Roles</span>
            </div>

            {{-- Main Container Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200/80 p-5">
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Matriz de Roles y Permisos</h2>
                        <p class="text-sm text-gray-500">Configura los accesos a los distintos módulos del sistema por rol.</p>
                    </div>

                    <button class="px-4 py-2 bg-[#003859] text-white rounded-lg text-sm font-semibold hover:bg-[#002840] transition duration-150">
                        Guardar Cambios
                    </button>
                </div>

                <div class="overflow-x-auto border border-gray-200/60 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200/80 text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-gray-200/60 w-1/3">
                                    Módulos / Permisos
                                </th>
                                @foreach($this->roles as $rol)
                                <th class="px-4 py-4 text-xs font-bold text-gray-700 uppercase tracking-wider text-center border-r border-gray-200/60 last:border-r-0">
                                    {{ $rol }}
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white">
                            @foreach($this->modulos as $modulo => $permisos)
                                {{-- Module Row (Header for permissions) --}}
                                <tr class="bg-gray-50/30">
                                    <td colspan="{{ count($this->roles) + 1 }}" class="px-6 py-2 text-sm font-semibold text-gray-800 border-t border-gray-200">
                                        {{ $modulo }}
                                    </td>
                                </tr>
                                
                                {{-- Permissions Rows --}}
                                @foreach($permisos as $permiso)
                                <tr class="hover:bg-blue-50/20 transition duration-150">
                                    <td class="px-6 py-3 text-sm text-gray-600 border-r border-gray-200/60 pl-10">
                                        {{ $permiso }}
                                    </td>
                                    @foreach($this->roles as $rol)
                                    <td class="px-4 py-3 text-center border-r border-gray-200/60 last:border-r-0">
                                        <div class="flex justify-center items-center h-full">
                                            <input type="checkbox" 
                                                   class="w-4 h-4 text-[#003859] bg-gray-100 border-gray-300 rounded focus:ring-[#003859] cursor-pointer"
                                                   wire:click="togglePermiso('{{ $modulo }}', '{{ $permiso }}', '{{ $rol }}')"
                                                   @if($this->permisos[$modulo][$permiso][$rol]) checked @endif>
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>
