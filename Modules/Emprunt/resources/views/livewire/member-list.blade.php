<div>
                
    <x-alert-message />
    <!-- Barre de recherche et bouton ajouter -->
    <div class="flex flex-col gap-3 md:flex-row md:items-end">
        <div class="flex-1">
            <label for="member_search" class="mb-1 block text-sm font-medium text-gray-700">
               {{ __('Search an member') }}
            </label>
            <input
                id="member_search"
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="{{__('Search by name or card number...') }}"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
            >
        </div>
        <div class="w-full md:w-auto">
            <button
                type="button"
                wire:click="openCreateModal"
                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 md:w-auto"
            >
                <i class="fas fa-plus"></i>
                {{ __('New member') }}
            </button>
        </div>
    </div>

     <!-- En-tête du tableau -->
     <div class="mt-4 flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-users text-indigo-600"></i>
            <h3 class="text-base font-semibold text-gray-900">{{ __('List of memebers') }}</h3>
            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600">{{$totalMembers}}</span>
        </div>
    </div>

    <!-- Tableau -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 sm:table-cell">{{ __('numMember') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('nameMember') }}</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 md:table-cell">{{ __('cardMember') }}</th>
                    <th class="hidden px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 md:table-cell">{{ __('phoneMember') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('actionsMember') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="hidden px-4 py-3 sm:table-cell"><span class="text-xs font-medium text-gray-500">{{ '#'.$loop->iteration}}</span></td>
                        <td class="px-4 py-3">{{$member?->user?->name}}</td>
                        <td class="hidden px-4 py-3 sm:table-cell"><p class="font-medium text-gray-900">{{$member->card_number}}</p></td>
                        <td class="hidden px-4 py-3 md:table-cell"><span class="text-sm text-gray-600">{{$member->phone}}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <button type="button" wire:click="openDetailsModal({{ $member->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600" title="{{ __('View') }}"><i class="fas fa-eye text-sm"></i></button>
                                <button type="button" wire:click="openEditModal({{ $member->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600" title="{{ __('Edit') }}"><i class="fas fa-edit text-sm"></i></button>
                                <button type="button" wire:click="openConfirmDeleteModal({{ $member->id }})" class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600" title="{{ __('Delete') }}"><i class="fas fa-trash text-sm"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('noMemberFound') }}</td>
                    </tr>
                    @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    {{-- Pagination Laravel (thème Tailwind côté composant PHP) --}}
        <div class="border-t border-blue-200 bg-blue-50 px-4 py-3">
            {{$members->links()}}
        </div>

    <!-- Modale de création/édition -->
    @if($showModalForm)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
                        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                            <h4 class="text-lg font-semibold text-gray-900">
                                {{ $editingId ? __('Edit member') : __('New member') }}
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
                            {{-- :key = recrée le formulaire quand on change create ↔ edit --}}
                            <livewire:emprunt::member-form
                                :member-id="$editingId"
                                :key="'member-form-'.($editingId ?? 'new')"
                            />
                        </div>
                    </div>
                </div>
            @endif


    @if ($showModalDetails)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6 sm:py-4">
                    <h4 class="text-lg font-semibold text-gray-900">
                        {{ __('Member detail') }}
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
                    <livewire:emprunt::member-show
                        :member-id="$viewingId"
                        :key="'member-show-'.$viewingId"
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
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Delete Member') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('Are you sure you want to delete this member? This action cannot be undone.') }}
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
                            wire:click="deleteMember"
                            class="w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 sm:w-auto"
                        >
                            <i class="fas fa-trash mr-2"></i>{{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
</div>