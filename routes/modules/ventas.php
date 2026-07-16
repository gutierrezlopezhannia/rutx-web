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
