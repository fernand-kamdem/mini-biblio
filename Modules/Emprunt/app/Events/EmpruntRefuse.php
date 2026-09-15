<?php

namespace Modules\Emprunt\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Emprunt\Models\Emprunt;

/**
 * Event : un emprunt vient d'être refusé (transition workflow `refuser`).
 *
 * Déclenché par : EmpruntWorkflowService::apply() après commit DB.
 * État : en_attente → refuse.
 *
 * Listener(s) enregistré(s) :
 * - NotifyMemberEmpruntRefuse
 *   → notifie uniquement le membre propriétaire (emprunt.member.user)
 *   → canal : database (EmpruntRefuseNotification)
 */
class EmpruntRefuse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  Emprunt  $emprunt  Emprunt refusé
     */
    public function __construct(public Emprunt $emprunt) {}
}
