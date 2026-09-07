<?php

use Illuminate\Support\Facades\Route;
use Modules\Emprunt\Http\Controllers\MemberController;


Route::middleware(['auth', 'verified'])->prefix('emprunt')->group(function () {

    Route::prefix('members')
        ->name('emprunt-member.')
        ->group(function () {
            Route::get('', [MemberController::class, 'index'])
                ->name('list')
                ->middleware('permission:voir-membres');
        });
});
