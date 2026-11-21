<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
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

    /**
     * Démonstration : connexion en un clic avec l'un des comptes publics de config/demo.php.
     */
    public function loginAsDemo(string $account): void
    {
        $demoAccount = config("demo.accounts.{$account}");

        abort_unless(config('demo.enabled') && is_array($demoAccount), 404);

        $this->form->email = $demoAccount['email'];
        $this->form->password = config('demo.password');

        $this->login();
    }
}; ?>

@section('title', config('app.name') . ' | ' . __('Login'))
<div class="space-y-6">
    <!-- En-tête -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Login') }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('Access your library space') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (config('demo.enabled'))
        <!-- Comptes de démonstration -->
        <section class="rounded-xl border border-indigo-200 bg-indigo-50 p-4" aria-labelledby="titre-demo">
            <h3 id="titre-demo" class="text-sm font-semibold text-indigo-900">
                <i class="fas fa-flask mr-1"></i>
                {{ __('Try the demo') }}
            </h3>
            <p class="mt-1 text-xs text-indigo-800">
                {{ __('Choose a role to sign in. Fictitious data, reset every night.') }}
            </p>
            <div class="mt-3 grid gap-2 sm:grid-cols-3">
                @foreach (config('demo.accounts') as $key => $account)
                    <button
                        type="button"
                        wire:click="loginAsDemo('{{ $key }}')"
                        wire:loading.attr="disabled"
                        class="rounded-lg border border-indigo-200 bg-white px-3 py-2 text-left transition hover:border-indigo-400 hover:shadow-sm disabled:opacity-60"
                    >
                        <span class="block text-sm font-medium text-gray-900">
                            {{ match ($account['role']) {
                                'ADMIN' => __('Administrator'),
                                'BIBLIOTHECAIRE' => __('Librarian'),
                                default => __('Member'),
                            } }}
                        </span>
                        <span class="block truncate text-xs text-gray-500">{{ $account['email'] }}</span>
                    </button>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-indigo-800">
                {{ __('Password for every demo account:') }}
                <code class="rounded bg-white px-1.5 py-0.5 font-semibold text-indigo-900">{{ config('demo.password') }}</code>
            </p>
        </section>
    @endif

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
                    placeholder="{{ __('you@example.com') }}"
                />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot Password?') }}
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
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
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
                {{ __('Log in') }}
            </span>
            <span wire:loading wire:target="login">
                {{ __('Signing in...') }}
            </span>
        </button>

        <!-- Lien d'inscription -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                {{ __("Don't have an account yet?") }}
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" wire:navigate>
                    {{ __('Create account') }}
                </a>
            </p>
        </div>
    </form>
</div>

