<?php

namespace Modules\Biblio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Biblio\Models\Book;
use Modules\Biblio\Models\Category;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $stockTotal = fake()->numberBetween(1, 20);

        return [
            'category_id' => Category::factory(),
            'titre' => fake()->sentence(4),
            'isbn' => fake()->unique()->isbn13(),
            'annee_parution' => fake()->optional()->numberBetween(1900, (int) date('Y')),
            'stock_total' => $stockTotal,
            'stock_disponible' => fake()->numberBetween(0, $stockTotal),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
