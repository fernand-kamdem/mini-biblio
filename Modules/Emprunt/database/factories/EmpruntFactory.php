<?php

namespace Modules\Emprunt\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;

/**
 * Emprunt dans un état donné du workflow. Les états n'ajustent pas le stock du livre :
 * le test qui en dépend le règle lui-même.
 *
 * @extends Factory<Emprunt>
 */
class EmpruntFactory extends Factory
{
    protected $model = Emprunt::class;

    public function definition(): array
    {
        return [
            'book_id' => Book::factory(),
            'member_id' => Member::factory(),
            'statut' => StatutEmprunt::Brouillon,
            'workflow_state' => StatutEmprunt::Brouillon->value,
            'date_retour_souhaitee' => now()->addDays(10)->toDateString(),
        ];
    }

    public function enAttente(): static
    {
        return $this->etat(StatutEmprunt::EnAttente);
    }

    public function approuve(): static
    {
        return $this->etat(StatutEmprunt::Approuve)->state(fn (): array => [
            'date_emprunt' => now()->toDateString(),
            'date_retour_prevue' => now()->addDays(14)->toDateString(),
        ]);
    }

    /**
     * Approuvé il y a $joursDeRetard + 14 jours : la date de retour prévue est dépassée.
     */
    public function enRetard(int $joursDeRetard = 3): static
    {
        return $this->etat(StatutEmprunt::Approuve)->state(fn (): array => [
            'date_emprunt' => now()->subDays($joursDeRetard + 14)->toDateString(),
            'date_retour_prevue' => now()->subDays($joursDeRetard)->toDateString(),
        ]);
    }

    public function rendu(): static
    {
        return $this->etat(StatutEmprunt::Rendu)->state(fn (): array => [
            'date_emprunt' => now()->subDays(20)->toDateString(),
            'date_retour_prevue' => now()->subDays(6)->toDateString(),
            'date_retour_reelle' => now()->subDays(7)->toDateString(),
        ]);
    }

    private function etat(StatutEmprunt $statut): static
    {
        return $this->state(fn (): array => [
            'statut' => $statut,
            'workflow_state' => $statut->value,
        ]);
    }
}
