<?php

namespace Modules\Emprunt\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\Emprunt\Events\EmpruntSoumis;
use Modules\Emprunt\Notifications\EmpruntSoumisNotification;

/**
 * Listener de EmpruntSoumis.
 *
 * Qui est notifié :
 * - tous les utilisateurs Spatie rôle ADMIN
 * - tous les utilisateurs Spatie rôle BIBLIOTHECAIRE
 *
 * Notification envoyée : EmpruntSoumisNotification (database).
 */
class NotifyBibliothecairesOrAdminEmpruntSoumis
{
    public function __construct() {}

    /**
     * Envoie la notif "nouvelle demande en attente" aux admin / bibliothécaires.
     */
    public function handle(EmpruntSoumis $event): void
    {
        $emprunt = $event->emprunt->loadMissing(['book', 'member.user']);

        Notification::send(
            User::role(['ADMIN', 'BIBLIOTHECAIRE'])->get(),
            new EmpruntSoumisNotification($emprunt)
        );
    }
}
