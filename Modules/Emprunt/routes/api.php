<?php

use Illuminate\Support\Facades\Route;
use Modules\Emprunt\Http\Controllers\EmpruntController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('emprunts', EmpruntController::class)->names('emprunt');
});
