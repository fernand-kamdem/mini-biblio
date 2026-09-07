<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $card_number = '';
    public string $address = '';
    public bool $is_active = false;
    public string $language = '';

    public function mount(): void
    {
        $user = Auth::user();
        $member = $user->member;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $member?->phone ?? '';
        $this->card_number = $member?->card_number ?? '';
        $this->address = $member?->address ?? '';
        $this->is_active = $user->is_active;
        $this->language = $user->language;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $member = $user->member;

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone' => ['required', 'string', Rule::unique('members', 'phone')->ignore($member?->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'language' => ['required', 'string', 'max:2'],
        ]);

        $languageChanged = $user->language !== $validated['language'];

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'language' => $validated['language'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($member) {
            $member->fill([
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? null,
            ]);
        }

        DB::transaction(function () use ($user, $member) {
            $user->save();
            $member?->save();
        });

        session()->flash('status', 'profile-updated');
        if ($languageChanged) {
            $this->js('window.location.reload()');
        }
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function resetProfile(){
        $this->mount();
    }

}; ?>


<div class="w-full max-w-full">
    <section class="w-full max-w-full space-y-6">
        <!-- En-tête de la section -->
        <div class="w-full flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-user-circle mr-2 text-indigo-600"></i>
                    {{ __('Profile Information') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __("Update your account's profile information and contact details.") }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800 whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-1.5 text-indigo-500"></i>
                    {{ auth()->user()->roles->first()->name ?? __('Member') }}
                </span>
                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 whitespace-nowrap">
                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-600"></span>
                    {{ $is_active?__('Active'):__('Disabled') }}
                </span>
            </div>
        </div>

        <!-- Carte membre -->
        @if(auth()->user()->hasRole('MEMBRE'))
            <div class="w-full overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-5 text-white shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white/20">
                            <i class="fas fa-user text-2xl text-white"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-indigo-200">{{ __('Member Card') }}</p>
                            <p class="text-lg font-bold truncate">{{ auth()->user()->name }}</p>
                            <p class="font-mono text-sm text-indigo-200 truncate">{{ auth()->user()->member?->card_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right w-full sm:w-auto flex-shrink-0">
                        <p class="text-xs text-indigo-200">{{ __('Member since') }}</p>
                        <p class="text-sm font-medium">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="updateProfileInformation" class="w-full space-y-6">
            <!-- Grille 2 colonnes -->
            <div class="w-full grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Nom complet -->
                <div class="w-full">
                    <x-input-label for="name" :value="__('Full name')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <x-text-input 
                            wire:model="name" 
                            id="name" 
                            name="name" 
                            type="text" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                            required 
                            autofocus 
                            autocomplete="name" 
                        />
                    </div>
                    <x-input-error class="mt-1.5 text-xs" :messages="$errors->get('name')" />
                </div>

                <!-- Email -->
                <div class="w-full">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <x-text-input 
                            wire:model="email" 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                            required 
                            autocomplete="username" 
                        />
                    </div>
                    <x-input-error class="mt-1.5 text-xs" :messages="$errors->get('email')" />
                </div>

                <!-- Téléphone -->
                @if(auth()->user()->hasRole('MEMBRE'))
                    <div class="w-full">
                        <x-input-label for="phone" :value="__('Phone number')" class="text-sm font-medium text-gray-700" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <x-text-input 
                                wire:model="phone" 
                                id="phone" 
                                name="phone" 
                                type="tel" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                                autocomplete="tel" 
                            />
                        </div>
                        <x-input-error class="mt-1.5 text-xs" :messages="$errors->get('phone')" />
                    </div>
                

                    <!-- Numéro de carte -->
                    <div class="w-full">
                        <x-input-label for="card_number" :value="__('Card number')" class="text-sm font-medium text-gray-700" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <x-text-input 
                                wire:model="card_number" 
                                id="card_number" 
                                name="card_number" 
                                type="text" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 bg-gray-50" 
                                readonly 
                                disabled
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-gray-400">{{ __('Card number cannot be modified') }}</p>
                    </div>

                    <!-- Adresse (pleine largeur) -->
                    <div class="w-full md:col-span-2">
                        <x-input-label for="address" :value="__('Address')" class="text-sm font-medium text-gray-700" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <x-text-input 
                                wire:model="address" 
                                id="address" 
                                name="address" 
                                type="text" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150" 
                                autocomplete="street-address" 
                            />
                        </div>
                        <x-input-error class="mt-1.5 text-xs" :messages="$errors->get('address')" />
                    </div>
                @endif
                <!-- Langue préférée -->
                <div class="w-full">
                    <x-input-label for="language" :value="__('Preferred language')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-globe text-gray-400"></i>
                        </div>
                        <select 
                            wire:model="language" 
                            id="language" 
                            name="language" 
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 appearance-none bg-white"
                        >
                            <option value="fr">{{ __('Français') }}</option>
                            <option value="en">{{ __('Anglais') }}</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <x-input-error class="mt-1.5 text-xs" :messages="$errors->get('language')" />
                </div>

                <!-- Rôle -->
                <div class="w-full">
                    <x-input-label for="role" :value="__('Role')" class="text-sm font-medium text-gray-700" />
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-shield-alt text-gray-400"></i>
                        </div>
                        <x-text-input 
                            id="role" 
                            name="role" 
                            type="text" 
                            value="{{ auth()->user()->roles->first()->name ?? __('Member') }}" 
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 bg-gray-50" 
                            readonly 
                            disabled
                        />
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">{{ __('Role assigned by administrator') }}</p>
                </div>
            </div>

            <!-- Boutons -->
            <div class="w-full flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end">
                <button 
                    type="button" 
                    wire:click="resetProfile" 
                    class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors"
                >
                    <i class="fas fa-undo mr-2"></i>
                    {{ __('Reset') }}
                </button>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="updateProfileInformation"
                    class="w-full sm:w-auto rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-70"
                >
                    <span wire:loading.remove wire:target="updateProfileInformation">
                        <i class="fas fa-save mr-2"></i>
                        {{ __('Save changes') }}
                    </span>
                    <span wire:loading wire:target="updateProfileInformation">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        {{ __('Saving...') }}
                    </span>
                </button>
            </div>

            <!-- Message -->
            @if (session('status') === 'profile-updated')
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-cloak
                    class="w-full mt-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700 flex items-start gap-3"
                >
                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-medium">{{ __('Profile updated successfully!') }}</p>
                        <p class="text-green-600">{{ __('Your information has been saved.') }}</p>
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
