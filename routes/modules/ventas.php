<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('cobranza', 'cobranza.index')
    ->middleware(['auth'])
    ->name('ventas.cobranza');


Volt::route('levantamiento', 'ventas.levantamiento')
    ->middleware(['auth'])
    ->name('ventas.levantamiento');
