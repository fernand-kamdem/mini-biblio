{{--
    BookList — vue parent CRUD.
    Props render() : $books, $categories, $totalBooks.
    États : filtres ($search, $categoryId) + 3 modals (form / delete / detail).

    Boutons → méthodes BookList :
      Nouveau        → showCreateModal
      Œil            → openDetailModal($id)
      Crayon         → showEditModal($id)
      Poubelle       → showConfirmDelete($id)

    Enfants inclus dans les modals :
      book-form     (:book-id="$editingId")
      book-details  (:book-id="$bookIdToShow")
--}}


                


<div>

<!-- Alertes / Notifications -->
@if(session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="ml-auto text-green-400 hover:text-green-600" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="ml-auto text-red-400 hover:text-red-600" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-4 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                            </div>
                            <button type="button" class="ml-auto text-yellow-400 hover:text-yellow-600" onclick="this.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif


    {{-- ========== FILTRES (équivalent <x-filter-card> Easy Day-O) ========== --}}
    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            {{-- Recherche titre / ISBN (debounce 300ms pour limiter les requêtes) --}}
            <div class="flex-1">
                <label for="book_search" class="mb-1 block text-sm font-medium text-gray-700">
                    {{ __('Book') }}
                </label>
                <input
                    id="book_search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by title or ISBN...') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            {{-- Filtre par catégorie --}}
            <div class="w-full md:w-56">
                <label for="book_category" class="mb-1 block text-sm font-medium text-gray-700">
                    {{ __('Category') }}
                </label>
                <select
                    id="book_category"
                    wire:model.live="categoryId"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">-- {{ __('All') }} --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ========== TABLEAU (équivalent <x-table-card> Easy Day-O) ========== --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- En-tête : titre + compteur + bouton créer --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-book text-indigo-600"></i>
                <h3 class="text-base font-semibold text-gray-900">{{ __('Book list') }}</h3>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                    {{ $totalBooks }}
                </span>
            </div>

            {{-- Bouton visible uniquement avec permission gérer-livres --}}
            @can('gérer-livres')
                <button
                    type="button"
                    wire:click="showCreateModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    <i class="fas fa-plus"></i>
                    {{ __('New book') }}
                </button>
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Title') }}
                        </th>
                        {{-- Colonnes masquées progressivement sur mobile --}}
                        <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 sm:table-cell">
                            {{ __('Category') }}
                        </th>
                        <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 md:table-cell">
                            {{ __('ISBN') }}
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Stock') }}
                        </th>
                        <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 lg:table-cell">
                            {{ __('Authors') }}
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                {{-- wire:loading : atténue le tableau pendant le refresh Livewire --}}
                <tbody class="divide-y divide-gray-200" wire:loading.class="opacity-60">
                    @forelse ($books as $book)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900">{{ $book->titre }}</p>
                                {{-- Catégorie affichée sous le titre sur mobile uniquement --}}
                                <p class="text-xs text-gray-500 sm:hidden">
                                    {{ $book->category?->nom ?? __('Uncategorized') }}
                                </p>
                            </td>
                            <td class="hidden px-4 py-3 sm:table-cell">
                                <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                                    {{ $book->category?->nom ?? __('Uncategorized') }}
                                </span>
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                <code class="rounded bg-gray-100 px-2 py-1 text-xs">{{ $book->isbn ?: '—' }}</code>
                            </td>
                            <td class="px-4 py-3">
                                {{-- disponible / total --}}
                                <span class="font-medium text-gray-900">{{ $book->stock_disponible }}</span>
                                <span class="text-xs text-gray-400">/ {{ $book->stock_total }}</span>
                            </td>
                            <td class="hidden px-4 py-3 lg:table-cell">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($book->authors as $author)
                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                                            {{ $author->prenom }} {{ $author->nom }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">{{ __('No author') }}</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-1">
                                    @can('gérer-livres')
                                    {{-- bouton book detail --}}
                                    <button
                                        type="button"
                                        wire:click="openDetailModal({{ $book->id }})"
                                        class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600"
                                        title="{{ __('Book detail') }}"
                                    >
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>

                                    {{-- bouton édition --}}
                                        <button
                                            type="button"
                                            wire:click="showEditModal({{ $book->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600"
                                            title="{{ __('Edit') }}"
                                        >
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>

                                        {{-- bouton suppression --}}
                                        <button
                                            type="button"
                                            wire:click="showConfirmDelete({{ $book->id }})"
                                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600"
                                            title="{{ __('Delete') }}"
                                        >
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>


                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                {{ __('No books found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Laravel (thème Tailwind côté composant PHP) --}}
        <div class="border-t border-blue-200 bg-blue-50 px-4 py-3">
            {{ $books->links() }}
        </div>
    </div>

    {{-- ========== 2. MODAL CREATE / UPDATE (enfant BookForm) ========== --}}
    @if ($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $editingId ? __('Edit book') : __('New book') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeFormModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    {{-- :key = recrée le formulaire quand on change create ↔ edit --}}
                    <livewire:biblio::book-form
                        :book-id="$editingId"
                        :key="'book-form-'.($editingId ?? 'new')"
                    />
                </div>
            </div>
        </div>
    @endif

    {{-- ========== 3. MODAL DELETE (confirmation, pas d'enfant) ========== --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Book') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Are you sure you want to delete this book? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="mt-5 flex justify-center gap-3">
                    <button
                        type="button"
                        wire:click="closeDeleteModal"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="deleteBook"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                    >
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ========== 4. MODAL SHOW (enfant BookDetails) ========== --}}
    @if ($showDetailModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Book detail') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeDetailModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <livewire:biblio::book-details
                        :book-id="$bookIdToShow"
                        :key="'book-details-'.$bookIdToShow"
                    />
                </div>
            </div>
        </div>
    @endif
</div>
