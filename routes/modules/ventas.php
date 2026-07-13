<?php

use Illuminate\Support\Facades\Route;

Route::view('cobranza', 'livewire.cobranza.index')
    ->middleware(['auth'])
    ->name('ventas.cobranza');
