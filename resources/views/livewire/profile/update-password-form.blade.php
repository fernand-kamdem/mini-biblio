<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $showNewPassword = false;
    public bool $showCurrentPassword = false;
    public bool $showConfirmPassword = false;

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('status', 'password-updated');
    }

    public function resetPasswordForm(): void
    {
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

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
}; ?>

<div class="w-full max-w-full">
    <section class="w-full max-w-full space-y-6">
        <!-- En-tête -->
        <div class="w-full flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-lock mr-2 text-indigo-600"></i>
                    {{ __('Update Password') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Ensure your account is using a long, random password to stay secure.') }}
                </p>
            </div>
            <!-- Indicateur de sécurité -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-1.5 text-green-500"></i>
                    {{ __('Secure') }}
                </span>
            </div>
        </div>

        <form wire:submit="updatePassword" class="w-full space-y-6">
            <!-- Grille des champs -->
            <div class="w-full grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Mot de passe actuel -->
                <div class="w-full md:col-span-2">
                    <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <x-text-input 
                            wire:model="current_password" 
                            id="update_password_current_password" 
                            name="current_password" 
                            type="{{ $showCurrentPassword ? 'text' : 'password' }}" 
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                            autocomplete="current-password" 
                            placeholder="{{ __('Enter your current password') }}"
                        />
                        <button 
                            type="button" 
                            wire:click="$toggle('showCurrentPassword')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="fas {{ $showCurrentPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('current_password')" class="mt-1.5 text-xs text-red-600" />
                </div>

                <!-- Nouveau mot de passe -->
                <div class="w-full">
                    <x-input-label for="update_password_password" :value="__('New Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <x-text-input 
                            wire:model.live="password" 
                            id="update_password_password" 
                            name="password" 
                            type="{{ $showNewPassword ? 'text' : 'password' }}" 
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                            autocomplete="new-password" 
                            placeholder="{{ __('Enter new password') }}"
                        />
                        <button 
                            type="button" 
                            wire:click="$toggle('showNewPassword')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="fas {{ $showNewPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-600" />
                    
                    <!-- Password strength indicator -->
                    @if($password)
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
                    @endif

                </div>

                <!-- Confirmation -->
                <div class="w-full">
                    <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-gray-400"></i>
                        </div>
                        <x-text-input 
                            wire:model.live="password_confirmation" 
                            id="update_password_password_confirmation" 
                            name="password_confirmation" 
                            type="{{ $showConfirmPassword ? 'text' : 'password' }}" 
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                            autocomplete="new-password" 
                            placeholder="{{ __('Confirm your new password') }}"
                        />
                        <button 
                            type="button" 
                            wire:click="$toggle('showConfirmPassword')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="fas {{ $showConfirmPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs text-red-600" />
                    
                    <!-- Indicateur de correspondance -->
                    @if($password && $password_confirmation)
                        <div class="mt-1.5 text-xs">
                            @if($password === $password_confirmation)
                                <span class="text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ __('Passwords match') }}
                                </span>
                            @else
                                <span class="text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ __('Passwords do not match') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Conseils de sécurité -->
            <div class="w-full rounded-lg bg-blue-50 border border-blue-200 p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ __('Password requirements:') }}
                </h4>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-xs text-blue-700">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                        {{ __('At least 8 characters') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                        {{ __('At least one uppercase letter') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                        {{ __('At least one lowercase letter') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                        {{ __('At least one number') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                        {{ __('At least one special character') }}
                    </li>
                </ul>
            </div>

            <!-- Boutons -->
            <div class="w-full flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
                <button 
                    type="button" 
                    wire:click="resetPasswordForm" 
                    class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors"
                >
                    <i class="fas fa-undo mr-2"></i>
                    {{ __('Reset') }}
                </button>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="updatePassword"
                    class="w-full sm:w-auto rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-70"
                >
                    <span wire:loading.remove wire:target="updatePassword">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('Update password') }}
                    </span>
                    <span wire:loading wire:target="updatePassword">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        {{ __('Updating...') }}
                    </span>
                </button>
            </div>

             <!-- Message -->
             @if (session('status') === 'password-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-cloak
                    class="w-full mt-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700 flex items-start gap-3"
                >
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium">{{ __('Password updated successfully!') }}</p>
                        <p class="text-green-600">{{ __('Your password has been updated.') }}</p>
                    </div>
                    <button
                        type="button"
                        @click="show = false"
                        class="text-green-600 hover:text-green-800"
                        aria-label="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        </form>
    </section>
</div>
