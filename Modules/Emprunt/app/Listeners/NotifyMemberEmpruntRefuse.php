<?php

namespace Modules\Emprunt\Listeners;

use Modules\Emprunt\Events\EmpruntRefuse;
use Modules\Emprunt\Notifications\EmpruntRefuseNotification;

/**
 * Listener de EmpruntRefuse.
 *
 * Qui est notifié :
 * - le membre propriétaire de l'emprunt uniquement ($emprunt->member->user)
 *   (pas tous les MEMBRE)
 *
 * Notification envoyée : EmpruntRefuseNotification (database).
 */
class NotifyMemberEmpruntRefuse
{
    public function __construct() {}

    /**
     * Envoie la notif "emprunt refusé" au membre concerné.
     */
    public function handle(EmpruntRefuse $event): void
    {
        $event->emprunt->member?->user?->notify(
            new EmpruntRefuseNotification($event->emprunt)
        );
    }
}
