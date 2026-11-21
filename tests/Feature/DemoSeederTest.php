<?php

use App\Models\User;
use Database\Seeders\DemoSeeder;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Models\Emprunt;

test('the demo data has the catalog, the three demo accounts and late loans', function () {
    $this->travelTo('2026-11-23 03:00:00');
    config(['demo.enabled' => true]);

    $this->seed(DemoSeeder::class);

    expect(Book::count())->toBe(20);
    foreach (config('demo.accounts') as $account) {
        expect(User::where('email', $account['email'])->first()?->hasRole($account['role']))->toBeTrue();
    }
    expect(Emprunt::query()->enRetard()->count())->toBe(3)
        ->and(Emprunt::query()->where('statut', StatutEmprunt::EnAttente)->count())->toBe(3);
});

test('the demo stock matches the loans that are out', function () {
    config(['demo.enabled' => true]);

    $this->seed(DemoSeeder::class);

    Book::withCount(['emprunts as sortis' => fn ($query) => $query->where('statut', StatutEmprunt::Approuve)])
        ->get()
        ->each(fn (Book $book) => expect($book->stock_disponible)->toBe($book->stock_total - $book->sortis));
});
