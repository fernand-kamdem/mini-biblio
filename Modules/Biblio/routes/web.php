<?php

use Illuminate\Support\Facades\Route;
use Modules\Biblio\Http\Controllers\AuthorController;
use Modules\Biblio\Http\Controllers\BookController;
use Modules\Biblio\Http\Controllers\CategoryController;

Route::middleware(['auth', 'verified'])->prefix('biblio')->group(function () {

    Route::prefix('categories')
        ->name('biblio-category.')
        ->group(function () {
            Route::get('', [CategoryController::class, 'index'])
                ->name('list')
                ->middleware('permission:voir-catégories');
        });

    Route::prefix('authors')
        ->name('biblio-author.')
        ->group(function () {
            Route::get('', [AuthorController::class, 'index'])
                ->name('list')
                ->middleware('permission:voir-auteurs');
        });

    Route::prefix('books')
        ->name('biblio-book.')
        ->group(function () {
            Route::get('', [BookController::class, 'index'])
                ->name('list')
                ->middleware('permission:voir-livres');
        });
});
