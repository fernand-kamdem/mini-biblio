<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Livewire\EmpruntApprove;
use Modules\Emprunt\Models\Emprunt;

test('approving saves the librarian note and reports success', function () {
    $book = Book::factory()->create(['stock_total' => 1, 'stock_disponible' => 1]);
    $emprunt = Emprunt::factory()->enAttente()->for($book)->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    Livewire::test(EmpruntApprove::class, ['empruntId' => $emprunt->id])
        ->set('note_bibliothecaire', 'À retirer à l’accueil')
        ->call('approve')
        ->assertDispatched('successApprove')
        ->assertNotDispatched('errorApprove');

    $emprunt->refresh();
    expect($emprunt->statut)->toBe(StatutEmprunt::Approuve)
        ->and($emprunt->note_bibliothecaire)->toBe('À retirer à l’accueil');
});

test('a failed approval reports the error and leaves the loan untouched', function () {
    $book = Book::factory()->create(['stock_total' => 1, 'stock_disponible' => 0]);
    $emprunt = Emprunt::factory()->enAttente()->for($book)->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    Livewire::test(EmpruntApprove::class, ['empruntId' => $emprunt->id])
        ->set('note_bibliothecaire', 'À retirer à l’accueil')
        ->call('approve')
        ->assertDispatched('errorApprove')
        ->assertNotDispatched('successApprove');

    $emprunt->refresh();
    expect($emprunt->statut)->toBe(StatutEmprunt::EnAttente)
        ->and($emprunt->note_bibliothecaire)->toBeNull();
    expect($book->refresh()->stock_disponible)->toBe(0);
});
