<div class="space-y-5">
    <!-- Prénom + Nom + Actions -->
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold text-gray-900">{{ $author->prenom ?? __('No first name') }}</h2>
                <h2 class="text-xl font-semibold text-gray-900">{{ $author->nom ?? __('No last name') }}</h2>
            </div>
        </div>

        <div class="flex shrink-0 gap-1">
            <button
                type="button"
                wire:click="requestEdit"
                class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600"
                title="{{ __('Edit') }}"    
            >
                <i class="fas fa-edit text-sm"></i>
            </button>
            <button
                type="button"
                wire:click="requestDelete"
                class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600"
                title="{{ __('Delete') }}"
            >
                <i class="fas fa-trash text-sm"></i>
            </button>
        </div>
    </div>

    <!-- Biographie -->
    <div class="border-t border-gray-200 pt-5">
        <h3 class="text-xs font-medium uppercase tracking-wide text-gray-500">Biographie</h3>
        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">
            {{ $author->biographie ?? __('No biography') }}
        </p>
    </div>
</div>