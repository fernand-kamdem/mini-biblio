
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed inset-y-0 left-0 z-30 transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
    
    <!-- Logo et nom de l'application -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <i class="fas fa-book-open text-lg text-white"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Biblio</h1>
            <p class="text-xs text-gray-500">GestFK</p>
        </div>
    </div>

    <!-- Navigation principale -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fas fa-th-large w-5 text-center text-lg"></i>
            <span>Tableau de bord</span>
        </a>

        <!-- Section : Gestion des livres -->
        <p class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Catalogue') }}</p>

        @can('voir-livres')
            <a href="{{ route('biblio-book.list') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('biblio-book.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-book w-5 text-center text-lg"></i>
                <span>{{ __('Books') }}</span>
            </a>
        @endcan

        @can('voir-auteurs')
            <a href="{{ route('biblio-author.list') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('biblio-author.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-pen-fancy w-5 text-center text-lg"></i>
                <span>{{ __('Authors') }}</span>
            </a>
        @endcan

        @can('voir-catégories')
            <a href="{{ route('biblio-category.list') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('biblio-category.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <i class="fas fa-tags w-5 text-center text-lg"></i>
                <span>{{ __('Categories') }}</span>
            </a>
        @endcan

        <!-- Section : Gestion des membres -->
        <p class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Members') }}</p>
        
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">
            <i class="fas fa-users w-5 text-center text-lg"></i>
            <span>{{ __('All members') }}</span>
        </a>

        <!-- Section : Gestion des emprunts -->
        <p class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Loans') }}</p>
        
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">
            <i class="fas fa-hand-holding-heart w-5 text-center text-lg"></i>
            <span>{{ __('Active loans') }}</span>
        </a>
        
        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900">
            <i class="fas fa-clock w-5 text-center text-lg"></i>
            <span>{{ __('Overdue') }}</span>
        </a>

        <!-- Section : Paramètres -->
        <p class="px-4 pt-6 pb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('Settings') }}</p>
        
        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('profile') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i class="fas fa-cog w-5 text-center text-lg"></i>
            <span>{{ __('Profile') }}</span>
        </a>
    </nav>

    <!-- Footer de la sidebar - Profil utilisateur -->
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-semibold text-sm">
                {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'U' }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ auth()->user() ? auth()->user()->name : 'Utilisateur' }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                    {{ auth()->user() ? auth()->user()->email : '' }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>