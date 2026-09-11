<?php

use Illuminate\Support\Facades\Route;
use Modules\Emprunt\Http\Controllers\MemberController;
use Modules\Emprunt\Http\Controllers\EmpruntController;
use Modules\Emprunt\Http\Controllers\LibrarianController;


Route::middleware(['auth', 'verified'])->prefix('emprunt')->group(function () {

    Route::prefix('members')
        ->name('emprunt-member.')
        ->group(function () {
            Route::get('', [MemberController::class, 'index'])
                ->name('list')
                ->middleware('permission:voir-membres');
        });


    Route::prefix('librarians')
        ->name('emprunt-librarian.')
        ->group(function () {
            Route::get('', [LibrarianController::class, 'index'])
                ->name('list')
                ->middleware('permission:gérer-bibliothécaires');
        });

    
    Route::prefix('emprunts')
        ->controller(EmpruntController::class)
        ->group(function () {
            Route::get('', 'index')
                ->name('emprunt-emprunt.list')
                ->middleware('permission:gérer-emprunts');
            Route::get('/mes-emprunts', 'myLoans')
                ->name('emprunt-mesemprunts.list')
                ->middleware('permission:voir-mes-emprunts');
        });

});
