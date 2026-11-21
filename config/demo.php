<?php

/*
|--------------------------------------------------------------------------
| Mode démonstration
|--------------------------------------------------------------------------
|
| Activé sur le serveur de démonstration (DEMO_MODE=true) :
| - la page de connexion affiche les comptes de démonstration ;
| - ces comptes ne peuvent pas être modifiés ni supprimés ;
| - la commande demo:reset remet chaque nuit des données fictives.
|
*/

return [

    'enabled' => env('DEMO_MODE', false),

    // Mot de passe commun aux comptes de démonstration, affiché publiquement.
    'password' => env('DEMO_PASSWORD', 'demo-biblio'),

    // Heure de la remise à zéro quotidienne (fuseau de l'application).
    'reset_at' => env('DEMO_RESET_AT', '03:00'),

    /*
     * Comptes publics, dans l'ordre d'affichage sur la page de connexion.
     *
     * @var array<string, array{name: string, email: string, role: string}>
     */
    'accounts' => [
        'membre' => [
            'name' => 'Aïcha Ndiaye',
            'email' => 'membre@example.com',
            'role' => 'MEMBRE',
        ],
        'bibliothecaire' => [
            'name' => 'Pauline Essomba',
            'email' => 'bibliothecaire@example.com',
            'role' => 'BIBLIOTHECAIRE',
        ],
        'admin' => [
            'name' => 'Direction',
            'email' => 'admin@example.com',
            'role' => 'ADMIN',
        ],
    ],

];
