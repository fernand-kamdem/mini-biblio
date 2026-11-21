<?php

namespace Tests;

use Database\Seeders\RolesPermissionsSeeder;
use Illuminate\Foundation\Testing\Attributes\Seed;
use Illuminate\Foundation\Testing\Attributes\Seeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Chaque base de test démarre avec les rôles et permissions de l'application :
 * l'inscription attribue le rôle MEMBRE et le tableau de bord compte les bibliothécaires.
 */
#[Seed]
#[Seeder(RolesPermissionsSeeder::class)]
abstract class TestCase extends BaseTestCase
{
    //
}
