<div>
    <!-- Alertes / Notifications -->
    <x-alert-message />

    {{-- ========== FILTRES ========== --}}
    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="space-y-3">
            {{-- Recherche pleine largeur --}}
            <div>
                <label for="loan_search" class="mb-1 block text-sm font-medium text-gray-700">
                    {{ __('Search a loan') }}
                </label>
                <input
                    id="loan_search"
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="{{ __('Search by book or title...') }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    value=""
                >
            </div>

            {{-- Filtres sur une ligne dédiée --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label for="loan_status" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('Status') }}
                    </label>
                    <select
                        id="loan_status"
                        wire:model.live="status"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('-- All statuses --') }}</option>
                        <option value="brouillon">{{ __('Draft') }}</option>
                        <option value="en_attente">{{ __('Pending') }}</option>
                        <option value="approuve">{{ __('Approved') }}</option>
                        <option value="refuse">{{ __('Refused') }}</option>
                        <option value="rendu">{{ __('Returned') }}</option>
                    </select>
                </div>

                <div>
                    <label for="loan_late" class="mb-1 block text-sm font-medium text-gray-700">
                        {{ __('Filter') }}
                    </label>
                    <select
                        id="loan_late"
                        wire:model.live="onlyLate"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('All loans') }}</option>
                        <option value="true">{{ __('Late only') }}</option>
                        <option value="false">{{ __('Not late') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== TABLEAU ========== --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        {{-- En-tête : titre + compteur + bouton créer --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-hand-holding text-indigo-600"></i>
                <h3 class="text-base font-semibold text-gray-900">{{ __('List of loans') }}</h3>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">
                    {{ $emprunts->total() }}
                </span>
            </div>

            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto sm:py-2"
            >
                <i class="fas fa-plus"></i>
                {{ __('New loan') }}
            </button>
        </div>

        <!-- Version Desktop : Tableau -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Loan date') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Return date') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    
                    @forelse($emprunts as $emprunt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="text-xs font-medium text-gray-500">{{ $loop->iteration }}</span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <p class="font-medium text-gray-900 truncate max-w-xs">{{ $emprunt?->book?->titre }}</p>
                            @forelse($emprunt?->book?->authors as $author)
                                <p class="text-xs text-gray-500 truncate max-w-xs">
                                 {{ $author->name }}
                                </p>
                            @empty
                                {{ __('No Author')}}
                            @endforelse
                            
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="text-sm text-gray-600"> {{ $emprunt->date_emprunt?->format('d/m/Y') ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <span class="text-sm text-gray-600">{{ $emprunt->date_retour_reelle ? $emprunt->date_retour_reelle?->format('d/m/Y') : $emprunt->date_retour_souhaitee?->format('d/m/Y')}}</span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            @php
                                $statutValue = $emprunt->statut instanceof \BackedEnum ? $emprunt->statut->value : (string) $emprunt->statut;

                                [$badgeClass, $dotClass, $label] = match ($statutValue) {
                                    'brouillon' => ['bg-gray-100 text-gray-700', 'bg-gray-400', __('Draft')],
                                    'en_attente' => ['bg-amber-100 text-amber-800', 'bg-amber-500', __('Pending')],
                                    'approuve' => ['bg-blue-100 text-blue-800', 'bg-blue-600', __('Approved')],
                                    'refuse' => ['bg-red-100 text-red-800', 'bg-red-600', __('Refused')],
                                    'rendu' => ['bg-green-100 text-green-800', 'bg-green-600', __('Returned')],
                                    default => ['bg-gray-100 text-gray-700', 'bg-gray-400', $statutValue],
                                };
                            @endphp

                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center align-middle">
                            <div class="flex justify-center gap-1">
                                @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::EnAttente, \Modules\Emprunt\Enums\StatutEmprunt::Approuve, \Modules\Emprunt\Enums\StatutEmprunt::Refuse, \Modules\Emprunt\Enums\StatutEmprunt::Rendu]))
                                    <button type="button" wire:click="openDetailsModal({{ $emprunt->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                @endif
                                @if($emprunt->statut === \Modules\Emprunt\Enums\StatutEmprunt::Brouillon)
                                    <button type="button" wire:click="openEditModal({{ $emprunt->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}">
                                        <i class="fas fa-edit text-sm"></i>
                                    </button>
                                    <button type="button" wire:click="openConfirmDeleteModal({{ $emprunt->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @endif

                                @if($emprunt->statut === \Modules\Emprunt\Enums\StatutEmprunt::EnAttente)
                                    <button type="button" wire:click="openConfirmCancelLoanModal({{ $emprunt->id }})" class="rounded-lg p-1.5 text-red-600 hover:bg-red-50 hover:text-red-700" title="{{ __('Cancel Ask') }}">
                                        <i class="fas fa-undo text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center align-middle">
                            <p class="text-gray-500">{{ __('No loan found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Version Mobile : Cartes dynamiques -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($emprunts as $emprunt)
                @php
                    $statutValue = $emprunt->statut instanceof \BackedEnum ? $emprunt->statut->value : (string) $emprunt->statut;

                    [$badgeClass, $dotClass, $label] = match ($statutValue) {
                        'brouillon' => ['bg-gray-100 text-gray-700', 'bg-gray-400', __('Draft')],
                        'en_attente' => ['bg-amber-100 text-amber-800', 'bg-amber-500', __('Pending')],
                        'approuve' => ['bg-blue-100 text-blue-800', 'bg-blue-600', __('Approved')],
                        'refuse' => ['bg-red-100 text-red-800', 'bg-red-600', __('Refused')],
                        'rendu' => ['bg-green-100 text-green-800', 'bg-green-600', __('Returned')],
                        default => ['bg-gray-100 text-gray-700', 'bg-gray-400', $statutValue],
                    };

                    $authorsLabel = $emprunt?->book?->authors?->isNotEmpty()
                        ? $emprunt->book->authors->map(fn($a) => $a->name)->join(', ')
                        : __('No Author');

                    $isLate = $emprunt->date_retour_prevue
                        && $emprunt->date_retour_prevue->isPast()
                        && in_array($statutValue, ['approuve']);
                @endphp

                <div class="p-4 hover:bg-gray-50">
                    {{-- En-tête : numéro + badge statut --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-500">#{{ $loop->iteration }}</span>
                        <div class="flex items-center gap-1">
                            @if($isLate)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-800">
                                    <i class="fas fa-exclamation-circle mr-0.5"></i>
                                    {{ __('Late') }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $label }}
                            </span>
                        </div>
                    </div>

                    {{-- Informations --}}
                    <div class="space-y-1">
                        <p class="text-sm text-gray-700 truncate">
                            {{ $emprunt?->book?->titre ?? '—' }} - {{ $authorsLabel }}
                        </p>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 pt-1">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                {{ __('Loan') }}: {{ $emprunt->date_emprunt?->format('d/m/Y') ?? '—' }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-undo text-[10px]"></i>
                                {{ __('Return') }}: {{ $emprunt->date_retour_reelle?->format('d/m/Y') ?? $emprunt->date_retour_souhaitee?->format('d/m/Y') ?? '—' }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-1 mt-3 pt-2 border-t border-gray-100">
                        @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::EnAttente, \Modules\Emprunt\Enums\StatutEmprunt::Approuve]))
                            <button type="button" wire:click="openDetailsModal({{ $emprunt->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        @endif

                        @if($emprunt->statut === \Modules\Emprunt\Enums\StatutEmprunt::Brouillon)
                            <button type="button" wire:click="openEditModal({{ $emprunt->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button type="button" wire:click="openConfirmDeleteModal({{ $emprunt->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        @endif

                        @if($emprunt->statut === \Modules\Emprunt\Enums\StatutEmprunt::EnAttente)
                            <button type="button" wire:click="openConfirmCancelLoanModal({{ $emprunt->id }})" class="rounded-lg p-2 text-red-600 hover:bg-red-50 hover:text-red-700" title="{{ __('Cancel Ask') }}">
                                <i class="fas fa-undo text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">
                    <i class="fas fa-inbox text-2xl text-gray-300 mb-2 block"></i>
                    {{ __('No loan found') }}
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
            {{ $emprunts->links() }}
        </div>
    </div>


    {{-- ========== 2. MODAL CREATE / UPDATE  ========== --}}
    @if ($showModalForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ $editingId ? __('Edit loan') : __('New loan') }}
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
                    <livewire:emprunt::emprunt-form
                        :emprunt-id="$editingId"
                        :key="'emprunt-form-'.($editingId ?? 'new')"
                    />
                </div>
            </div>
        </div>
    @endif



    @if($showDeleteModalForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Loan') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Are you sure you want to delete this loan? This action cannot be undone.') }}
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
                        wire:click="deleteLoan"
                        class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 sm:w-auto"
                    >
                        <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showCancelLoanModalForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Cancel Loan') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Are you sure you want to cancel this loan? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center">
                    <button
                        type="button"
                        wire:click="closeCancelLoanModal"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="cancelLoan"
                        class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 sm:w-auto"
                    >
                        <i class="fas fa-undo mr-2"></i>{{ __('Confirm cancellation') }}
                    </button>
                </div>
            </div>
        </div>
    @endif


    @if ($showModalDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Loan detail') }}
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
                    <livewire:emprunt::emprunt-show
                        :emprunt-id="$viewingId"
                        :key="'emprunt-show-'.$viewingId"
                    />
                </div>
            </div>
        </div>
    @endif
</div>