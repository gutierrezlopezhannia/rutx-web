<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    Session::flash('status', __($status));
};

?>

<div>
    <div class="mb-5 text-xs text-gray-500 leading-relaxed text-center">
        ¿Olvidaste tu contraseña? Escribe tu correo electrónico para enviarte un enlace de restablecimiento de contraseña.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5">
        <!-- Email Address -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                <!-- Envelope Icon -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <x-text-input wire:model="email" 
                          id="email" 
                          type="email" 
                          name="email" 
                          placeholder="Correo electrónico" 
                          required 
                          autofocus 
                          class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none focus:border-[#004066] focus:ring-1 focus:ring-[#004066] transition duration-150" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Return to Login Link -->
        <div class="text-center">
            <a class="text-xs text-gray-400 hover:text-gray-600 transition duration-150" href="{{ route('login') }}" wire:navigate>
                Regresar al inicio de sesión
            </a>
        </div>

        <!-- Submit Button -->
        <div>
            <x-primary-button class="w-full justify-center bg-[#004066] hover:bg-[#00314f] active:bg-[#00273f] focus:ring-[#004066] text-white py-3 rounded-lg font-semibold text-sm normal-case tracking-normal transition duration-150">
                Enviar Enlace
            </x-primary-button>
        </div>
    </form>
</div>
