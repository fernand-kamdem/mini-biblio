<?php

use Modules\Emprunt\Models\Emprunt;

test('a loan is late only when it is still out after its expected return date', function (string $etat, string $dateRetourPrevue, bool $enRetard) {
    $this->travelTo('2026-11-23 10:00:00');
    $emprunt = Emprunt::factory()->{$etat}()->create(['date_retour_prevue' => $dateRetourPrevue]);

    $trouve = Emprunt::query()->enRetard()->whereKey($emprunt->id)->exists();

    expect($trouve)->toBe($enRetard)
        ->and($emprunt->estEnRetard())->toBe($enRetard);
})->with([
    'out, due yesterday' => ['approuve', '2026-11-22', true],
    'out, due today' => ['approuve', '2026-11-23', false],
    'returned, due yesterday' => ['rendu', '2026-11-22', false],
]);
