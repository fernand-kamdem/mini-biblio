<?php

use App\Models\User;

test('the reset refuses to run outside demo mode and keeps the data', function () {
    config(['demo.enabled' => false]);
    $user = User::factory()->create();

    $this->artisan('demo:reset', ['--force' => true])->assertFailed();

    $this->assertModelExists($user);
});
