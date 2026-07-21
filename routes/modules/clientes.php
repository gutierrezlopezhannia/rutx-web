<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('clientes')->name('clientes.')->middleware(['auth'])->group(function () {
    Volt::route('/', 'clientes.index')->name('index');
    Volt::route('cadena', 'clientes.cadena')->name('cadena');
    Volt::route('credito', 'clientes.credito')->name('credito');
    Volt::route('traspaso', 'clientes.traspaso')->name('traspaso');
});

