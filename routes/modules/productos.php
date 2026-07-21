<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('productos/reporte', 'productos.reporte')
    ->middleware(['auth'])
    ->name('productos.reporte');
