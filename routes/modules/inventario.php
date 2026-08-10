<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para el módulo de Inventario
Route::middleware(['auth', 'verified'])->prefix('inventario')->name('inventario.')->group(function () {
    Volt::route('/', 'inventario.index')->name('index');
    Volt::route('/cierre', 'inventario.cierre')->name('cierre');
    Volt::route('/mermas', 'inventario.mermas')->name('mermas');
    Volt::route('/ruta', 'inventario.ruta')->name('ruta');
    Volt::route('/entrada-almacen', 'inventario.entrada-almacen')->name('entrada-almacen');
    Volt::route('/existencias-globales', 'inventario.existencias-globales')->name('existencias-globales');
    Volt::route('/carga-entrega', 'inventario.carga-entrega')->name('carga-entrega');
    Volt::route('/historico', 'inventario.historico')->name('historico');
    Volt::route('/carga-imovil-grid', 'inventario.carga-imovil-grid')->name('carga-imovil-grid');
});
