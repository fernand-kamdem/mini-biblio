<?php

use Modules\Biblio\Models\Author;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Models\Member;

test('the catalog search from the address bar matches an author name', function () {
    $oyono = Author::factory()->create(['prenom' => 'Ferdinand', 'nom' => 'Oyono']);
    Book::factory()->hasAttached($oyono)->create(['titre' => 'Une vie de boy']);
    Book::factory()->create(['titre' => 'Germinal']);

    $response = $this->actingAs(Member::factory()->create()->user)->get('/biblio/books?search=Oyono');

    $response->assertOk()
        ->assertSee('Une vie de boy')
        ->assertDontSee('Germinal');
});
