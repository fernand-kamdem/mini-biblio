<footer class="mt-auto border-t border-gray-200 bg-white px-6 py-4">
    <div class="flex flex-col items-center justify-between gap-2 text-sm text-gray-500 sm:flex-row">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}</p>
        <p class="text-xs">
            {{ __('Designed and developed by') }}
            <a href="https://fernandkamdem.com" target="_blank" rel="noopener" class="font-medium text-indigo-600 transition-colors hover:text-indigo-700">Fernand Kamdem</a>
        </p>
    </div>
</footer>
