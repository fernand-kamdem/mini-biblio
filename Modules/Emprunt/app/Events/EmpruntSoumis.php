<?php

namespace Modules\Emprunt\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Emprunt\Models\Emprunt;

/**
 * Event : un emprunt vient d'être soumis (transition workflow `demander`).
 *
 * Déclenché par : EmpruntWorkflowService::apply() après commit DB.
 * État : brouillon → en_attente.
 *
 * Listener(s) enregistré(s) :
 * - NotifyBibliothecairesOrAdminEmpruntSoumis
 *   → notifie tous les users avec rôle ADMIN ou BIBLIOTHECAIRE
 *   → canal : database (EmpruntSoumisNotification)
 */
class EmpruntSoumis
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Emprunt  $emprunt  Emprunt passé en en_attente
     */
    public function __construct(public Emprunt $emprunt) {}
}
