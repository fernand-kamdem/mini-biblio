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
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Email') }}</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 break-words">
                                <code class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">{{ $email }}</code>
                            </dd>
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
    </div>
</div>