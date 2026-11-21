<?php

namespace Modules\Emprunt\Services;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Emprunt\Events\EmpruntApprove;
use Modules\Emprunt\Events\EmpruntRefuse;
use Modules\Emprunt\Events\EmpruntSoumis;
use Modules\Emprunt\Models\Emprunt;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Exception\LogicException;

class EmpruntWorkflowService
{
    /**
     * RG-14 : durée d'un emprunt, de l'approbation au retour prévu.
     */
    public const DUREE_EMPRUNT_JOURS = 14;

    /**
     * Date de retour prévue pour un emprunt approuvé aujourd'hui (RG-14).
     */
    public static function dateRetourPrevue(): CarbonImmutable
    {
        return CarbonImmutable::today()->addDays(self::DUREE_EMPRUNT_JOURS);
    }

    /**
     * Applique une transition workflow puis, après commit, dispatch les events de notif.
     *
     * Events / personnes notifiées :
     * - demander  → EmpruntSoumis   → tous ADMIN + BIBLIOTHECAIRE
     * - approuver → EmpruntApprove  → membre propriétaire (member.user)
     * - refuser   → EmpruntRefuse   → membre propriétaire (member.user)
     * - rendre / annuler → aucun event notif pour l’instant
     */
    public function apply(Emprunt $emprunt, string $transition): void
    {
        // Récupère le service singleton défini dans EmpruntServiceProvider
        $workflow = app('workflow.emprunt');

        // Vérifie si la transition est possible pour l'état actuel
        if (! $workflow->can($emprunt, $transition)) {
            throw new LogicException(__('This action is no longer possible for this loan.'));
        }

        DB::transaction(function () use ($emprunt, $transition, $workflow) {
            match ($transition) {
                'demander' => $this->assertCanDemander($emprunt),
                'approuver' => $this->handleApprouver($emprunt),
                'refuser' => $this->assertBibliothecaireOrAdmin(),
                'rendre' => $this->handleRendre($emprunt),
                'annuler' => $this->assertProprietaire($emprunt),
                default => throw new InvalidArgumentException("Transition inconnue : {$transition}"),
            };

            $workflow->apply($emprunt, $transition);
            $emprunt->save();
        });

        // Effets de bord après commit (listeners → notifications database)
        match ($transition) {
            'demander' => event(new EmpruntSoumis($emprunt)),   // → ADMIN + BIBLIOTHECAIRE
            'approuver' => event(new EmpruntApprove($emprunt)),  // → membre propriétaire
            'refuser' => event(new EmpruntRefuse($emprunt)),   // → membre propriétaire
            default => null,
        };
    }

    /**
     * Seul le propriétaire de l'emprunt (celui qui l'a créé) peut le soumettre.
     * RG-09 : stock disponible > 0
     * RG-10 : membre et user actifs
     * RG-11 : livre non soft-deleted
     */
    private function assertCanDemander(Emprunt $emprunt): void
    {
        $this->assertProprietaire($emprunt);

        // La ressource (livre) doit être disponible.
        $book = $emprunt->book;
        if (! $book || $book->trashed()) {
            throw new \DomainException(__('This book is no longer in the catalog.'));
        }

        if ($book->stock_disponible <= 0) {
            throw new \DomainException(__('No copy of this book is available.'));
        }

        // Le membre et son compte utilisateur doivent être actifs.
        $member = $emprunt->member;
        $user = $member?->user;

        if (! $member || $member->trashed() || ! $user || $user->trashed() || ! $user->is_active) {
            throw new \DomainException(__('This member is not allowed to borrow.'));
        }
    }

    /**
     * Seul un bibliothécaire ou un admin peut approuver.
     * Enregistre qui a approuvé et quand.
     * RG-07 : décrémente stock_disponible
     * RG-14 : date_emprunt + date_retour_prevue (+14 jours)
     */
    private function handleApprouver(Emprunt $emprunt): void
    {
        $this->assertBibliothecaireOrAdmin();

        $emprunt->approuve_par = Auth::id();
        $emprunt->date_emprunt = now()->toDateString();
        $emprunt->date_retour_prevue = self::dateRetourPrevue()->toDateString();

        // Marquer la ressource comme moins disponible (stock--).
        $book = $emprunt->book()->lockForUpdate()->first();
        if (! $book || $book->stock_disponible <= 0) {
            throw new \DomainException(__('No copy of this book is available.'));
        }
        $book->decrement('stock_disponible');
    }

    /**
     * Seul un bibliothécaire ou un admin peut refuser / approuver / valider un retour.
     * RG-12
     */
    private function assertBibliothecaireOrAdmin(): void
    {
        $user = Auth::user();

        if (! $user?->hasAnyRole(['BIBLIOTHECAIRE', 'ADMIN'])) {
            throw new AuthorizationException(
                __('Only a librarian or an administrator can perform this action.')
            );
        }
    }

    /**
     * Marquer le retour effectif et rendre la ressource disponible à nouveau.
     * RG-08 : incrémente stock_disponible
     */
    private function handleRendre(Emprunt $emprunt): void
    {
        $this->assertBibliothecaireOrAdmin();

        $emprunt->date_retour_reelle = now()->toDateString();
        $emprunt->recu_par = Auth::id();

        // Remettre la ressource disponible (stock++).
        $emprunt->book()->lockForUpdate()->first()?->increment('stock_disponible');
    }

    /**
     * Seul le propriétaire de l'emprunt peut l'annuler.
     * RG-13 : member.user_id === auth()->id()
     */
    private function assertProprietaire(Emprunt $emprunt): void
    {
        if ($emprunt->member?->user_id !== Auth::id()) {
            throw new AuthorizationException(__('You are not allowed to modify this loan.'));
        }
    }
}
