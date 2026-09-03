<?php

namespace Modules\Biblio\Database\Seeders;

use Illuminate\Database\Seeder;

class BiblioDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AuthorSeeder::class,
        ]);
    }
}
