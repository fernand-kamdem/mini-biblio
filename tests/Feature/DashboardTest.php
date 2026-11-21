<?php

use App\Models\User;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;

test('the librarian dashboard lists the real pending requests and late loans', function () {
    $demande = Emprunt::factory()->enAttente()
        ->for(Book::factory()->state(['titre' => 'Une si longue lettre']))
        ->create();
    $retard = Emprunt::factory()->enRetard()
        ->for(Book::factory()->state(['titre' => 'Les Misérables']))
        ->create();

    $response = $this->actingAs(User::factory()->bibliothecaire()->create())->get('/dashboard');

    $response->assertOk()
        ->assertSee('Une si longue lettre')
        ->assertSee($demande->member->card_number)
        ->assertSee('Les Misérables')
        ->assertSee($retard->member->user->name);
});

test('a member dashboard shows only their own loans', function () {
    $member = Member::factory()->create();
    Emprunt::factory()->enRetard()->for($member)->for(Book::factory()->state(['titre' => 'Le monde s’effondre']))->create();
    Emprunt::factory()->approuve()->for(Book::factory()->state(['titre' => 'Germinal']))->create();

    $response = $this->actingAs($member->user)->get('/dashboard');

    $response->assertOk()
        ->assertSee('Le monde s’effondre')
        ->assertDontSee('Germinal');
});
