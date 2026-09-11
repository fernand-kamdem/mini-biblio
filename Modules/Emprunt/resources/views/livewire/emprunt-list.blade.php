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
                    type="text"
                    wire:model.live.debounce='search'
                    placeholder="{{ __('Search by name, book or card number...') }}"
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
                        wire:model.live='status'
                        class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">{{ __('-- All statuses --') }}</option>
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
                        wire:model.live='onlyLate'
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
        </div>

        <!-- Version Desktop : Tableau -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Member') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Loan date') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Return date') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-center align-middle text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    
                    @forelse($emprunts as $emprunt)
                        @if($emprunt->statut !== \Modules\Emprunt\Enums\StatutEmprunt::Brouillon)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center align-middle">
                                    <span class="text-xs font-medium text-gray-500">{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-4 py-3 text-center align-middle">
                                    <p class="font-medium text-gray-900">{{ $emprunt?->member?->user?->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $emprunt?->member?->card_number }}</p>
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
                                    <span class="text-sm text-gray-600"> {{ $emprunt->date_emprunt?->format('Y-m-d') ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center align-middle">
                                    <span class="text-sm text-gray-600">{{ $emprunt->date_retour_prevue ? $emprunt->date_retour_prevue?->format('Y-m-d') : $emprunt->date_retour_souhaitee?->format('Y-m-d') }}</span>
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

                                            <button type="button" wire:click="openShowModal({{ $emprunt->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('Detail') }}">
                                                <i class="fas fa-eye text-sm"></i>
                                            </button>

                                        @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::Approuve]))
                                            <button type="button" wire:click="retourner({{ $emprunt->id }})" class="rounded-lg p-1.5 text-green-600 hover:bg-blue-50 hover:text-green-700" title="{{ __('Return book') }}">
                                                <i class="fas fa-undo text-sm"></i>
                                            </button>
                                        @endif
                                        
                                        @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::EnAttente]))
                                            <button type="button" wire:click="openModalApprove({{ $emprunt->id }})" class="rounded-lg p-1.5 text-green-700 hover:bg-green-50 hover:text-green-700" title="{{ __('Approve') }}">
                                                <i class="fas fa-check text-sm"></i>
                                            </button>
                                            <button type="button" wire:click="openModalRefuse({{ $emprunt->id }})" class="rounded-lg p-1.5 text-red-600 hover:bg-red-50 hover:text-red-700" title="{{ __('Refuse') }}">
                                                <i class="fas fa-times text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('No Loan Available') }}</td>
                        </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>

        <!-- Version Mobile : Cartes -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($emprunts as $emprunt)
                @if($emprunt->statut !== \Modules\Emprunt\Enums\StatutEmprunt::Brouillon)
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

                        $authors = $emprunt?->book?->authors;
                        $authorsLabel = $authors && $authors->isNotEmpty()
                            ? $authors->map(fn($a) => $a->name)->join(', ')
                            : __('No Author');
                    @endphp

                    <div class="p-4 hover:bg-gray-50">
                        {{-- En-tête : numéro + badge statut --}}
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-500">#{{ $loop->iteration }}</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotClass }}"></span>
                                {{ $label }}
                            </span>
                        </div>

                        {{-- Informations --}}
                        <div class="space-y-1">
                            <p class="font-medium text-gray-900">{{ $emprunt?->member?->user?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">{{ $emprunt?->member?->card_number ?? '—' }}</p>
                            <p class="text-sm text-gray-700 truncate">{{ $emprunt?->book?->titre ?? '—' }} - {{ $authorsLabel }}</p>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 pt-1">
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-calendar-alt text-[10px]"></i>
                                    {{ __('Loan') }}: {{ $emprunt->date_emprunt?->format('d/m/Y') ?? '—' }}
                                </span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-undo text-[10px]"></i>
                                    {{ __('Return') }}: {{ $emprunt->date_retour_prevue?->format('d/m/Y') ?? $emprunt->date_retour_souhaitee?->format('d/m/Y') ?? '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-end gap-1 mt-3 pt-2 border-t border-gray-100">
                            @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::EnAttente, \Modules\Emprunt\Enums\StatutEmprunt::Approuve, \Modules\Emprunt\Enums\StatutEmprunt::Rendu]))
                                <button type="button" wire:click="openShowModal({{ $emprunt->id }})" class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('Detail') }}">
                                    <i class="fas fa-eye text-sm"></i>
                                </button>
                            @endif

                            @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::Approuve]))
                                <button type="button" wire:click="retourner({{ $emprunt->id }})" class="rounded-lg p-2 text-green-600 hover:bg-green-50 hover:text-green-700" title="{{ __('Return book') }}">
                                    <i class="fas fa-undo text-sm"></i>
                                </button>
                            @endif

                            @if(in_array($emprunt->statut, [\Modules\Emprunt\Enums\StatutEmprunt::EnAttente]))
                                <button type="button" wire:click="openModalApprove({{ $emprunt->id }})" class="rounded-lg p-2 text-green-700 hover:bg-green-50" title="{{ __('Approve') }}">
                                    <i class="fas fa-check text-sm"></i>
                                </button>
                                <button type="button" class="rounded-lg p-2 text-red-600 hover:bg-red-50 hover:text-red-700" title="{{ __('Refuse') }}">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @empty
                <div class="p-8 text-center text-sm text-gray-500">
                    <i class="fas fa-inbox text-2xl text-gray-300 mb-2 block"></i>
                    {{ __('No Loan Available') }}
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="border-t border-blue-200 bg-blue-50 px-4 py-3">
            {{ $emprunts->links() }}
        </div>
    </div>


    @if ($showModalDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Loan detail') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeShowModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                    <livewire:emprunt::emprunt-show-admin
                        :emprunt-id="$viewingId"
                        :key="'emprunt-show-admin'.$viewingId"
                    />
                </div>
            </div>
        </div>
    @endif

    @if ($showModalApprove)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Approve Loan') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeApproveModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                    <livewire:emprunt::emprunt-approve
                        :emprunt-id="$approvingId"
                        :key="'emprunt-approve'.$approvingId"
                    />
                </div>
            </div>
        </div>
    @endif


    @if ($showModalRefuse)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Refuse Loan') }}
                    </h4>
                    <button
                        type="button"
                        wire:click="closeRefuseModal"
                        class="rounded-lg p-1.5 text-red-400 hover:bg-gray-100 hover:text-gray-600"
                        title="{{ __('Close') }}"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                    <livewire:emprunt::emprunt-refuse
                        :emprunt-id="$refusingId"
                        :key="'emprunt-refuse'.$refusingId"
                    />
                </div>
            </div>
        </div>
    @endif
</div>