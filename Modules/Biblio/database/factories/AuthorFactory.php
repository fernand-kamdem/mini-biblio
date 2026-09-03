<?php

namespace Modules\Biblio\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Biblio\Models\Author;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'biographie' => fake()->optional()->paragraph(),
        ];
    }
}
