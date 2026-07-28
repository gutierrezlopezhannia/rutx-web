<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para el módulo de Rutas
Volt::route('/ruta/rutas', 'rutas.rutas')
    ->middleware(['auth', 'verified'])
    ->name('ruta.rutas');

Volt::route('/ruta/mapa-clientes', 'rutas.mapa-clientes')
    ->middleware(['auth', 'verified'])
    ->name('ruta.mapa-clientes');

Volt::route('/ruta/agenda', 'rutas.agenda')
    ->middleware(['auth', 'verified'])
    ->name('ruta.agenda');

Volt::route('/ruta/gastos-operativos', 'rutas.gastos-operativos')
    ->middleware(['auth', 'verified'])
    ->name('ruta.gastos-operativos');

