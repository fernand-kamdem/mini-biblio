<?php

namespace Modules\Emprunt\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Emprunt\Models\Member;

/**
 * Profil bibliothèque d'un membre : son compte utilisateur reçoit le rôle MEMBRE.
 *
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'card_number' => fake()->unique()->numerify('MB-'.date('Y').'-#####'),
            'phone' => fake()->unique()->numerify('+237 6## ## ## ##'),
            'address' => fake()->optional()->city(),
            'registration_date' => now()->toDateString(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Member $member): void {
            $member->user->assignRole('MEMBRE');
        });
    }
}
