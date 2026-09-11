<form class="space-y-6" wire:submit.prevent="submit">
    <!-- En-tête du formulaire -->
    <div class="flex items-center gap-3 border-b border-gray-200 pb-4">
        <div class="rounded-lg bg-indigo-50 p-2.5">
            <i class="fas fa-info-circle text-indigo-600"></i>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-900">{{ __('Loan information') }}</h3>
            <p class="text-xs text-gray-500">
                {{ __('Fill in all required fields') }} <span class="text-red-500 font-bold">*</span>
            </p>
        </div>
    </div>

    <!-- Livre : recherche + liste -->
    <div>
        <label for="book_search" class="block text-sm font-medium text-gray-700">
            {{ __('Book') }} <span class="text-red-500">*</span>
        </label>

        @if ($selectedBook)
            <div class="mt-2 flex items-start justify-between gap-3 rounded-lg border border-indigo-200 bg-indigo-50/60 px-3 py-3">
                <div class="min-w-0 flex-1 space-y-1">
                    <p class="text-sm font-semibold text-gray-900">{{ $selectedBook->titre }}</p>
                    <p class="text-xs text-gray-500">
                        <span class="font-medium text-gray-600">{{ __('Author') }} :</span>
                        @if ($selectedBook->authors->isNotEmpty())
                            {{ $selectedBook->authors->map(fn ($a) => trim($a->prenom.' '.$a->nom))->join(', ') }}
                        @else
                            {{ __('No Author') }}
                        @endif
                    </p>
                    <p class="text-xs">
                        <span class="inline-flex items-center rounded-md bg-white px-2 py-0.5 font-medium text-emerald-700 ring-1 ring-emerald-200">
                            {{ __('Stock') }} : {{ $selectedBook->stock_disponible }}
                        </span>
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="clearBook"
                    class="shrink-0 rounded-lg p-1.5 text-gray-400 hover:bg-white hover:text-red-600"
                    title="{{ __('Change book') }}"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @else
            <div class="relative mt-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-sm text-gray-400"></i>
                </div>
                <input
                    id="book_search"
                    type="search"
                    wire:model.live.debounce.300ms="bookSearch"
                    placeholder="{{ __('Search by title or author...') }}"
                    autocomplete="off"
                    class="block w-full rounded-lg border-gray-300 py-2 pl-10 pr-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                >
            </div>

            <div class="mt-2 max-h-40 overflow-y-auto rounded-lg border border-gray-200 bg-white">
                @forelse ($books as $book)
                    <button
                        type="button"
                        wire:click="selectBook({{ $book->id }})"
                        class="flex w-full items-start justify-between gap-3 border-b border-gray-100 px-2.5 py-1.5 text-left last:border-b-0 hover:bg-gray-50"
                    >
                        <div class="min-w-0 flex-1 space-y-0.5">
                            <p class="truncate text-sm font-semibold text-gray-900">{{ $book->titre }}</p>
                            <p class="truncate text-xs text-gray-500">
                                <span class="font-medium text-gray-600">{{ __('Author') }} :</span>
                                @if ($book->authors->isNotEmpty())
                                    {{ $book->authors->map(fn ($a) => trim($a->prenom.' '.$a->nom))->join(', ') }}
                                @else
                                    {{ __('No Author') }}
                                @endif
                            </p>
                        </div>
                        <span class="shrink-0 rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-100">
                            {{ __('Stock') }} {{ $book->stock_disponible }}
                        </span>
                    </button>
                @empty
                    <p class="px-3 py-4 text-sm text-gray-500">{{ __('No Book Available') }}</p>
                @endforelse
            </div>
        @endif

        @error('bookId')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Date de retour souhaitée -->
    <div>
        <label for="date_retour_souhaitee" class="block text-sm font-medium text-gray-700">
            {{ __('Desired return date') }} <span class="text-red-500">*</span>
        </label>
        <div class="relative mt-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-calendar-alt text-sm text-gray-400"></i>
            </div>
            <input
                id="date_retour_souhaitee"
                type="date"
                wire:model="dateRetourSouhaitee"
                min="{{ now()->toDateString() }}"
                class="block w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
            >
        </div>
        <p class="mt-1 text-xs text-gray-500">{{ __('Indicate the date on which you wish to return the book.') }}</p>
        @error('dateRetourSouhaitee')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Note -->
    <div>
        <label for="note" class="block text-sm font-medium text-gray-700">
            {{ __('Note') }}<span class="text-red-500">*</span>
        </label>
        <div class="relative mt-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-start pl-3 pt-3">
                <i class="fas fa-comment text-sm text-gray-400"></i>
            </div>
            <textarea
                id="note"
                wire:model="note"
                rows="3"
                placeholder="{{ __('Add a reason or comment...') }}"
                class="block w-full resize-y rounded-lg border-gray-300 py-2.5 pl-10 pr-3 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
            ></textarea>
        </div>
        @error('note')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Séparateur -->
    <div class="border-t border-gray-200"></div>

    <!-- Actions -->
    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
        <button
            type="button"
            wire:click="cancel"
            class="w-full rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:border-gray-400 hover:bg-gray-50 sm:w-auto"
        >
            <i class="fas fa-times mr-2"></i>
            {{ __('Cancel') }}
        </button>
        <button
            type="button"
            wire:click="saveDraft"
            wire:loading.attr="disabled"
            wire:target="saveDraft"
            class="w-full rounded-lg border border-indigo-300 bg-white px-6 py-2.5 text-sm font-medium text-indigo-700 transition-colors hover:bg-indigo-50 disabled:opacity-70 sm:w-auto"
        >
            <i class="fas fa-save mr-2"></i>
            {{ __('Save') }}
        </button>
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="submit"
            class="w-full rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-70 sm:w-auto"
        >
            <span wire:loading.remove wire:target="submit">
                <i class="fas fa-paper-plane mr-2"></i>
                {{ __('Submit') }}
            </span>
            <span wire:loading wire:target="submit">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                {{ __('Saving...') }}
            </span>
        </button>
    </div>
</form>
