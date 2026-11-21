<div>
    @if ($this->emprunt)
        @php
            $emprunt = $this->emprunt;
            $stockDisponible = $emprunt->book?->stock_disponible ?? 0;
        @endphp

        <form wire:submit="approve" class="space-y-5">
            {{-- Récapitulatif de la demande --}}
            <dl class="grid gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Member') }}</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $emprunt->member?->user?->name }}</dd>
                    <dd class="text-xs text-gray-500">{{ $emprunt->member?->card_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $emprunt->book?->titre }}</dd>
                    <dd @class(['text-xs', 'text-red-600' => $stockDisponible === 0, 'text-gray-500' => $stockDisponible > 0])>
                        {{ __(':available of :total copies available', ['available' => $stockDisponible, 'total' => $emprunt->book?->stock_total ?? 0]) }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Desired return date') }}</dt>
                    <dd class="mt-1 tabular-nums text-gray-900">{{ $emprunt->date_retour_souhaitee?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Expected return if approved') }}</dt>
                    <dd class="mt-1 font-medium tabular-nums text-gray-900">{{ $dateRetourPrevue->format('d/m/Y') }}</dd>
                    <dd class="text-xs text-gray-500">{{ __(':days days after the loan', ['days' => $dureeEmprunt]) }}</dd>
                </div>
            </dl>

            @if ($stockDisponible === 0)
                <div class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-3">
                    <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                    <p class="text-sm text-red-700">{{ __('No copy of this book is available. Refuse the request or wait for a return.') }}</p>
                </div>
            @else
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3">
                    <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                    <p class="text-sm text-blue-700">{{ __('Approving takes one copy out of stock and notifies the member.') }}</p>
                </div>
            @endif

            {{-- Note du bibliothécaire --}}
            <div>
                <label for="note_bibliothecaire" class="block text-sm font-medium text-gray-700">
                    {{ __('Librarian note') }} <span class="text-xs text-gray-400">{{ __('(Optional)') }}</span>
                </label>
                <div class="relative mt-1">
                    <div class="pointer-events-none absolute top-3 left-3">
                        <i class="fas fa-comment text-sm text-gray-400"></i>
                    </div>
                    <textarea
                        id="note_bibliothecaire"
                        wire:model="note_bibliothecaire"
                        rows="3"
                        placeholder="{{ __('For example: copy to collect at the front desk.') }}"
                        class="block w-full resize-y rounded-lg border-gray-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-0"
                    ></textarea>
                </div>
                @error('note_bibliothecaire')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-end">
                <button
                    type="button"
                    wire:click="requestCancelApprove"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto"
                >
                    {{ __('Cancel') }}
                </button>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="approve"
                    @disabled($stockDisponible === 0)
                    class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                >
                    <span wire:loading.remove wire:target="approve">
                        <i class="fas fa-check mr-2"></i>
                        {{ __('Approve') }}
                    </span>
                    <span wire:loading wire:target="approve">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        {{ __('Approving...') }}
                    </span>
                </button>
            </div>
        </form>
    @else
        <p class="text-sm text-gray-500">{{ __('This loan no longer exists.') }}</p>
    @endif
</div>
