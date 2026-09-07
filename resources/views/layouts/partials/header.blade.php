<!-- ============================================ -->
<!-- HEADER - Barre de navigation supérieure      -->
<!-- ============================================ -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="px-6 py-4 flex items-center justify-between">
        
        <!-- Partie gauche : Titre de la page + bouton mobile -->
        <div class="flex items-center gap-4">
            <!-- Bouton toggle sidebar (mobile) -->
            <button id="sidebarToggle" class="lg:hidden text-gray-500 hover:text-gray-700 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                <p class="text-sm text-gray-500 hidden sm:block">@yield('page-subtitle', 'Bienvenue sur votre espace de gestion')</p>
            </div>
        </div>

        <!-- Partie droite : Actions et notifications -->
        <div class="flex items-center gap-3">
            
            <!-- Barre de recherche -->
            <div class="hidden md:flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2 transition-all duration-300 focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500">
                <i class="fas fa-search text-gray-400 text-sm"></i>
                <input type="text" placeholder="Rechercher..." class="bg-transparent border-none outline-none text-sm w-48 focus:w-64 transition-all duration-300">
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                </button>
            </div>

            <!-- Messages -->
            <button class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
                <i class="fas fa-envelope text-lg"></i>
                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-indigo-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">5</span>
            </button>

            <!-- Language Switcher -->
            <x-language-switcher />

            <!-- Séparateur -->
            <div class="w-px h-8 bg-gray-200 hidden sm:block"></div>

            <!-- Profil utilisateur avec menu déroulant -->
            <div class="relative" x-data="{ open: false }">
                <div class="flex items-center gap-2 cursor-pointer group" @click="open = !open">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                        {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600 transition-colors">
                            {{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}
                        </p>
                        <p class="text-xs text-gray-500">{{ auth()->user() ? auth()->user()->email : '' }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 transition-colors hidden sm:block" 
                       :class="open ? 'rotate-180' : ''"></i>
                </div>

                <!-- Menu déroulant -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50 origin-top-right">
                    
                    <!-- En-tête du menu -->
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user() ? auth()->user()->email : '' }}</p>
                    </div>

                    <!-- Liens du menu -->
                    <div class="py-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                            <i class="fas fa-user-circle w-4 text-gray-400"></i>
                            {{ __('Profile') }}
                        </a>
                    </div>

                    <!-- Séparateur -->
                    <div class="border-t border-gray-100"></div>

                    <!-- Déconnexion -->
                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
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