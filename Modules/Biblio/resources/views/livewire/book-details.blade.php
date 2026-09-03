<div class="space-y-5">
    {{-- Titre + catégorie + actions --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $book->titre }}</h2>
            <div class="mt-2">
                <span class="inline-flex rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800">
                    {{ $book->category?->nom ?? __('Uncategorized') }}
                </span>
            </div>
        </div>

        @can('gérer-livres')
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
        @endcan
    </div>

    {{-- Infos principales --}}
    <dl class="grid grid-cols-1 gap-4 border-t border-gray-200 pt-5 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('ISBN') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">
                @if ($book->isbn)
                    <code class="rounded bg-gray-100 px-2 py-1 text-xs">{{ $book->isbn }}</code>
                @else
                    <span class="text-gray-400">—</span>
                @endif
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Publication year') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">
                {{ $book->annee_parution ?? '—' }}
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Available stock') }}</dt>
            <dd class="mt-1 text-sm text-gray-900">
                <span class="font-medium">{{ $book->stock_disponible }}</span>
                <span class="text-gray-400">/ {{ $book->stock_total }}</span>
            </dd>
        </div>

        <div>
            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Authors') }}</dt>
            <dd class="mt-1">
                <div class="flex flex-wrap gap-1">
                    @forelse ($book->authors as $author)
                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">
                            {{ $author->prenom }} {{ $author->nom }}
                        </span>
                    @empty
                        <span class="text-sm text-gray-400">{{ __('No author') }}</span>
                    @endforelse
                </div>
            </dd>
        </div>
    </dl>

    {{-- Description --}}
    @if ($book->description)
        <div class="border-t border-gray-200 pt-5">
            <h3 class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Description') }}</h3>
            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-700">
                {{ $book->description }}
            </p>
        </div>
    @endif
</div>
