<div class="w-full space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 truncate">
                    {{ __('Loan') }}
                </h1>

                @php
                    $statusColor = match($statut) {
                        'en_attente'  => 'bg-yellow-100 text-yellow-800',
                        'rendu'    => 'bg-green-100 text-green-800',
                        'en_retard'   => 'bg-red-100 text-red-800',
                        'annule'      => 'bg-gray-100 text-gray-800',
                        'refuse'      => 'bg-red-100 text-red-800',
                        default       => 'bg-blue-100 text-blue-800',
                    };
                    $dotColor = match($statut) {
                        'en_attente'  => 'bg-yellow-600',
                        'rendu'    => 'bg-green-600',
                        'en_retard'   => 'bg-red-600',
                        'annule'      => 'bg-gray-600',
                        'refuse'      => 'bg-red-600',
                        default       => 'bg-blue-600',
                    };
                @endphp

                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium whitespace-nowrap {{ $statusColor }}">
                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
                    {{ ucfirst(str_replace('_', ' ', $statut)) }}
                </span>
            </div>

            <div class="mt-2 flex flex-col gap-1.5 text-sm text-gray-500 sm:mt-1 sm:flex-row sm:flex-wrap sm:items-center sm:gap-x-4 sm:gap-y-1">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-book text-gray-400 text-xs"></i>
                    <span class="font-medium text-gray-700">{{ $book->titre }}</span>
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-user text-gray-400 text-xs"></i>
                    <span class="font-medium text-gray-700">
                        @forelse($book->authors as $author)
                            {{ $author->nom . ' ' . $author->prenom }}@if(!$loop->last), @endif
                        @empty
                            {{ __('No Author') }}
                        @endforelse
                    </span>
                </span>
                @if($book->date_emprunt)
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                        {{ __('Borrowed on') . ' ' . $book->date_emprunt }}
                    </span>
                @endif
            </div>
        </div>

        @if(in_array($statut, ['en_attente']))
            <div class="flex w-full shrink-0 gap-2 sm:w-auto">
                <button
                    type="button"
                    wire:click="requestCancel"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-red-100 px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-200 transition-colors whitespace-nowrap sm:w-auto sm:py-2"
                >
                    <i class="fas fa-undo text-sm"></i>
                    {{ __('Cancel') }}
                </button>
            </div>
        @endif
    </div>

    <!-- Informations de l'emprunt -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 sm:py-4">
            <h2 class="text-sm font-semibold text-gray-900">
                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                {{ __('Loan information') }}
            </h2>
        </div>
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Book') }}</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900 break-words">{{ $book->titre }}</dd>
                    <dd class="text-xs text-gray-500">
                        @forelse($book->authors as $author)
                            {{ $author->nom . ' ' . $author->prenom }}@if(!$loop->last), @endif
                        @empty
                            {{ __('No Author') }}
                        @endforelse
                    </dd>
                </div>

                @if($date_emprunt)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Borrow date') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $date_emprunt }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Status') }}</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                            <span class="mr-1 h-1.5 w-1.5 rounded-full {{ $dotColor }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $statut)) }}
                        </span>
                    </dd>
                </div>

                @if($approbateur)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Approved by') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 break-words">{{ $approbateur->name }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Notes -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                <h3 class="text-xs font-semibold text-gray-900">
                    <i class="fas fa-user mr-1.5 text-indigo-600"></i>
                    {{ __('Loan reason') }}
                </h3>
            </div>
            <div class="px-4 py-3">
                <p class="text-sm text-gray-700">{{ $note_membre ?? '—' }}</p>
            </div>
        </div>

        @if($note_bibliothecaire)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-xs font-semibold text-gray-900">
                        <i class="fas fa-user-tie mr-1.5 text-indigo-600"></i>
                        {{ __('Librarian note') }}
                    </h3>
                </div>
                <div class="px-4 py-3">
                    <p class="text-sm text-gray-700">{{ $note_bibliothecaire }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Dates -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 sm:px-6 sm:py-4">
            <h2 class="text-sm font-semibold text-gray-900">
                <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                {{ __('Dates') }}
            </h2>
        </div>
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Desired return') }}</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $date_retour_souhaitee }}</dd>
                </div>

                @if($date_retour_prevue)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Expected return') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $date_retour_prevue }}</dd>
                    </div>
                @endif

                @if($date_retour_reelle)
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Actual return') }}</dt>
                        <dd class="mt-1 text-sm font-medium text-green-600">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            {{ $date_retour_reelle }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>