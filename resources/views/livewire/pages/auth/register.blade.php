<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $showPassword = false;

    public bool $showPasswordConfirmation = false;

    public function passwordStrength(): array
    {
        $password = $this->password;
        $score = 0;

        if ($password === '') {
            return ['percentage' => 0, 'label' => '—', 'bar' => 'bg-gray-300', 'text' => 'text-gray-400'];
        }

        if (strlen($password) >= 8) {
            $score++;
        }
        if (strlen($password) >= 12) {
            $score++;
        }
        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        if (preg_match('/\d/', $password)) {
            $score++;
        }
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score++;
        }

        return match (true) {
            $score <= 1 => ['percentage' => 20, 'label' => 'Très faible', 'bar' => 'bg-red-500', 'text' => 'text-red-500'],
            $score === 2 => ['percentage' => 40, 'label' => 'Faible', 'bar' => 'bg-orange-400', 'text' => 'text-orange-500'],
            $score === 3 => ['percentage' => 60, 'label' => 'Moyen', 'bar' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
            $score === 4 => ['percentage' => 80, 'label' => 'Fort', 'bar' => 'bg-blue-500', 'text' => 'text-blue-500'],
            default => ['percentage' => 100, 'label' => 'Très fort', 'bar' => 'bg-green-500', 'text' => 'text-green-600'],
        };
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-6">
    <!-- En-tête -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Créer un compte</h2>
        <p class="mt-2 text-sm text-gray-600">Rejoignez notre bibliothèque numérique</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom complet')" class="text-sm font-medium text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <x-text-input 
                    wire:model="name" 
                    id="name" 
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                    type="text" 
                    name="name" 
                    required 
                    autofocus 
                    autocomplete="name"
                    placeholder="Jean Dupont"
                />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <x-text-input 
                    wire:model="email" 
                    id="email" 
                    class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                    type="email" 
                    name="email" 
                    required 
                    autocomplete="username"
                    placeholder="votre@email.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="text-sm font-medium text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <x-text-input 
                    wire:model.live="password"
                    id="password" 
                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                    type="{{ $showPassword ? 'text' : 'password' }}"
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="••••••••"
                />
                <button type="button" wire:click="$toggle('showPassword')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
            
            <!-- Indicateur de force du mot de passe -->
            @php $s = $this->passwordStrength(); @endphp
            <div class="mt-2 space-y-1">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500">Force du mot de passe :</span>
                    <span class="font-medium {{ $s['text'] }}">{{ $s['label'] }}</span>
                </div>
                <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full {{ $s['bar'] }} transition-all duration-300" style="width: {{ $s['percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-sm font-medium text-gray-700" />
            <div class="relative mt-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-check-circle text-gray-400"></i>
                </div>
                <x-text-input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150"
                    type="{{ $showPasswordConfirmation ? 'text' : 'password' }}"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirmez votre mot de passe"
                />
                <button type="button" wire:click="$toggle('showPasswordConfirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas {{ $showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="register"
            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="register">
                <i class="fas fa-user-plus mr-2"></i>
                {{ __('Créer mon compte') }}
            </span>
            <span wire:loading wire:target="register">
                Création en cours...
            </span>
        </button>

        <!-- Lien de connexion -->
        <div class="text-center pt-2">
            <p class="text-sm text-gray-600">
                Vous avez déjà un compte ? 
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" wire:navigate>
                    Se connecter
                </a>
            </p>
        </div>
    </form>
</div>

