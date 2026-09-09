<div>
    <x-alert-message />

    {{-- ========== FILTRES ========== --}}
    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="flex-1">
                <label for="author_search" class="mb-1 block text-sm font-medium text-gray-700">
                    {{ __('Search an author') }}
                </label>
                <input
                    id="author_search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name or surname...') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                >
            </div>
        </div>
    </div>

    {{-- ========== TABLEAU ========== --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-pen-fancy text-indigo-600"></i>
                <h3 class="text-base font-semibold text-gray-900">{{ __('List of authors') }}</h3>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{ $totalAuthors }}</span>
            </div>
            @can('gérer-auteurs')
                <button
                    type="button"
                    wire:click="showCreateModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                >
                    <i class="fas fa-plus"></i>
                    {{ __('New author') }}
                </button>
            @endcan
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="hidden px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500 sm:table-cell">{{ __('numberAuthor') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('nameAuthor') }}</th>
                        <th class="hidden px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500 sm:table-cell">{{ __('surnameAuthor') }}</th>
                        <th class="hidden px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500 md:table-cell">{{ __('biographyAuthor') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('actionsAuthor') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($authors as $author)
                        <tr class="hover:bg-gray-50">
                            <td class="hidden px-4 py-3 text-center align-middle sm:table-cell"><span class="text-xs font-medium text-gray-500">{{ '#'.$loop->iteration }}</span></td>
                            <td class="px-4 py-3 text-center align-middle">
                                <p class="font-medium text-gray-900">{{ $author->nom }}</p>
                                <p class="text-xs text-gray-500 sm:hidden">{{ $author->prenom }}</p>
                                <p class="mt-0.5 line-clamp-2 text-xs text-gray-500 md:hidden">
                                    {{ $author->biographie ?: __('No biography') }}
                                </p>
                            </td>
                            <td class="hidden px-4 py-3 text-center align-middle sm:table-cell"><p class="font-medium text-gray-900">{{ $author->prenom }}</p></td>
                            <td class="hidden px-4 py-3 text-center align-middle md:table-cell"><span class="text-sm text-gray-600">{{ $author->biographie ?: __('No biography') }}</span></td>
                            <td class="px-4 py-3 text-center align-middle">
                                @can('gérer-auteurs')
                                    <div class="flex justify-center gap-1">
                                        <button type="button" wire:click="openDetailView({{ $author->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}"><i class="fas fa-eye text-sm"></i></button>
                                        <button type="button" wire:click="showEditModal({{ $author->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}"><i class="fas fa-edit text-sm"></i></button>
                                        <button type="button" wire:click="showDeleteModal({{ $author->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}"><i class="fas fa-trash text-sm"></i></button>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('noAuthorFound') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-blue-200 bg-blue-50 px-4 py-3">
            {{ $authors->links() }}
        </div>
    </div>

    @if($showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $editingId ? __('Edit author') : __('New author') }}
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
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                    <livewire:biblio::author-form
                        :author-id="$editingId"
                        :key="'author-form-'.($editingId ?? 'new')"
                    />
                </div>
            </div>
        </div>
    @endif

    @if($showConfirmDeleteForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Author') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Are you sure you want to delete this author? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                    <button
                        type="button"
                        wire:click="closeDeleteModal"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="deleteAuthor"
                        class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 sm:w-auto"
                    >
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDetailView)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Author detail') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeDetailView"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <livewire:biblio::author-details
                        :author-id="$authorId"
                        :key="'author-details-'.$authorId"
                    />
                </div>
            </div>
        </div>
    @endif
</div>
