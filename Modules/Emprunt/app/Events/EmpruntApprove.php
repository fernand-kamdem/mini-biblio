<?php

namespace Modules\Emprunt\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Emprunt\Models\Emprunt;

/**
 * Event : un emprunt vient d'être approuvé (transition workflow `approuver`).
 *
 * Déclenché par : EmpruntWorkflowService::apply() après commit DB.
 * État : en_attente → approuve.
 *
 * Listener(s) enregistré(s) :
 * - NotifyMemberEmpruntApprove
 *   → notifie uniquement le membre propriétaire (emprunt.member.user)
 *   → canal : database (EmpruntApproveNotification)
 */
class EmpruntApprove
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Emprunt  $emprunt  Emprunt approuvé
     */
    public function __construct(public Emprunt $emprunt) {}
}
