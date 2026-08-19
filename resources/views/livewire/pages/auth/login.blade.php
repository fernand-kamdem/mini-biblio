<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public LoginForm $form;
    public bool $showPassword = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <!-- En-tête -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Connexion</h2>
        <p class="mt-2 text-sm text-gray-600">Accédez à votre espace bibliothèque</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input 
                    wire:model="form.email" 
                    id="email" 
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="votre@email.com"
                />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Mot de passe')" class="text-sm font-medium text-gray-700" />
                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
            </div>
            
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input 
                    wire:model="form.password" 
                    id="password" 
                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                    type="{{ $showPassword ? 'text' : 'password' }}" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <button type="button" wire:click="$toggle('showPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center cursor-pointer">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-2 transition duration-150" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-600">{{ __('Se souvenir de moi') }}</span>
            </label>
        </div>

        <!-- Submit Button -->

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="login"
            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="login">
                <i class="fas fa-sign-in-alt mr-2"></i>
                {{ __('Se connecter') }}
            </span>
            <span wire:loading wire:target="login">
                {{-- spinner --}}
                Connexion en cours...
            </span>
        </button>

        <!-- Lien d'inscription -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Pas encore de compte ? 
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" wire:navigate>
                    Créer un compte
                </a>
            </p>
        </div>
    </form>
</div>

