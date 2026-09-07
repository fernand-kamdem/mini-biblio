<?php

namespace Modules\Biblio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Biblio\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
