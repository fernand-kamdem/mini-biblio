@if (config('demo.enabled'))
    <div class="border-b border-amber-200 bg-amber-50 px-4 py-2 text-center text-xs text-amber-900 sm:text-sm" role="note">
        <i class="fas fa-flask mr-1" aria-hidden="true"></i>
        {{ __('Demo version: fictitious data, reset every night.') }}
    </div>
@endif
