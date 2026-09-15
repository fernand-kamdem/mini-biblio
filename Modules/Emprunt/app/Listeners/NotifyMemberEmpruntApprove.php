<?php

namespace Modules\Emprunt\Listeners;

use Modules\Emprunt\Events\EmpruntApprove;
use Modules\Emprunt\Notifications\EmpruntApproveNotification;

/**
 * Listener de EmpruntApprove.
 *
 * Qui est notifié :
 * - le membre propriétaire de l'emprunt uniquement ($emprunt->member->user)
 *   (pas tous les MEMBRE)
 *
 * Notification envoyée : EmpruntApproveNotification (database).
 */
class NotifyMemberEmpruntApprove
{
    public function __construct() {}

    /**
     * Envoie la notif "emprunt approuvé" au membre concerné.
     */
    public function handle(EmpruntApprove $event): void
    {
        $event->emprunt->member?->user?->notify(
            new EmpruntApproveNotification($event->emprunt)
        );
    }
}
