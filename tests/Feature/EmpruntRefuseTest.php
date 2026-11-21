<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Livewire\EmpruntRefuse;
use Modules\Emprunt\Models\Emprunt;

test('a failed refusal reports the error and does not save the reason', function () {
    $emprunt = Emprunt::factory()->approuve()->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    Livewire::test(EmpruntRefuse::class, ['empruntId' => $emprunt->id])
        ->set('note_bibliothecaire', 'Exemplaire abîmé')
        ->call('refuse')
        ->assertDispatched('errorRefuse')
        ->assertNotDispatched('successRefuse');

    $emprunt->refresh();
    expect($emprunt->statut)->toBe(StatutEmprunt::Approuve)
        ->and($emprunt->note_bibliothecaire)->toBeNull();
});
