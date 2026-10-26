<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Biblio\Database\Seeders\BiblioDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            BiblioDatabaseSeeder::class,
        ]);

        // Compte de test (mot de passe « password » de la factory) :
        // uniquement en local, jamais sur un serveur.
        if (app()->isLocal()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
