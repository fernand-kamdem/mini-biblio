@if (config('app.user_internationalisation'))
    @php
        $currentLocale = app()->getLocale();
    @endphp

    <div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border border-gray-200 bg-white p-1 text-sm shadow-sm']) }}>
        <a
            href="{{ route('change.language', 'fr') }}"
            class="rounded-full px-3 py-1 font-medium transition {{ $currentLocale === 'fr' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}"
            @if ($currentLocale === 'fr') aria-current="true" @endif
        >
            {{ __('French') }}
        </a>
        <a
            href="{{ route('change.language', 'en') }}"
            class="rounded-full px-3 py-1 font-medium transition {{ $currentLocale === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}"
            @if ($currentLocale === 'en') aria-current="true" @endif
        >
            {{ __('English') }}
        </a>
    </div>
@endif
