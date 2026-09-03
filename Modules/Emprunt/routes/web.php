<?php

use Illuminate\Support\Facades\Route;
use Modules\Emprunt\Http\Controllers\EmpruntController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('emprunts', EmpruntController::class)->names('emprunt');
});
