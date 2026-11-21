<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('phone', '0612345678')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    // L'inscription crée aussi la fiche membre et attribue le rôle MEMBRE.
    $user = User::where('email', 'test@example.com')->firstOrFail();
    expect($user->hasRole('MEMBRE'))->toBeTrue()
        ->and($user->member?->phone)->toBe('0612345678');
});
