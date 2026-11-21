<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Livewire\EmpruntList;
use Modules\Emprunt\Models\Emprunt;

test('the late filter keeps only the late loans', function () {
    Emprunt::factory()->enRetard()->for(Book::factory()->state(['titre' => 'Les Misérables']))->create();
    Emprunt::factory()->approuve()->for(Book::factory()->state(['titre' => 'Germinal']))->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    Livewire::test(EmpruntList::class)
        ->set('onlyLate', '1')
        ->assertSee('Les Misérables')
        ->assertDontSee('Germinal');
});

test('loans can be searched by book title', function () {
    Emprunt::factory()->enAttente()->for(Book::factory()->state(['titre' => 'Americanah']))->create();
    Emprunt::factory()->enAttente()->for(Book::factory()->state(['titre' => 'Germinal']))->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    Livewire::test(EmpruntList::class)
        ->set('search', 'Americ')
        ->assertSee('Americanah')
        ->assertDontSee('Germinal');
});
