<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Livewire\EmpruntApprove;
use Modules\Emprunt\Livewire\EmpruntForm;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;

// Un champ <input type="date"> n'accepte que le format AAAA-MM-JJ : avec un autre format,
// le champ s'affiche vide quand on modifie un brouillon. À l'approbation, les dates sont affichées.

beforeEach(function () {
    $user = User::factory()->create();
    $user->assignRole('MEMBRE');

    $this->member = Member::create([
        'user_id' => $user->id,
        'card_number' => Member::generateCardNumber(),
        'registration_date' => now()->toDateString(),
    ]);

    $this->dateRetour = now()->addDays(10)->toDateString();
});

test('loan form prefills the desired return date in date input format', function () {
    $emprunt = Emprunt::create([
        'book_id' => Book::factory()->create()->id,
        'member_id' => $this->member->id,
        'statut' => StatutEmprunt::Brouillon,
        'workflow_state' => StatutEmprunt::Brouillon->value,
        'date_retour_souhaitee' => $this->dateRetour,
    ]);

    $this->actingAs($this->member->user);

    Livewire::test(EmpruntForm::class, ['empruntId' => $emprunt->id])
        ->assertSet('dateRetourSouhaitee', $this->dateRetour);
});

test('approval form shows the desired return date and the return date due 14 days later', function () {
    $this->travelTo('2026-11-23 10:00:00');
    $emprunt = Emprunt::create([
        'book_id' => Book::factory()->create()->id,
        'member_id' => $this->member->id,
        'statut' => StatutEmprunt::EnAttente,
        'workflow_state' => StatutEmprunt::EnAttente->value,
        'date_retour_souhaitee' => '2026-12-01',
    ]);

    $librarian = User::factory()->create();
    $librarian->assignRole('BIBLIOTHECAIRE');

    $this->actingAs($librarian);

    Livewire::test(EmpruntApprove::class, ['empruntId' => $emprunt->id])
        ->assertSee('01/12/2026')
        ->assertSee('07/12/2026');
});
