<?php

namespace Modules\Biblio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Biblio\Models\Author;
use Modules\Biblio\Models\Book;
use Modules\Biblio\Models\Category;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::factory()->count(5)->create();

        $authors = Author::factory()->count(5)->create();

        foreach ($authors as $index => $author) {
            $count = [5, 3, 1, 2, 5][$index] ?? 2;

            $books = Book::factory()
                ->count($count)
                ->create([
                    'category_id' => $categories->random()->id,
                ]);

            $author->books()->attach($books->pluck('id'));
        }
    }
}
