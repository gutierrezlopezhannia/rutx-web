<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('cobranza', 'cobranza.index')
    ->middleware(['auth'])
    ->name('ventas.cobranza');

Volt::route('utilidad', 'ventas.utilidad')
    ->middleware(['auth'])
    ->name('ventas.utilidad');

Volt::route('levantamiento', 'ventas.levantamiento')
    ->middleware(['auth'])
    ->name('ventas.levantamiento');

Volt::route('pedidos', 'ventas.pedidos')
    ->middleware(['auth'])
    ->name('ventas.pedidos');

Volt::route('ventas-cliente', 'ventas.ventas-cliente')
    ->middleware(['auth'])
    ->name('ventas.ventas-cliente');


Volt::route('clientes-pendientes', 'ventas.clientes-pendientes')
    ->middleware(['auth'])
    ->name('ventas.clientes-pendientes');

Volt::route('reporte-rentabilidad-ruta', 'ventas.rentabilidad-ruta')
    ->middleware(['auth'])
    ->name('ventas.rentabilidad-ruta');

Volt::route('visor', 'ventas.visor')
    ->middleware(['auth'])
    ->name('ventas.visor');

Volt::route('reportes-globales', 'ventas.reportes-globales')
    ->middleware(['auth'])
    ->name('ventas.reportes-globales');

Volt::route('deposito-venta', 'ventas.deposito-venta')
    ->middleware(['auth'])
    ->name('ventas.deposito-venta');
