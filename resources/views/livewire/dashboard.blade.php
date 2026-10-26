@php
    // --- Switch de prévisualisation (à retirer quand Livewire fournit $role) ---
    // $role = strolower($user?->getRoleNames()->first() ?? '');
    $role = 'membre';

    $roleLabel = match ($role) {
        'admin' => __('Administrator'),
        'bibliothecaire' => __('Librarian'),
        default => __('Member'),
    };

    $roleBadge = match ($role) {
        'admin' => 'bg-indigo-100 text-indigo-800',
        'bibliothecaire' => 'bg-blue-100 text-blue-800',
        default => 'bg-emerald-100 text-emerald-800',
    };

    $roleIcon = match ($role) {
        'admin' => 'fa-user-shield',
        'bibliothecaire' => 'fa-user-tie',
        default => 'fa-user',
    };

    // --- KPIs ---
    $kpis = match ($role) {
        'admin' => [
            ['value' => $totalPendingLoans,  'label' => __('Pending loans'),    'tone' => 'text-amber-600', 'href' => route('emprunt-emprunt.list', ['status' => 'en_attente'])],
            ['value' => $totalLateLoans,     'label' => __('Late loans'),       'tone' => 'text-red-600',   'href' => route('emprunt-emprunt.list', ['onlyLate' => true])],
            ['value' => $totalApproveLoans,  'label' => __('Active loans'),     'tone' => 'text-gray-900',  'href' => route('emprunt-emprunt.list', ['status' => 'approuve'])],
            ['value' => $totalBooks,         'label' => __('Available copies'), 'tone' => 'text-gray-900',  'href' => route('biblio-book.list')],
            ['value' => $totalMembers,       'label' => __('Active members'),   'tone' => 'text-gray-900',  'href' => route('emprunt-member.list')],
            ['value' => $totalLibrarians,    'label' => __('Librarians'),       'tone' => 'text-gray-900',  'href' => route('emprunt-librarian.list')],
        ],
        'bibliothecaire' => [
            ['value' => $totalPendingLoans,  'label' => __('To process (pending)'), 'tone' => 'text-amber-600'],
            ['value' => 12, 'label' => __('Approved loans out'),   'tone' => 'text-blue-600'],
            ['value' => 3,  'label' => __('Late'),                 'tone' => 'text-red-600'],
            ['value' => 4,  'label' => __('Out of stock'),         'tone' => 'text-gray-900'],
        ],
        default => [
            ['value' => 2, 'label' => __('My active loans'), 'tone' => 'text-gray-900'],
            ['value' => 1, 'label' => __('Late'),            'tone' => 'text-red-600'],
            ['value' => 1, 'label' => __('Pending'),         'tone' => 'text-amber-600'],
            ['value' => 0, 'label' => __('Drafts'),          'tone' => 'text-gray-900'],
        ],
    };

    // --- Listes membre ---
    $myActiveLoans = [
        ['book' => 'Le Petit Prince', 'loaned' => '2026-09-10', 'due' => '2026-09-25', 'status' => 'approved'],
        ['book' => '1984',            'loaned' => '2026-08-20', 'due' => '2026-09-01', 'status' => 'late'],
    ];

    $upcomingReturns = [
        ['book' => 'Le Petit Prince', 'date' => '2026-09-25'],
        ['book' => 'Les Misérables',  'date' => '2026-09-28'],
        ['book' => 'Madame Bovary',   'date' => '2026-10-05'],
    ];

    $lateCount = 1;

    $isStaff = in_array($role, ['admin', 'bibliothecaire'], true);
@endphp

