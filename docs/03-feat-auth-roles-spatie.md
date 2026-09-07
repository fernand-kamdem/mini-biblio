# 3 — Branche `feat/auth-roles-spatie`

**But :** rôles **ADMIN**, **BIBLIOTHECAIRE**, **MEMBRE** + comptes de test. Alias Spatie déjà posés en [02](./02-feat-auth-login-easyday.md).

```bash
git checkout master   # après merge feat/auth-login-easyday
git checkout -b feat/auth-roles-spatie
```

Ne pas republier Spatie si `config/permission.php` existe déjà.

## Fichier créé : `database/seeders/RolesPermissionsSeeder.php`

Permissions (noms du README racine, pas Easy Day-O) :

- `voir-livres`, `gérer-livres`
- `créer-emprunt`, `voir-mes-emprunts`, `annuler-emprunt`
- `gérer-emprunts`, `approuver-emprunt`, `refuser-emprunt`, `valider-retour`
- `voir-membres`, `gérer-membres`

| Rôle | Permissions |
|------|-------------|
| ADMIN | toutes |
| BIBLIOTHECAIRE | livres, emprunts (gérer / approuver / refuser / retour), membres |
| MEMBRE | voir livres + ses emprunts (créer / voir / annuler) |

Comptes : `admin@mail.com` / `biblio@mail.com`, mot de passe `password`, `is_active => true`, `email_verified_at` rempli.

`Permission::firstOrCreate` + `Role::firstOrCreate` + `User::firstOrCreate` pour relancer le seeder sans doublons. Vider le cache Spatie en tête de `run()` : `PermissionRegistrar::forgetCachedPermissions()`.

Le mot de passe en clair `'password'` est hashé par le **cast** `User` (`hashed`), pas par `Hash::make` dans le seeder.

## Encore à faire (pas sur cette branche)

| Tâche | Pourquoi |
|-------|----------|
| `$this->call(RolesPermissionsSeeder::class)` dans `DatabaseSeeder` | Aujourd’hui seul `test@example.com` est créé au `db:seed` |
| `User::create` à l’inscription ? `assignRole('MEMBRE')` | Sans ça, un inscrit n’a aucune permission Spatie |
| `'is_active' => true` dans `UserFactory` | Toujours le piège tests 302 (doc 02) |

Les `->middleware('permission:voir-livres')` iront sur les **modules**, pas ici.

## Commandes

```bash
php artisan db:seed --class=RolesPermissionsSeeder
# ou, une fois branché dans DatabaseSeeder :
php artisan migrate:fresh --seed
```

## Commit

```bash
git add database/seeders/RolesPermissionsSeeder.php
git commit -m "Seeder Spatie : rôles ADMIN, BIBLIOTHECAIRE, MEMBRE et comptes de test."
```
