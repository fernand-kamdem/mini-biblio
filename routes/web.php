<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

//--- modification de la locale
Route::get('/lang/{locale}', [LocaleController::class, 'setLocale'])->name('change.language');

require __DIR__.'/auth.php';
