<?php

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Event;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Events\EmpruntApprove;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;
use Symfony\Component\Workflow\Exception\LogicException;

test('approving a request takes one copy out of stock and sets the return 14 days later', function () {
    $this->travelTo('2026-11-23 10:00:00');
    $librarian = User::factory()->bibliothecaire()->create();
    $book = Book::factory()->create(['stock_total' => 2, 'stock_disponible' => 2]);
    $emprunt = Emprunt::factory()->enAttente()->for($book)->create();
    Event::fake([EmpruntApprove::class]);

    $this->actingAs($librarian);
    app(EmpruntWorkflowService::class)->apply($emprunt, 'approuver');

    $emprunt->refresh();
    expect($emprunt->statut)->toBe(StatutEmprunt::Approuve)
        ->and($emprunt->date_emprunt->toDateString())->toBe('2026-11-23')
        ->and($emprunt->date_retour_prevue->toDateString())->toBe('2026-12-07')
        ->and($emprunt->approuve_par)->toBe($librarian->id);
    expect($book->refresh()->stock_disponible)->toBe(1);
    Event::assertDispatched(EmpruntApprove::class, fn (EmpruntApprove $event) => $event->emprunt->is($emprunt));
});

test('only one of two requests for the last copy can be approved', function () {
    $book = Book::factory()->create(['stock_total' => 1, 'stock_disponible' => 1]);
    $premiere = Emprunt::factory()->enAttente()->for($book)->create();
    $seconde = Emprunt::factory()->enAttente()->for($book)->create();
    $service = app(EmpruntWorkflowService::class);
    $this->actingAs(User::factory()->bibliothecaire()->create());
    $service->apply($premiere, 'approuver');

    expect(fn () => $service->apply($seconde, 'approuver'))->toThrow(DomainException::class);

    expect($seconde->refresh()->statut)->toBe(StatutEmprunt::EnAttente)
        ->and($seconde->date_retour_prevue)->toBeNull();
    expect($book->refresh()->stock_disponible)->toBe(0);
});

test('a member cannot approve a loan', function () {
    $book = Book::factory()->create(['stock_total' => 1, 'stock_disponible' => 1]);
    $emprunt = Emprunt::factory()->enAttente()->for($book)->create();
    Event::fake([EmpruntApprove::class]);

    $this->actingAs($emprunt->member->user);

    expect(fn () => app(EmpruntWorkflowService::class)->apply($emprunt, 'approuver'))
        ->toThrow(AuthorizationException::class);
    expect($emprunt->refresh()->statut)->toBe(StatutEmprunt::EnAttente);
    expect($book->refresh()->stock_disponible)->toBe(1);
    Event::assertNotDispatched(EmpruntApprove::class);
});

test('returning a loan puts the copy back in stock', function () {
    $this->travelTo('2026-11-23 10:00:00');
    $librarian = User::factory()->bibliothecaire()->create();
    $book = Book::factory()->create(['stock_total' => 1, 'stock_disponible' => 0]);
    $emprunt = Emprunt::factory()->approuve()->for($book)->create();

    $this->actingAs($librarian);
    app(EmpruntWorkflowService::class)->apply($emprunt, 'rendre');

    $emprunt->refresh();
    expect($emprunt->statut)->toBe(StatutEmprunt::Rendu)
        ->and($emprunt->date_retour_reelle->toDateString())->toBe('2026-11-23')
        ->and($emprunt->recu_par)->toBe($librarian->id);
    expect($book->refresh()->stock_disponible)->toBe(1);
});

test('a transition the current state does not allow is rejected', function () {
    $emprunt = Emprunt::factory()->enAttente()->create();
    $this->actingAs(User::factory()->bibliothecaire()->create());

    expect(fn () => app(EmpruntWorkflowService::class)->apply($emprunt, 'rendre'))
        ->toThrow(LogicException::class);
    expect($emprunt->refresh()->statut)->toBe(StatutEmprunt::EnAttente);
});
