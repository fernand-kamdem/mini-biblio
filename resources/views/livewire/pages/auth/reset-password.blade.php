<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';

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
            $score <= 1 => ['percentage' => 20, 'label' => __('Very weak'), 'bar' => 'bg-red-500', 'text' => 'text-red-500'],
            $score === 2 => ['percentage' => 40, 'label' => __('Weak'), 'bar' => 'bg-orange-400', 'text' => 'text-orange-500'],
            $score === 3 => ['percentage' => 60, 'label' => __('Fair'), 'bar' => 'bg-yellow-400', 'text' => 'text-yellow-600'],
            $score === 4 => ['percentage' => 80, 'label' => __('Strong'), 'bar' => 'bg-blue-500', 'text' => 'text-blue-500'],
            default => ['percentage' => 100, 'label' => __('Very strong'), 'bar' => 'bg-green-500', 'text' => 'text-green-600'],
        };
    }

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

@section('title', config('app.name') . ' | ' . __('Reset password'))
    <div class="space-y-6">
    <!-- En-tête -->
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-key text-2xl text-amber-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">{{ __('New password') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('Choose a secure password for your account') }}
        </p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ session('status') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit="resetPassword" class="space-y-5">
        <!-- Token caché -->
        <input type="hidden" name="token" value="{{ $token ?? '' }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-gray-700" />
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
                    autofocus 
                    autocomplete="username"
                    placeholder="votre@email.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('New password')" class="text-sm font-medium text-gray-700" />
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

            @php $s = $this->passwordStrength(); @endphp
            <div class="mt-2 space-y-1">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500">{{ __('Password strength:') }}</span>
                    <span class="font-medium {{ $s['text'] }}">{{ $s['label'] }}</span>
                </div>
                <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full {{ $s['bar'] }} transition-all duration-300" style="width: {{ $s['percentage'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
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
                    placeholder="{{ __('Confirm your password') }}"
                />
                <button type="button" wire:click="$toggle('showPasswordConfirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas {{ $showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Message de correspondance -->
        @if ($password && $password_confirmation && $password === $password_confirmation)
            <div class="flex items-center gap-2 text-sm text-green-600 bg-green-50 rounded-lg p-2 px-3 border border-green-200">
                <i class="fas fa-check-circle"></i>
                <span>{{ __('Passwords match') }}</span>
            </div>
        @elseif ($password && $password_confirmation && $password !== $password_confirmation)
            <div class="flex items-center gap-2 text-sm text-red-600 bg-red-50 rounded-lg p-2 px-3 border border-red-200">
                <i class="fas fa-times-circle"></i>
                <span>{{ __('Passwords do not match') }}</span>
            </div>
        @endif

        <!-- Submit Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="resetPassword"
            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-150 disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="resetPassword">
                <i class="fas fa-save mr-2"></i>
                {{ __('Reset Password') }}
            </span>
            <span wire:loading wire:target="resetPassword">
                {{ __('Resetting...') }}
            </span>
        </button>

        <!-- Lien de retour -->
        <div class="text-center pt-2">
            <p class="text-sm text-gray-600">
                <i class="fas fa-arrow-left mr-1 text-xs"></i>
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition duration-150" wire:navigate>
                    {{ __('Back to login') }}
                </a>
            </p>
        </div>
    </form>
</div>
