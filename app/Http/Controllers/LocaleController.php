<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function setLocale(string $locale): RedirectResponse
    {
        if (! in_array($locale, ['en', 'fr'], true)) {
            return back();
        }

        session()->put('locale', $locale);

        if (Auth::check()) {
            Auth::user()->update([
                'language' => $locale,
            ]);
        }

        return back();
    }
}