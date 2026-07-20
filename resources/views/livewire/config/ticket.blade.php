<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state([
    'empresa' => 'PRUEB',
    'direccion' => 'DIR',
    'colonia' => 'COL',
    'codigo_postal' => '12345',
    'estado' => 'QRO',
    'ciudad' => 'CITY',
    'rfc' => 'RFC',
    'telefono' => '4428531033',
]);

$guardar = function () {
    // Aquí iría la lógica para guardar el ticket
    $this->dispatch('ticket-guardado');
};

?>

<div class="h-full bg-white dark:bg-gray-900 p-6 font-sans transition-colors duration-300">
    <div class="max-w-5xl mx-auto">
        
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Ticket</h1>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            {{-- Row 1 --}}
            <div class="col-span-1 md:col-span-12">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Empresa</legend>
                    <input type="text" wire:model="empresa" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>

            {{-- Row 2 --}}
            <div class="col-span-1 md:col-span-6">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Dirección</legend>
                    <input type="text" wire:model="direccion" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>
            <div class="col-span-1 md:col-span-6">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Colonia</legend>
                    <input type="text" wire:model="colonia" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>

            {{-- Row 3 --}}
            <div class="col-span-1 md:col-span-4">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Código Postal</legend>
                    <input type="text" wire:model="codigo_postal" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>
            <div class="col-span-1 md:col-span-4">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Estado</legend>
                    <input type="text" wire:model="estado" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>
            <div class="col-span-1 md:col-span-4">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Ciudad</legend>
                    <input type="text" wire:model="ciudad" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>

            {{-- Row 4 --}}
            <div class="col-span-1 md:col-span-6">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">RFC</legend>
                    <input type="text" wire:model="rfc" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>
            <div class="col-span-1 md:col-span-6">
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Teléfono</legend>
                    <input type="text" wire:model="telefono" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>
            </div>
        </div>

        {{-- Botón Guardar --}}
        <div class="pt-8 flex items-center gap-4">
            <button wire:click="guardar" class="bg-[#2463eb] hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition duration-150 text-sm cursor-pointer">
                Guardar
            </button>
            <div x-data="{ shown: false, timeout: null }"
                 x-on:ticket-guardado.window="clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000);"
                 x-show="shown"
                 x-transition.opacity.duration.1500ms
                 style="display: none;"
                 class="text-sm text-green-600 dark:text-green-400 font-medium">
                Configuración guardada.
            </div>
        </div>

    </div>
</div>
