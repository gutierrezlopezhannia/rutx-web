<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('productos', 'productos.index')
    ->middleware(['auth'])
    ->name('productos.index');

Volt::route('productos/lineas', 'productos.lineas.index')
    ->middleware(['auth'])
    ->name('productos.lineas');

Volt::route('productos/politicas', 'productos.politicas.index')
    ->middleware(['auth'])
    ->name('productos.politicas');

Volt::route('productos/reporte', 'productos.reporte')
    ->middleware(['auth'])
    ->name('productos.reporte');
