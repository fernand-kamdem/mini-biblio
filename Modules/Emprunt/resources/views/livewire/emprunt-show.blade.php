<div class="w-full space-y-6">
    <!-- En-tête avec informations principales et actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 truncate">
                    Emprunt
                </h1>
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800 whitespace-nowrap">
                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                    {{ $statut }}
                </span>
            </div>
            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-book text-gray-400 text-xs"></i>
                    <span class="font-medium text-gray-700">Le Petit Prince</span>
                </span>
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-user text-gray-400 text-xs"></i>
                    <span class="font-medium text-gray-700">Jean Dupont</span>
                </span>
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                    Emprunté le 15 janvier 2024
                </span>
            </div>
        </div>
        
        <div class="flex shrink-0 gap-2">
            <button
                type="button"
                wire:click="requestCancel"
                class="inline-flex items-center gap-2 rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition-colors whitespace-nowrap"
            >
                <i class="fas fa-undo text-sm"></i>
                Annuler
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                        Informations de l'emprunt
                    </h2>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Livre</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">Le Petit Prince</dd>
                            <dd class="text-xs text-gray-500">Antoine de Saint-Exupéry</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Membre</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">Jean Dupont</dd>
                            <dd class="text-xs text-gray-500">Carte : M-2024-001</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date d'emprunt</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">15 janvier 2024</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                    <span class="mr-1 h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                    En cours
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Approuvé par</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-words">Marie Martin</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Notes -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-xs font-semibold text-gray-900">
                            <i class="fas fa-user mr-1.5 text-indigo-600"></i>
                            Note du membre
                        </h3>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-700">Livre en bon état, emprunt pour lecture personnelle.</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-xs font-semibold text-gray-900">
                            <i class="fas fa-user-tie mr-1.5 text-indigo-600"></i>
                            Note du bibliothécaire
                        </h3>
                    </div>
                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-700">Emprunt approuvé, retour prévu dans 15 jours.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-1">
        <div class="space-y-6">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>
                        Dates
                    </h2>
                </div>
                <div class="px-6 py-5">
                        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date d'emprunt</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">15 janv. 2024</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Retour souhaitée</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">30 janv. 2024</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Retour prévue</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">15 fév. 2024</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Retour réelle</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    <span class="text-gray-400">—</span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>