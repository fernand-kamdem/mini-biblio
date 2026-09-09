<form class="space-y-5" wire:submit="save">
    <!-- En-tête du formulaire -->
    <div class="flex items-center gap-3 border-b border-gray-200 pb-4">
        <div class="rounded-lg bg-indigo-50 p-2.5">
            <i class="fas fa-info-circle text-indigo-600"></i>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Author information') }}</h3>
            <p class="text-xs text-gray-500">
                {{ __('Fill in all required fields') }} <span class="text-red-500 font-bold">*</span>
            </p>
        </div>
    </div>

    <!-- Nom -->
    <div>
        <label for="nom" class="block text-sm font-medium text-gray-700">{{ __('Name') }} <span class="text-red-500">*</span></label>
        <input
            id="nom"
            type="text"
            wire:model="nom"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        />
        @if (session('error'))
            <p class="mt-2 text-sm text-red-600">{{ session('error') }}</p>
        @endif
    </div>

    <!-- Prenom -->
    <div>
        <label for="prenom" class="block text-sm font-medium text-gray-700">{{ __('Surname') }} <span class="text-red-500">*</span></label>
        <input
            id="prenom"
            type="text"
            wire:model="prenom"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        />
        @if (session('error'))
            <p class="mt-2 text-sm text-red-600">{{ session('error') }}</p>
        @endif
    </div>

    <!-- Biographie -->
    <div>
        <label for="biographie" class="block text-sm font-medium text-gray-700">{{ __('Biography') }}</label>
        <textarea
            id="biographie"
            rows="3"
            wire:model="biographie"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
        </textarea>
    </div>

    <!-- Actions -->
    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-end">
        <button
            type="button"
            wire:click="cancel"
            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
        >
            {{ __('Cancel') }}
        </button>
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-70 sm:w-auto"
        >
            <span wire:loading.remove wire:target="save">
                {{$authorId ? __('Update') : __('Create') }}
            </span>
            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
        </button>
    </div>
</form>