<div class="space-y-6">
    {{-- En-tête --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
                {{ __('Hello').' '.$user->name.'!' }}
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                @if ($role === 'admin')
                    {{ __('Overview of library activity') }}
                @elseif ($role === 'bibliothecaire')
                    {{ __('Your daily workspace') }}
                @else
                    {{ __('Your reading space') }}
                @endif
            </p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-medium {{ $roleBadge }}">
            <i class="fas {{ $roleIcon }} mr-1.5 text-[10px]"></i>
            {{ $roleLabel }}
        </span>
    </div>

    {{-- Alerte retard (membre) --}}
    @if ($role === 'membre' && $lateCount > 0)
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
            <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
            <p class="text-sm text-red-800">
                {{ __('You have :count late loan(s)', ['count' => $lateCount]) }}
            </p>
        </div>
    @endif

    {{-- KPIs --}}
    <div @class([
        'grid gap-4 grid-cols-2',
        'md:grid-cols-3 xl:grid-cols-6' => $role === 'admin',
        'md:grid-cols-4' => $role !== 'admin',
    ])>
        @foreach ($kpis as $kpi)
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-2xl font-bold {{ $kpi['tone'] }}">{{ $kpi['value'] }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $kpi['label'] }}</p>
                @if (! empty($kpi['href']))
                    <a href="{{ $kpi['href'] }}" class="mt-2 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800">
                        {{ __('View') }} &rarr;
                    </a>
                @endif
            </div>
        @endforeach
    </div>

    @if ($isStaff)
        {{-- ============================================================ --}}
        {{-- LISTE A : Demandes en attente (desktop + mobile) --}}
        {{-- ============================================================ --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-hourglass-half mr-2 text-amber-500"></i>
                    {{ $role === 'bibliothecaire' ? __("Today's queue — requests") : __('Pending requests') }}
                </h2>
                <a href="{{ route('emprunt-emprunt.list', ['status' => 'en_attente']) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
            </div>

            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Member') }}</th>
                            @if ($role === 'bibliothecaire')
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Card number') }}</th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Desired return date') }}</th>
                            @if ($role === 'admin')
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($pendingLoans as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $row?->member?->user?->name }}</td>
                                @if ($role === 'bibliothecaire')
                                    <td class="px-4 py-3 text-gray-600">
                                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">{{ $row?->member?->card_number }}</code>
                                    </td>
                                @endif
                                <td class="px-4 py-3 text-gray-700">{{ $row?->book?->titre }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $row?->date_retour_souhaitee?->format('d/m/Y') }}</td>
                                @if ($role === 'admin')
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            {{ ucfirst(str_replace('_', ' ', $row->statut->value)) }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $role === 'bibliothecaire' ? 4 : 4 }}" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('No pending requests') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="divide-y divide-gray-200 md:hidden">
                @forelse ($pendingLoans as $row)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900">
                                    {{ $row?->member?->user?->name }}
                                </p>
                                @if ($role === 'bibliothecaire' && $row?->member?->card_number)
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        <code class="rounded bg-gray-100 px-1.5 py-0.5">{{ $row?->member?->card_number }}</code>
                                    </p>
                                @endif
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                                {{ __('Pending') }}
                            </span>
                        </div>
                        <p class="mt-2 truncate text-sm text-gray-700">
                            <i class="fas fa-book mr-1 text-xs text-gray-400"></i>
                            {{ $row?->book?->titre }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ __('Return') }} : {{ $row?->date_retour_souhaitee?->format('d/m/Y') }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        <i class="fas fa-inbox text-2xl text-gray-300 mb-2 block"></i>
                        {{ __('No pending requests') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- LISTE B : Retards (desktop + mobile) --}}
        {{-- ============================================================ --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                    {{ $role === 'bibliothecaire' ? __('Returns / late') : __('Late loans') }}
                </h2>
                <a href="{{ route('emprunt-emprunt.list', ['onlyLate' => true]) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
            </div>

            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Member') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Expected return') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ $role === 'bibliothecaire' ? __('Status') : __('Days late') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($lateLoans as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $row?->member?->user?->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $row?->book?->titre }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $row?->date_retour_prevue?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if ($role === 'bibliothecaire')
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                            {{ __('Late') }}
                                        </span>
                                    @else
                                        <span class="font-medium text-red-600">
                                            {{ now()->diffInDays($row->date_retour_prevue, false) }} {{ __('days') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('No late loans') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="divide-y divide-gray-200 md:hidden">
                @forelse ($lateLoans as $row)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-gray-900">{{ $row?->member?->user?->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500">
                                    <i class="fas fa-book mr-1 text-[10px]"></i>
                                    {{ $row?->book?->titre }}
                                </p>
                            </div>
                            @if ($role === 'bibliothecaire')
                                <span class="inline-flex shrink-0 items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                    <i class="fas fa-exclamation-circle mr-1 text-[10px]"></i>
                                    {{ __('Late') }}
                                </span>
                            @else
                                <span class="shrink-0 text-xs font-medium text-red-600">
                                    {{ now()->diffInDays($row->date_retour_prevue, false) }} {{ __('days') }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ __('Return') }} : {{ $row?->date_retour_prevue?->format('d/m/Y') }}
                        </p>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">
                        <i class="fas fa-check-circle text-2xl text-green-300 mb-2 block"></i>
                        {{ __('No late loans') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- LISTE C : Stock bas (desktop = ul, mobile = ul aussi) --}}
        {{-- ============================================================ --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-box-open mr-2 text-gray-500"></i>
                    {{ $role === 'bibliothecaire' ? __('Critical stock') : __('Low stock') }}
                </h2>
                <a href="{{ route('biblio-book.list') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
            </div>
            <ul class="divide-y divide-gray-200">
                @forelse ($lowStock as $item)
                    <li class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                        <span class="min-w-0 flex-1 truncate text-sm text-gray-900">{{ $item->titre }}</span>
                        <span @class([
                            'inline-flex shrink-0 items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            'bg-red-100 text-red-800' => $item->stock_disponible === 0,
                            'bg-amber-100 text-amber-800' => $item->stock_disponible > 0,
                        ])>
                            {{ $item->stock_disponible }}
                        </span>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-gray-500">
                        {{ __('No low stock') }}
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Raccourcis staff --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('emprunt-emprunt.list') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-hand-holding text-indigo-600"></i> {{ __('Loans') }}
            </a>
            <a href="{{ route('emprunt-member.list') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-users text-indigo-600"></i> {{ __('Members') }}
            </a>
            <a href="{{ route('biblio-book.list') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <i class="fas fa-book text-indigo-600"></i> {{ __('Books') }}
            </a>
            @if ($role === 'admin')
                <a href="{{ route('emprunt-librarian.list') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-user-tie text-indigo-600"></i> {{ __('Librarians') }}
                </a>
            @endif
        </div>

    @else
        {{-- ============================================================ --}}
        {{-- VUE MEMBRE --}}
        {{-- ============================================================ --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-book-open mr-2 text-indigo-600"></i>
                    {{ __('My active loans') }}
                </h2>
                <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">{{ __('View all') }}</a>
            </div>

            {{-- Desktop --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Book') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Loan date') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Expected return') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($myActiveLoans as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-900">{{ $row['book'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $row['loaned'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $row['due'] }}</td>
                                <td class="px-4 py-3">
                                    @if ($row['status'] === 'late')
                                        <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-red-600"></span>
                                            {{ __('Late') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                            <span class="mr-1 h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                                            {{ __('Approved') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="divide-y divide-gray-200 md:hidden">
                @foreach ($myActiveLoans as $row)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">
                            <p class="min-w-0 flex-1 truncate font-medium text-gray-900">{{ $row['book'] }}</p>
                            @if ($row['status'] === 'late')
                                <span class="inline-flex shrink-0 items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">
                                    {{ __('Late') }}
                                </span>
                            @else
                                <span class="inline-flex shrink-0 items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                    {{ __('Approved') }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-calendar-alt text-[10px]"></i>
                                {{ $row['loaned'] }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-undo text-[10px]"></i>
                                {{ $row['due'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Prochains retours --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 py-3 sm:px-6">
                <h2 class="text-sm font-semibold text-gray-900">
                    <i class="fas fa-clock mr-2 text-indigo-600"></i>
                    {{ __('Upcoming returns') }}
                </h2>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach ($upcomingReturns as $item)
                    <li class="flex items-center justify-between gap-3 px-4 py-3 sm:px-6">
                        <span class="min-w-0 flex-1 truncate text-sm text-gray-900">{{ $item['book'] }}</span>
                        <span class="shrink-0 text-xs text-gray-500">{{ $item['date'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- CTA + raccourcis --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto">
                <i class="fas fa-plus"></i>
                {{ __('Borrow a book') }}
            </a>
            <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                {{ __('View my loans') }}
            </a>
            <a href="#" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">
                <i class="fas fa-book text-indigo-600"></i> {{ __('Catalog') }}
            </a>
        </div>
    @endif
</div>