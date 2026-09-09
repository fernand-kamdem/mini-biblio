<!-- ============================================ -->
<!-- HEADER - Barre de navigation supérieure      -->
<!-- ============================================ -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="flex min-w-0 items-center justify-between gap-2 px-3 py-3 sm:gap-4 sm:px-6 sm:py-4">

        <!-- Partie gauche : Titre de la page + bouton mobile -->
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-4">
            <button
                id="sidebarToggle"
                type="button"
                class="shrink-0 rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 lg:hidden"
            >
                <i class="fas fa-bars text-xl"></i>
            </button>

            <div class="min-w-0 flex-1">
                <h2 class="truncate text-base font-semibold text-gray-900 sm:text-lg">
                    @yield('page-title', 'Dashboard')
                </h2>
                <p class="mt-0.5 hidden truncate text-sm text-gray-500 sm:block">
                    @yield('page-subtitle', 'Bienvenue sur votre espace de gestion')
                </p>
            </div>
        </div>

        <!-- Partie droite : Actions et notifications -->
        <div class="flex shrink-0 items-center gap-1 sm:gap-3">

            <!-- Barre de recherche -->
            <div class="hidden items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 transition-all duration-300 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500 md:flex">
                <i class="fas fa-search text-sm text-gray-400"></i>
                <input type="text" placeholder="Rechercher..." class="w-40 border-none bg-transparent text-sm outline-none transition-all duration-300 focus:w-56 lg:w-48 lg:focus:w-64">
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button type="button" class="relative rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">3</span>
                </button>
            </div>

            <!-- Messages -->
            <button type="button" class="relative rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600">
                <i class="fas fa-envelope text-lg"></i>
                <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-indigo-500 text-[10px] font-bold text-white">5</span>
            </button>

            <!-- Séparateur -->
            <div class="mx-1 hidden h-8 w-px bg-gray-200 sm:block"></div>

            <!-- Profil utilisateur avec menu déroulant -->
            <div class="relative shrink-0" x-data="{ open: false }">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-lg p-1 transition-colors hover:bg-gray-50"
                    @click="open = !open"
                >
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 text-sm font-semibold text-white">
                        {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
                    </div>
                    <div class="hidden min-w-0 text-left lg:block">
                        <p class="max-w-[10rem] truncate text-sm font-medium text-gray-900">
                            {{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}
                        </p>
                        <p class="max-w-[10rem] truncate text-xs text-gray-500">
                            {{ auth()->user() ? auth()->user()->email : '' }}
                        </p>
                    </div>
                    <i
                        class="fas fa-chevron-down hidden text-xs text-gray-400 transition-transform lg:inline"
                        :class="open ? 'rotate-180' : ''"
                    ></i>
                </button>

                <!-- Menu déroulant -->
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
                    class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 bg-white py-1 shadow-lg"
                >
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user() ? auth()->user()->email : '' }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-indigo-50 hover:text-indigo-700">
                            <i class="fas fa-user-circle w-4 text-gray-400"></i>
                            {{ __('Profile') }}
                        </a>
                    </div>

                    <div class="border-t border-gray-100"></div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 transition-colors hover:bg-red-50 hover:text-red-700">
                                <i class="fas fa-sign-out-alt w-4 text-red-400"></i>
                                {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
