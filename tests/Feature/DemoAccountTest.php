<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;

test('a demo account cannot change its password', function () {
    config(['demo.enabled' => true]);
    $user = User::factory()->create(['email' => 'membre@example.com']);

    Volt::actingAs($user)->test('profile.update-password-form')
        ->set('current_password', 'password')
        ->set('password', 'nouveau-mot-de-passe')
        ->set('password_confirmation', 'nouveau-mot-de-passe')
        ->call('updatePassword')
        ->assertHasErrors('current_password');

    expect(Hash::check('password', $user->refresh()->password))->toBeTrue();
});

test('a demo account cannot be deleted', function () {
    config(['demo.enabled' => true]);
    $user = User::factory()->create(['email' => 'bibliothecaire@example.com']);

    $user->delete();

    expect($user->refresh()->trashed())->toBeFalse();
});

test('the same address is an ordinary account outside demo mode', function () {
    config(['demo.enabled' => false]);
    $user = User::factory()->create(['email' => 'membre@example.com']);

    $user->update(['password' => 'nouveau-mot-de-passe']);

    expect(Hash::check('nouveau-mot-de-passe', $user->refresh()->password))->toBeTrue();
});
