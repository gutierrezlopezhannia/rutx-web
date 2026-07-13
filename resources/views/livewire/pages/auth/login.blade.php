<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <!-- Envelope Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <x-text-input wire:model="form.email" 
                          id="email" 
                          type="email" 
                          name="email" 
                          placeholder="Correo electrónico" 
                          required 
                          autofocus 
                          autocomplete="username" 
                          class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition duration-150" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <!-- Lock Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <x-text-input wire:model="form.password" 
                          id="password" 
                          type="password" 
                          name="password" 
                          placeholder="Contraseña" 
                          required 
                          autocomplete="current-password" 
                          class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition duration-150" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Forgot Password Link -->
        @if (Route::has('password.request'))
            <div class="text-center">
                <a class="text-xs text-gray-400 hover:text-gray-600 transition duration-150" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        @endif

        <!-- Submit Button -->
        <div>
            <x-primary-button class="w-full justify-center bg-[#004066] hover:bg-[#00314f] active:bg-[#00273f] focus:ring-[#004066] text-white py-3 rounded-lg font-semibold text-sm normal-case tracking-normal transition duration-150">
                Iniciar Sesión
            </x-primary-button>
        </div>
    </form>
</div>
