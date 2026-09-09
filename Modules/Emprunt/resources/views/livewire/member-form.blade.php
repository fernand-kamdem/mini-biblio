<form class="space-y-6" wire:submit.prevent="save">
    <!-- En-tête du formulaire -->
    <div class="flex items-center gap-3 border-b border-gray-200 pb-4">
        <div class="rounded-lg bg-indigo-50 p-2.5">
            <i class="fas fa-info-circle text-indigo-600"></i>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Member information') }}</h3>
            <p class="text-xs text-gray-500">
                {{ __('Fill in all required fields') }} <span class="text-red-500 font-bold">*</span>
            </p>
        </div>
    </div>

    <!-- Grille 2 colonnes pour les infos principales -->
    <div class="grid grid-cols-1 gap-5">
        <!-- Nom -->
        <div>
            <label for="nom" class="block text-sm font-medium text-gray-700">
                {{ __('Name') }} <span class="text-red-500">*</span>
            </label>
            <input
                id="nom"
                type="text"
                placeholder="{{ __('Enter the name') }}"
                class="mt-1 block w-full rounded-lg border-gray-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 transition-colors"
                wire:model="name"
            />
            @error('name')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Email - Pleine largeur -->
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
            {{ __('Email') }} <span class="text-red-500">*</span>
        </label>
        <div class="relative mt-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-envelope text-gray-400 text-sm"></i>
            </div>
            <input
                id="email"
                type="email"
                wire:model="email"
                placeholder="{{ __('Enter the email') }}"
                class="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 transition-colors"
            />
        </div>
        @error('email')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Téléphone et Carte - Grille 2 colonnes -->
    <div class="grid grid-cols-1 gap-5">
        <!-- Téléphone -->
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">
                {{ __('Phone') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                </div>
                <input
                    id="phone"
                    type="tel"
                    placeholder="{{ __('Enter the phone') }}"
                    class="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 transition-colors"

                    wire:model="phone"
                />
            </div>
            @error('phone')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Adresse - Pleine largeur -->
    <div>
        <label for="address" class="block text-sm font-medium text-gray-700">
            {{ __('Address') }}
        </label>
        <div class="relative mt-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
            </div>
            <input
                id="address"
                type="text"
                placeholder="{{ __('Enter the address') }}"
                class="block w-full rounded-lg border-gray-300 pl-10 pr-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0 transition-colors"
                wire:model="address"
            />
        </div>
        <p class="mt-1.5 text-xs text-gray-400">{{ __('Optional') }}</p>
    </div>

    <!-- Séparateur -->
    <div class="border-t border-gray-200"></div>

    <!-- Actions -->
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <button
            type="button"
            wire:click="cancel"
            class="w-full rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors sm:w-auto"
        >
            <i class="fas fa-times mr-2"></i>
            {{ __('Cancel') }}
        </button>
        <button
            type="submit"
            class="w-full rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors sm:w-auto"
        >
            
            <i class="{{ $memberId? 'fas fa-edit mr-2' : 'fas fa-save mr-2' }}"></i>
            {{ $memberId? __('Update') : __('Create') }}
        </button>
    </div>
</form>