<?php

namespace Modules\Emprunt\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Mapping Event → Listener(s) du module Emprunt.
 *
 * Récap notifications :
 * | Event           | Transition | Qui reçoit la notif              |
 * |-----------------|------------|----------------------------------|
 * | EmpruntSoumis   | demander   | tous ADMIN + BIBLIOTHECAIRE      |
 * | EmpruntApprove  | approuver  | membre propriétaire de l'emprunt |
 * | EmpruntRefuse   | refuser    | membre propriétaire de l'emprunt |
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        // Soumission → notifier le staff
        \Modules\Emprunt\Events\EmpruntSoumis::class => [
            \Modules\Emprunt\Listeners\NotifyBibliothecairesOrAdminEmpruntSoumis::class,
        ],
        // Approbation → notifier le membre
        \Modules\Emprunt\Events\EmpruntApprove::class => [
            \Modules\Emprunt\Listeners\NotifyMemberEmpruntApprove::class,
        ],
        // Refus → notifier le membre
        \Modules\Emprunt\Events\EmpruntRefuse::class => [
            \Modules\Emprunt\Listeners\NotifyMemberEmpruntRefuse::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
