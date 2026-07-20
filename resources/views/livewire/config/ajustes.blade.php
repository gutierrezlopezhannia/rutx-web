<?php

use function Livewire\Volt\{state, layout};

layout('layouts.app');

state([
    'organizacion' => 'Prueba',
    'idioma' => 'Español',
    'tema_predeterminado' => 'Light',
    'tipo_ticket' => 'General',
    'exportar_devolucion' => true,
    'exportar_cargas' => true,
    'exportar_entradas' => true,
    'exportar_levantamientos' => true,
    'texto_carga' => 'Empty',
    'texto_devolucion' => 'Empty',
]);

$guardar = function () {
    // Aquí iría la lógica para guardar en la base de datos
    $this->dispatch('ajustes-guardados');
};

?>

<div x-data
     x-init="
        if (localStorage.getItem('tema_rutx')) {
            $wire.tema_predeterminado = localStorage.getItem('tema_rutx');
        }
        document.documentElement.classList.toggle('dark', $wire.tema_predeterminado === 'Dark');
     "
     class="h-full font-sans transition-colors duration-300">
    
    <div class="min-h-full bg-white dark:bg-gray-900 p-6 rounded shadow-sm">
        <div class="max-w-4xl mx-auto">
            
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Ajustes</h1>

            <div class="space-y-6">
                {{-- Organización --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Organización</legend>
                    <input type="text" wire:model="organizacion" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent" />
                </fieldset>

                {{-- Idioma --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors relative">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Idioma</legend>
                    <select wire:model="idioma" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent appearance-none cursor-pointer">
                        <option value="Español">Español</option>
                        <option value="Inglés">Inglés</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center pt-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </div>
                </fieldset>

                {{-- Tema Predeterminado --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors relative">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Tema Predeterminado</legend>
                    <select wire:model.live="tema_predeterminado" x-on:change="localStorage.setItem('tema_rutx', $event.target.value); document.documentElement.classList.toggle('dark', $event.target.value === 'Dark');" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent appearance-none cursor-pointer">
                        <option value="Light" class="text-gray-900">Light</option>
                        <option value="Dark" class="text-gray-900">Dark</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center pt-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </div>
                </fieldset>

                {{-- Tipo de Ticket --}}
                <fieldset class="border border-gray-300 dark:border-gray-600 rounded px-3 pb-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition-colors relative">
                    <legend class="text-[11px] text-gray-500 dark:text-gray-400 px-1">Tipo de Ticket</legend>
                    <select wire:model="tipo_ticket" class="w-full border-none p-0 text-sm focus:ring-0 text-gray-900 dark:text-white bg-transparent appearance-none cursor-pointer">
                        <option value="General">General</option>
                        <option value="Detallado">Detallado</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center pt-2 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </div>
                </fieldset>

                {{-- Checkboxes --}}
                <div class="space-y-4 pt-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="exportar_devolucion" class="w-4 h-4 text-blue-600 bg-transparent border-gray-400 dark:border-gray-500 rounded focus:ring-blue-500">
                        <span class="text-[15px] text-gray-900 dark:text-gray-200">Exportar Devolución Ruta a Almacén</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="exportar_cargas" class="w-4 h-4 text-blue-600 bg-transparent border-gray-400 dark:border-gray-500 rounded focus:ring-blue-500">
                        <span class="text-[15px] text-gray-900 dark:text-gray-200">Exportar Cargas a Ruta</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="exportar_entradas" class="w-4 h-4 text-blue-600 bg-transparent border-gray-400 dark:border-gray-500 rounded focus:ring-blue-500">
                        <span class="text-[15px] text-gray-900 dark:text-gray-200">Exportar Entradas a Almacén</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="exportar_levantamientos" class="w-4 h-4 text-blue-600 bg-transparent border-gray-400 dark:border-gray-500 rounded focus:ring-blue-500">
                        <span class="text-[15px] text-gray-900 dark:text-gray-200">Exportar Levantamientos de Venta</span>
                    </label>
                </div>

                {{-- Textareas --}}
                <div class="pt-4 space-y-6">
                    <div>
                        <label class="block text-[15px] text-gray-500 dark:text-gray-400 mb-1">Texto de Responsiva Carga a Ruta</label>
                        <textarea wire:model.live="texto_carga" rows="1" class="w-full border border-gray-300 dark:border-gray-600 rounded p-1.5 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none font-mono"></textarea>
                        <div class="text-[13px] text-gray-900 dark:text-gray-300 mt-1">{{ strlen($this->texto_carga) }}/800</div>
                    </div>

                    <div>
                        <label class="block text-[15px] text-gray-500 dark:text-gray-400 mb-1">Texto de Responsiva Devolución Ruta a Almacén</label>
                        <textarea wire:model.live="texto_devolucion" rows="1" class="w-full border border-gray-300 dark:border-gray-600 rounded p-1.5 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none font-mono"></textarea>
                        <div class="text-[13px] text-gray-900 dark:text-gray-300 mt-1">{{ strlen($this->texto_devolucion) }}/800</div>
                    </div>
                </div>

                {{-- Botón Guardar --}}
                <div class="pt-4 flex items-center gap-4">
                    <button wire:click="guardar" class="bg-[#2463eb] hover:bg-blue-700 text-white font-medium py-2 px-6 rounded text-sm transition cursor-pointer">
                        Guardar
                    </button>
                    
                    <div x-data="{ shown: false, timeout: null }"
                         x-on:ajustes-guardados.window="clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000);"
                         x-show="shown"
                         x-transition.opacity.duration.1500ms
                         style="display: none;"
                         class="text-sm text-green-600 dark:text-green-400 font-medium">
                        Configuración guardada.
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
