{{--
  Vue Livewire : cloche de notifications (header).
  Composant PHP : App\Livewire\NotificationBell

  - Alpine (x-data) : ouverture / fermeture du dropdown
  - Livewire : données (unreadCount, items) + actions markAsRead / markAllAsRead
  - Badge rouge : visible seulement si unreadCount > 0
--}}
<div
    class="relative"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
>
    {{-- Bouton cloche : toggle Alpine + refresh Livewire à l’ouverture --}}
    <button
        type="button"
        class="relative rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
        @click="open = !open"
        wire:click="refreshNotifications"
        aria-label="{{ __('Notifications') }}"
    >
        <i class="fas fa-bell text-lg"></i>

        {{-- Badge : masqué si aucune non lue ; "9+" au-delà de 9 --}}
        @if ($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown liste des notifications --}}
    <div
        x-show="open"
        x-cloak
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg sm:w-96"
    >
        {{-- En-tête : titre + tout marquer comme lu --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <p class="text-sm font-semibold text-gray-900">{{ __('Notifications') }}</p>
            @if ($unreadCount > 0)
                <button
                    type="button"
                    wire:click="markAllAsRead"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800"
                >
                    {{ __('Mark all as read') }}
                </button>
            @endif
        </div>

        {{-- Corps : items ou état vide --}}
        <div class="max-h-80 overflow-y-auto">
            @forelse ($items as $item)
                {{-- Clic → markAsRead (Livewire) + ferme le dropdown (Alpine) --}}
                <button
                    type="button"
                    wire:click="markAsRead('{{ $item['id'] }}')"
                    @click="open = false"
                    class="flex w-full flex-col gap-0.5 border-b border-gray-50 px-4 py-3 text-left transition-colors hover:bg-indigo-50"
                >
                    {{-- Message déjà traduit dans le composant PHP --}}
                    <span class="text-sm font-medium text-gray-900">{{ $item['message'] }}</span>

                    {{-- Métadonnées optionnelles du payload (livre / membre) --}}
                    @if ($item['book_title'] || $item['member_name'])
                        <span class="text-xs text-gray-500">
                            @if ($item['book_title'])
                                {{ $item['book_title'] }}
                            @endif
                            @if ($item['book_title'] && $item['member_name'])
                                ·
                            @endif
                        </span>
                    @endif

                    @if ($item['created_at'])
                        <span class="text-[11px] text-gray-400">{{ $item['created_at'] }}</span>
                    @endif
                </button>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-500">
                    <i class="fas fa-bell-slash mb-2 text-lg text-gray-300"></i>
                    <p>{{ __('No notifications') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
