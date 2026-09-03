<form wire:submit="save" class="space-y-5">
    {{-- Titre --}}
    <div>
        <x-input-label for="titre" :value="__('Title')" />
        <x-text-input
            wire:model="titre"
            id="titre"
            class="mt-1 block w-full"
            type="text"
            required
        />
        <x-input-error :messages="$errors->get('titre')" class="mt-2" />
    </div>

    {{-- ISBN + année --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="isbn" :value="__('ISBN')" />
            <x-text-input
                wire:model="isbn"
                id="isbn"
                class="mt-1 block w-full"
                type="text"
                placeholder="978-3-16-148410-0"
            />
            <x-input-error :messages="$errors->get('isbn')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="annee_parution" :value="__('Publication year')" />
            <x-text-input
                wire:model="annee_parution"
                id="annee_parution"
                class="mt-1 block w-full"
                type="number"
                min="1000"
                max="9999"
            />
            <x-input-error :messages="$errors->get('annee_parution')" class="mt-2" />
        </div>
    </div>

    {{-- Catégorie --}}
    <div>
        <x-input-label for="categoryId" :value="__('Category')" />
        <select
            wire:model="categoryId"
            id="categoryId"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
            <option value="">{{ __('Select a category') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->nom }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('categoryId')" class="mt-2" />
    </div>

    {{-- Auteurs : recherche + chips (meilleur que select multiple) --}}
    <div>
        <x-input-label for="authorSearch" :value="__('Authors')" />

        @if ($selectedAuthors->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($selectedAuthors as $author)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-800">
                        {{ $author->prenom }} {{ $author->nom }}
                        <button
                            type="button"
                            wire:click="removeAuthor({{ $author->id }})"
                            class="rounded-full p-0.5 text-indigo-500 hover:bg-indigo-100 hover:text-indigo-700"
                            title="{{ __('Remove') }}"
                        >
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </span>
                @endforeach
            </div>
        @endif

        <div class="relative mt-2">
            <x-text-input
                wire:model.live.debounce.300ms="authorSearch"
                id="authorSearch"
                class="block w-full"
                type="search"
                placeholder="{{ __('Search an author...') }}"
                autocomplete="off"
            />
        </div>

        <div class="mt-2 max-h-40 overflow-y-auto rounded-md border border-gray-200 bg-white">
            @forelse ($authors as $author)
                @php $isSelected = in_array($author->id, $authorIds, true); @endphp
                <button
                    type="button"
                    wire:click="toggleAuthor({{ $author->id }})"
                    class="flex w-full items-center justify-between px-3 py-2 text-left text-sm hover:bg-gray-50 {{ $isSelected ? 'bg-indigo-50 text-indigo-900' : 'text-gray-700' }}"
                >
                    <span>{{ $author->prenom }} {{ $author->nom }}</span>
                    @if ($isSelected)
                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                    @endif
                </button>
            @empty
                <p class="px-3 py-3 text-sm text-gray-500">{{ __('No authors found') }}</p>
            @endforelse
        </div>

        <x-input-error :messages="$errors->get('authorIds')" class="mt-2" />
        <x-input-error :messages="$errors->get('authorIds.*')" class="mt-2" />
    </div>

    {{-- Stock --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="stock_total" :value="__('Total stock')" />
            <x-text-input
                wire:model="stock_total"
                id="stock_total"
                class="mt-1 block w-full"
                type="number"
                min="0"
                required
            />
            <x-input-error :messages="$errors->get('stock_total')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="stock_disponible" :value="__('Available stock')" />
            <x-text-input
                wire:model="stock_disponible"
                id="stock_disponible"
                class="mt-1 block w-full"
                type="number"
                min="0"
                required
            />
            <x-input-error :messages="$errors->get('stock_disponible')" class="mt-2" />
        </div>
    </div>

    {{-- Description --}}
    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea
            wire:model="description"
            id="description"
            rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        ></textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-4">
        <button
            type="button"
            wire:click="cancel"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            {{ __('Cancel') }}
        </button>
        <button
            type="submit"
            wire:loading.attr="disabled"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-70"
        >
            <span wire:loading.remove wire:target="save">
                {{ $bookId ? __('Update') : __('Create') }}
            </span>
            <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
        </button>
    </div>
</form>
