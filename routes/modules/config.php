<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Rutas para el módulo de Configuración
Route::prefix('config')->name('config.')->middleware(['auth'])->group(function () {
    Volt::route('users', 'config.users')->name('users');
    Volt::route('roles', 'config.roles')->name('roles');
    Volt::route('zonas', 'config.zonas')->name('zonas');
    Volt::route('campos-adicionales', 'config.campos-adicionales')->name('campos-adicionales');
    Volt::route('ajustes', 'config.ajustes')->name('ajustes');
    Volt::route('ticket', 'config.ticket')->name('ticket');
});
