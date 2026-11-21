<?php

namespace Modules\Emprunt\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Emprunt\Events\EmpruntApprove;
use Modules\Emprunt\Events\EmpruntRefuse;
use Modules\Emprunt\Events\EmpruntSoumis;
use Modules\Emprunt\Listeners\NotifyBibliothecairesOrAdminEmpruntSoumis;
use Modules\Emprunt\Listeners\NotifyMemberEmpruntApprove;
use Modules\Emprunt\Listeners\NotifyMemberEmpruntRefuse;

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
        EmpruntSoumis::class => [
            NotifyBibliothecairesOrAdminEmpruntSoumis::class,
        ],
        // Approbation → notifier le membre
        EmpruntApprove::class => [
            NotifyMemberEmpruntApprove::class,
        ],
        // Refus → notifier le membre
        EmpruntRefuse::class => [
            NotifyMemberEmpruntRefuse::class,
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
