<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para el módulo de Rutas
Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('/ruta/rutas', 'rutas.simulacion')->name('ruta.rutas');
});
