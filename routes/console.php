<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Démonstration publique : données fictives remises à zéro chaque nuit.
Schedule::command('demo:reset --force')
    ->dailyAt(config('demo.reset_at'))
    ->when(fn (): bool => (bool) config('demo.enabled'))
    ->withoutOverlapping();
