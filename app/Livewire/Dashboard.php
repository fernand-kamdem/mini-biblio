<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;

/**
 * Tableau de bord : une vue d'ensemble pour l'équipe (administrateur, bibliothécaire)
 * et un espace de lecture pour le membre, tous deux calculés sur les données réelles.
 */
class Dashboard extends Component
{
    /**
     * Seuil du stock critique : livres dont il reste au plus cet exemplaire.
     */
    private const STOCK_CRITIQUE = 1;

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $role = match (true) {
            $user->hasRole('ADMIN') => 'admin',
            $user->hasRole('BIBLIOTHECAIRE') => 'bibliothecaire',
            default => 'membre',
        };

        $donnees = $role === 'membre'
            ? $this->donneesMembre($user)
            : $this->donneesEquipe();

        return view('livewire.dashboard', ['user' => $user, 'role' => $role, ...$donnees]);
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesEquipe(): array
    {
        return [
            'totalPendingLoans' => Emprunt::query()->where('statut', StatutEmprunt::EnAttente)->count(),
            'totalActiveLoans' => Emprunt::query()->where('statut', StatutEmprunt::Approuve)->count(),
            'totalLateLoans' => Emprunt::query()->enRetard()->count(),
            'totalAvailableCopies' => (int) Book::query()->sum('stock_disponible'),
            'totalOutOfStock' => Book::query()->where('stock_disponible', 0)->count(),
            'totalMembers' => Member::query()->whereHas('user', fn ($query) => $query->where('is_active', true))->count(),
            'totalLibrarians' => User::role('BIBLIOTHECAIRE')->count(),
            'pendingLoans' => Emprunt::with(['member.user', 'book'])
                ->where('statut', StatutEmprunt::EnAttente)
                ->latest()
                ->limit(5)
                ->get(),
            'lateLoans' => Emprunt::with(['member.user', 'book'])
                ->enRetard()
                ->orderBy('date_retour_prevue')
                ->limit(5)
                ->get(),
            'lowStock' => Book::query()
                ->where('stock_disponible', '<=', self::STOCK_CRITIQUE)
                ->orderBy('stock_disponible')
                ->orderBy('titre')
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesMembre(User $user): array
    {
        $memberId = $user->member?->id;

        $emprunts = Emprunt::with('book')
            ->where('member_id', $memberId)
            ->get();

        $activeLoans = $emprunts
            ->where('statut', StatutEmprunt::Approuve)
            ->sortBy('date_retour_prevue')
            ->values();

        return [
            'activeLoans' => $activeLoans,
            'myLateCount' => $activeLoans->filter->estEnRetard()->count(),
            'myPendingCount' => $emprunts->where('statut', StatutEmprunt::EnAttente)->count(),
            'myDraftCount' => $emprunts->where('statut', StatutEmprunt::Brouillon)->count(),
            'upcomingReturns' => $activeLoans->reject->estEnRetard()->take(3),
        ];
    }
}
