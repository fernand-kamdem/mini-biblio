<?php

use Database\Seeders\DemoSeeder;
use Livewire\Volt\Volt;

test('the login page lists the demo accounts only in demo mode', function (bool $demo) {
    config(['demo.enabled' => $demo]);

    $response = $this->get('/login');

    $response->assertOk();
    $demo
        ? $response->assertSee('membre@example.com')
        : $response->assertDontSee('membre@example.com');
})->with([
    'demo mode on' => [true],
    'demo mode off' => [false],
]);

test('a visitor signs in with one click as the demo member', function () {
    config(['demo.enabled' => true]);
    $this->seed(DemoSeeder::class);

    Volt::test('pages.auth.login')
        ->call('loginAsDemo', 'membre')
        ->assertRedirect(route('dashboard', absolute: false));

    expect(auth()->user()->email)->toBe('membre@example.com');
});

test('the one-click demo sign-in is not available outside demo mode', function () {
    config(['demo.enabled' => false]);

    Volt::test('pages.auth.login')
        ->call('loginAsDemo', 'membre')
        ->assertNotFound();

    $this->assertGuest();
});
