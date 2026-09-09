<div class="w-full space-y-6">
    <!-- En-tête avec nom complet et actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $name }}</h1>
                @if ($is_active)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 whitespace-nowrap">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-green-600"></span>
                        {{ __('Active') }}
                    </span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 whitespace-nowrap">
                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                        {{ __('Disabled') }}
                    </span>
                @endif
            </div>
            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500">
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-id-card text-gray-400 text-xs"></i>
                    <code class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $card_number }}</code>
                </span>
                <span class="inline-flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                    {{ __('Registered on') . ' ' . $registration_date }}
                </span>
            </div>
        </div>
        
        <div class="flex shrink-0 gap-2">
            <button
                type="button"
                wire:click="openEditModal"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-100 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-200 transition-colors whitespace-nowrap"
            >
                <i class="fas fa-edit text-sm"></i>
                {{ __('Edit') }}
            </button>
            <button
                type="button"
                wire:click="openConfirmDeleteModal"
                class="inline-flex items-center gap-2 rounded-lg bg-red-100 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-200 transition-colors whitespace-nowrap"
            >
                <i class="fas fa-trash text-sm"></i>
                {{ __('Delete') }}
            </button>
        </div>
    </div>

    <!-- Grille principale -->
    <div class="space-y-6">
        <!-- Informations personnelles -->
        <div>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">
                        <i class="fas fa-user mr-2 text-indigo-600"></i>
                        {{ __('Personal information') }}
                    </h2>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Name') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">{{ $name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Card number') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">
                                <code class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $card_number }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Email') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">
                                <a href="mailto:{{ $email }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                    {{ $email }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Phone') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-words">{{ $phone }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Address') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-words">{{ $address }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Registration date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-words">{{ $registration_date }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Status') }}</dt>
                            <dd class="mt-1">
                                @if ($is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                        {{ __('Disabled') }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Carte membre et statistiques -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Carte membre -->
            <div class="overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 p-5 text-white shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium uppercase tracking-wider text-indigo-200">
                            {{ __('Library card') }}
                        </p>
                        <p class="mt-2 text-base font-bold truncate">{{ $name }}</p>
                        <p class="font-mono text-sm text-indigo-200 truncate">{{ $card_number }}</p>
                    </div>
                    <div class="ml-3 flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/20">
                        <i class="fas fa-user text-lg text-white"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-white/20 pt-3 text-xs">
                    <span class="font-medium text-white">
                        {{ $is_active ? __('Active') : __('Disabled') }}
                    </span>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="shrink-0 rounded-lg bg-blue-100 p-2">
                            <i class="fas fa-book text-sm text-blue-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Loans') }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ $total_loans ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-2">
                        <div class="shrink-0 rounded-lg bg-yellow-100 p-2">
                            <i class="fas fa-clock text-sm text-yellow-600"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Late') }}</p>
                            <p class="text-lg font-bold text-gray-900">{{ $late_loans ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des emprunts -->
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-sm font-semibold text-gray-900">
                <i class="fas fa-history mr-2 text-indigo-600"></i>
                {{ __('Loan history') }}
            </h2>
            <span class="text-xs text-gray-500 whitespace-nowrap">{{ __('Total') . ' : ' . ($total_loans ?? 0) . ' ' . __('loans') }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 whitespace-nowrap">{{ __('Borrow date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 whitespace-nowrap">{{ __('Return date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 truncate max-w-xs">Le Petit Prince</p>
                            <p class="text-xs text-gray-500 truncate max-w-xs">Antoine de Saint-Exupéry</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">01/03/2024</td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">15/03/2024</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 whitespace-nowrap">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                {{ __('Returned') }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 truncate max-w-xs">1984</p>
                            <p class="text-xs text-gray-500 truncate max-w-xs">George Orwell</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">10/02/2024</td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">—</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 whitespace-nowrap">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-yellow-600"></span>
                                {{ __('In progress') }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 truncate max-w-xs">Les Misérables</p>
                            <p class="text-xs text-gray-500 truncate max-w-xs">Victor Hugo</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">20/01/2024</td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">05/02/2024</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 whitespace-nowrap">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                {{ __('Returned') }}
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 truncate max-w-xs">L'Étranger</p>
                            <p class="text-xs text-gray-500 truncate max-w-xs">Albert Camus</p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">15/03/2024</td>
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">—</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 whitespace-nowrap">
                                <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                {{ __('Late') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-gray-700">
                    {{ __('Showing') . ' ' }}
                    <span class="font-medium">1</span>
                    {{ __('to') . ' ' }}
                    <span class="font-medium">4</span>
                    {{ __('of') . ' ' }}
                    <span class="font-medium">{{ $total_loans ?? 15 }}</span>
                    {{ __('results') }}
                </div>
                <div class="flex gap-1">
                    <button class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50" disabled>
                        {{ __('Previous') }}
                    </button>
                    <button class="rounded-lg bg-indigo-600 px-3 py-1 text-sm text-white hover:bg-indigo-700">
                        1
                    </button>
                    <button class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">
                        2
                    </button>
                    <button class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">
                        3
                    </button>
                    <button class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">
                        {{ __('Next') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>