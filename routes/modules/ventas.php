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

Volt::route('nuevo-gasto-op', 'ventas.nuevo-gasto-op')
    ->middleware(['auth'])
    ->name('ventas.nuevo-gasto-op');

Volt::route('top-clientes', 'ventas.top-clientes')
    ->middleware(['auth'])
    ->name('ventas.top-clientes');

Volt::route('reporte-productos-rechazados', 'ventas.rechazos-campo')
    ->middleware(['auth'])
    ->name('ventas.rechazos-campo');

Volt::route('reporte-preventa-entrega', 'ventas.reporte-preventa-entrega')
    ->middleware(['auth'])
    ->name('ventas.preventa-entrega');

Volt::route('carga-imovil-grid', 'ventas.carga-imovil-grid')
    ->middleware(['auth'])
    ->name('ventas.carga-imovil-grid');


