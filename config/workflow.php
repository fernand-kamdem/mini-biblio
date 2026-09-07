<?php

return [
    //workflow.emprunt est le nom du workflow
    'emprunt' => [
        
        //type state_machine : l'objet ne peut etre que dans un seul etat a la fois, le workflow est un automate a etats
        'type' => 'state_machine', 

        //Indique où et comment Symfony Workflow lit/écrit l'état actuel de l'objet 
        'marking_store' => [

            //Symfony appellera des méthodes générées automatiquement (getWorkflowState() / setWorkflowState()) sur le modèle Emprunt, plutôt que d'accéder directement à une propriété publique.
            'type' => 'method',

            //nom de la methode qui sera appellee pour lire/ecrire l'etat actuel de l'objet
            'property' => 'workflowState',
        ],

        //Indique les classes qui peuvent être gérées par ce workflow.Le service Workflow refusera d'agir sur tout objet qui n'est pas une instance de cette classe.
        'supports' => [
            \Modules\Emprunt\Models\Emprunt::class,
        ],

        //Indique les états possibles pour l'objet.
        'places' => [
            'brouillon',
            'en_attente',
            'approuve',
            'refuse',
            'rendu',
        ],

        //Indique l'état initial de l'objet.
        'initial_marking' => 'brouillon',

        //Indique les transitions possibles pour l'objet.
        'transitions' => [
            'demander' => [
                'from' => 'brouillon',
                'to' => 'en_attente',
            ],
            'approuver' => [
                'from' => 'en_attente',
                'to' => 'approuve',
            ],
            'refuser' => [
                'from' => 'en_attente',
                'to' => 'refuse',
            ],
            'rendre' => [
                'from' => 'approuve',
                'to' => 'rendu',
            ],
            'annuler' => [
                'from' => 'en_attente',
                'to' => 'brouillon',
            ],
        ],
    ],
];