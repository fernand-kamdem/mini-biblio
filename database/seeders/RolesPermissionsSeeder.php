<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder chargé de créer les rôles, les permissions
 * et les utilisateurs de base de l'application.
 *
 * Il utilise le package Spatie Laravel Permission
 * pour gérer les rôles et les permissions.
 */
class RolesPermissionsSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        // Supprime le cache des permissions de Spatie.
        //
        // Spatie met en cache les rôles et permissions pour améliorer
        // les performances. Après une modification ou une création,
        // on vide le cache afin que les nouvelles permissions
        // soient immédiatement prises en compte.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Liste de toutes les permissions disponibles dans l'application.
        //
        // Chaque permission représente une action qu'un utilisateur
        // peut avoir le droit d'effectuer.
        $permissions = [
            'voir-catégories',
            'gérer-catégories',
            'voir-auteurs',
            'gérer-auteurs',
            'voir-livres',
            'gérer-livres',
            'créer-emprunt',
            'modifier-emprunt',
            'voir-mes-emprunts',
            'annuler-emprunt',
            'supprimer-emprunt',
            'gérer-emprunts',
            'approuver-emprunt',
            'refuser-emprunt',
            'valider-retour',
            'voir-membres',
            'gérer-membres',
        ];

        // Parcourt la liste des permissions.
        foreach ($permissions as $name) {

            // Crée la permission si elle n'existe pas déjà.
            //
            // firstOrCreate() permet d'éviter de créer des doublons
            // si le seeder est exécuté plusieurs fois.
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Création des différents rôles de l'application.
        //
        // ADMIN :
        // possède tous les droits.
        //
        // BIBLIOTHECAIRE :
        // gère les livres, les emprunts et les membres.
        //
        // MEMBRE :
        // peut consulter les livres et gérer ses propres emprunts.

        $admin = Role::firstOrCreate([
            'name' => 'ADMIN',
            'guard_name' => 'web',
        ]);

        $biblio = Role::firstOrCreate([
            'name' => 'BIBLIOTHECAIRE',
            'guard_name' => 'web',
        ]);

        $membre = Role::firstOrCreate([
            'name' => 'MEMBRE',
            'guard_name' => 'web',
        ]);

        // Attribue les permissions nécessaires au rôle
        // BIBLIOTHECAIRE.
        //
        // Le bibliothécaire peut :
        // - consulter et gérer les livres ;
        // - gérer les emprunts ;
        // - approuver ou refuser un emprunt ;
        // - valider les retours ;
        // - consulter et gérer les membres.
        $biblio->syncPermissions([
            'voir-catégories',
            'gérer-catégories',
            'voir-auteurs',
            'voir-mes-emprunts',
            'gérer-auteurs',
            'voir-livres',
            'gérer-livres',
            'gérer-emprunts',
            'approuver-emprunt',
            'refuser-emprunt',
            'valider-retour',
            'voir-membres',
            'gérer-membres',
        ]);

        // Attribue les permissions nécessaires au rôle MEMBRE.
        //
        // Un membre peut :
        // - consulter les livres ;
        // - créer une demande d'emprunt ;
        // - consulter ses propres emprunts ;
        // - annuler ses emprunts.
        $membre->syncPermissions([
            'voir-livres',
            'créer-emprunt',
            'voir-mes-emprunts',
            'annuler-emprunt',
            'modifier-emprunt',
            'supprimer-emprunt'
        ]);

        // L'administrateur reçoit toutes les permissions existantes.
        //
        // Permission::all() récupère toutes les permissions
        // présentes dans la base de données.
        $admin->syncPermissions(Permission::all());

        // Création du compte administrateur.
        //
        // firstOrCreate() vérifie d'abord si l'adresse e-mail existe.
        // Si elle existe, l'utilisateur n'est pas recréé.
        // Sinon, un nouveau compte est créé.
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Administrateur',
                'password' => 'password',
                'email_verified_at' => now(),

                // Le compte est actif dès sa création.
                'is_active' => true,
            ]
        );

        // Attribue le rôle ADMIN au compte administrateur.
        $userAdmin->syncRoles(['ADMIN']);

        // Création du compte bibliothécaire.
        $userBiblio = User::firstOrCreate(
            ['email' => 'biblio@mail.com'],
            [
                'name' => 'Bibliothécaire',
                'password' => 'password',
                'email_verified_at' => now(),

                // Le compte est actif dès sa création.
                'is_active' => true,
            ]
        );

        // Attribue le rôle BIBLIOTHECAIRE au compte.
        $userBiblio->syncRoles(['BIBLIOTHECAIRE']);
    }
}