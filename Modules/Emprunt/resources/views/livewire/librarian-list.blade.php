<div>
    <!-- Alertes / Notifications -->
    <x-alert-message />

    {{-- ========== FILTRES ========== --}}
    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="space-y-3">
            {{-- Recherche pleine largeur --}}
            <div>
                <label for="loan_search" class="mb-1 block text-sm font-medium text-gray-700">
                    {{ __('Search a librarian') }}
                </label>
                <input
                    id="loan_search"
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Search by name...') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    value=""
                >
            </div>
        </div>
    </div>

    {{-- ========== TABLEAU ========== --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- En-tête : titre + compteur + bouton créer --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-hand-holding text-indigo-600"></i>
                <h3 class="text-base font-semibold text-gray-900">{{ __('List of librarians') }}</h3>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                    {{ $librarians->total() }}
                </span>
            </div>

            @can('gérer-bibliothécaires')
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto sm:py-2"
                >
                    <i class="fas fa-plus"></i>
                    {{ __('New Librarian') }}
                </button>
            @endcan
        </div>

        <!-- Version Desktop : Tableau -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    
                    @forelse($librarians as $librarian)
                        <tr class="hover:bg-gray-50">
                            <td class="hidden px-4 py-3 text-center align-middle sm:table-cell">
                                <span class="text-xs font-medium text-gray-500">{{ '#'.$loop->iteration }}</span>
                            </td>
                            <td class="px-4 py-3 text-center align-middle">{{ $librarian->name }}</td>
                            <td class="hidden px-4 py-3 text-center align-middle sm:table-cell">
                                <p class="font-medium text-gray-900">{{ $librarian->email }}</p>
                            </td>
                            <td class="hidden px-4 py-3 text-center align-middle md:table-cell">
                                @if($librarian->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                        {{ __('Disabled') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center align-middle">
                                <div class="flex justify-center gap-1">
                                    <button type="button" wire:click="openDetailsModal({{ $librarian->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    @can('gérer-bibliothécaires')
                                        <button type="button" wire:click="openEditModal({{ $librarian->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button type="button" wire:click="openConfirmDeleteModal({{ $librarian->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('No Librarian Available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Version Mobile : Cartes dynamiques -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($librarians as $librarian)
                <div class="p-4 hover:bg-gray-50">
                    {{-- En-tête : numéro + badge statut --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-500">#{{ $loop->iteration }}</span>
                        @if($librarian->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                {{ __('Active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-500"></span>
                                {{ __('Disabled') }}
                            </span>
                        @endif
                    </div>

                    {{-- Informations --}}
                    <div class="space-y-1">
                        <p class="font-medium text-gray-900 truncate">{{ $librarian->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $librarian->email }}</p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-1 mt-3 pt-2 border-t border-gray-100">
                        <button type="button" wire:click="openDetailsModal({{ $librarian->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        @can('gérer-bibliothécaires')
                            <button type="button" wire:click="openEditModal({{ $librarian->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button type="button" wire:click="openConfirmDeleteModal({{ $librarian->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">
                    <i class="fas fa-inbox text-2xl text-gray-300 mb-2 block"></i>
                    {{ __('No Librarian Available') }}
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
            {{ $librarians->links() }}
        </div>
    </div>

    @if($showModalForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $editingId ? __('Edit librarian') : __('New librarian') }}
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
                    <livewire:emprunt::librarian-form
                        :librarian-id="$editingId"
                        :key="'librarian-form-'.($editingId ?? 'new')"
                    />
                </div>
            </div>
        </div>
    @endif

    @if ($showModalDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Librarian detail') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeDetailsModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                    <livewire:emprunt::librarian-show
                        :librarian-id="$viewingId"
                        :key="'librarian-show-'.$viewingId"
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
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Librarian') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Are you sure you want to delete this librarian? This action cannot be undone.') }}
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
                        wire:click="deleteLibrarian"
                        class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 sm:w-auto"
                    >
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>