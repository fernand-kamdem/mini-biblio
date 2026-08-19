# Mini Bibliothèque — Documentation complète

> Application de gestion de bibliothèque : des membres empruntent des livres,
> un bibliothécaire valide les retours, chaque emprunt passe par un workflow
> d'approbation. Projet d'apprentissage reprenant les patterns de Easy Day-O.

---

## Sommaire

- [Stack technique](#stack-technique)
- [Structure des modules](#structure-des-modules)
- [Modèle de données](#modèle-de-données)
- [Règles de gestion](#règles-de-gestion)
- [Diagramme UML](#diagramme-uml)
- [Relations Eloquent](#relations-eloquent)
- [Workflow des emprunts](#workflow-des-emprunts)
- [Rôles et permissions (Spatie)](#rôles-et-permissions-spatie)
- [Authentification (Breeze)](#authentification-breeze)
- [Composants Livewire](#composants-livewire)
- [Emails](#emails)
- [SoftDeletes](#softdeletes)
- [Ordre d'implémentation](#ordre-dimplémentation)

---

## Stack technique

Starter kit officiel **Laravel 13 + Livewire**. Auth : Breeze **Livewire** (pas Blade).

### Installé

| Package | Version | Rôle |
|---------|---------|------|
| PHP | ^8.3 | Runtime |
| `laravel/framework` | ^13.17 | Framework principal |
| `laravel/breeze` | ^2.4 (dev) | Scaffolding auth (à installer avec `breeze:install livewire`) |
| `livewire/livewire` | ^3.6.4 | Composants réactifs (tout le projet) |
| `livewire/volt` | ^1.7 | Pages auth Breeze (composants Volt) |
| `livewire/blaze` | ^1.0 | Compilation / perf Livewire |
| `nwidart/laravel-modules` | ^13.0 | Architecture modulaire (`Modules/`) |
| `spatie/laravel-permission` | ^8.3 | Rôles et permissions |
| `guzzlehttp/guzzle` | ^8.0 (via Laravel) | HTTP client (dépendance du framework) |
| Vite | ^8.0 | Bundler front |
| Tailwind CSS | ^4.x | Styles |

### Prévu plus tard (pas encore dans `composer.json`)

| Package | Version visée | Rôle | Note |
|---------|---------------|------|------|
| `anhskohbo/no-captcha` | ^3.8 | reCAPTCHA sur le login | Conflit avec Guzzle 8 (`no-captcha` exige Guzzle ^6\|^7). À installer plus tard en pinant Guzzle `^7.9`, ou valider reCAPTCHA via `Http`. |
| `symfony/workflow` | ^7.0 | Machine à états des emprunts | Module Emprunt |
| `laravel/reverb` | ^1.0 | WebSockets temps réel | Dashboard / stock live |

---

## Structure des modules

```
app/
  Models/
    User.php                        ← HasOne Membre, HasRoles (Spatie)
  Events/
    EmpruntSoumis.php               ← broadcast → canal 'bibliothecaire'
    EmpruntApprouve.php             ← broadcast → canal 'membre.{id}'
    EmpruntRefuse.php               ← broadcast → canal 'membre.{id}'
    StockMisAJour.php               ← broadcast → canal public 'livres'
  Mail/
    BienvenueMail.php               ← envoyé à l'inscription
    EmpruntApprouveMail.php         ← envoyé au membre à l'approbation
    EmpruntRefuseMail.php           ← envoyé au membre au refus
    RappelRetardMail.php            ← envoyé par le scheduler
  Observers/
    EmpruntObserver.php             ← déclenche Events + Mails selon statut
    MembreObserver.php              ← crée Membre + assigne rôle + envoie Bienvenue
  Console/
    Commands/
      CheckRetardsCommand.php       ← commande schedulée quotidienne

Modules/
  Biblio/                           ← Gestion du catalogue
    app/
      Models/
        Auteur.php
        Categorie.php
        Livre.php
      Livewire/
        LivreList.php               ← liste paginée + recherche wire:model.live
        LivreForm.php               ← ajout / édition (modal)
        AuteurForm.php
        CategorieForm.php
      Http/Controllers/
        BiblioController.php        ← mince, return view uniquement
      Providers/
        BiblioServiceProvider.php   ← enregistrement Livewire::component(...)
      routes/
        web.php
    database/
      migrations/
        create_categories_table.php
        create_auteurs_table.php
        create_livres_table.php
        create_auteur_livre_table.php
      seeders/
        BiblioSeeder.php

  Emprunt/                          ← Gestion des emprunts + workflow
    app/
      Models/
        Membre.php
        Emprunt.php
      Enums/
        StatutEmprunt.php           ← brouillon, en_attente, approuve, refuse, rendu
      Livewire/
        EmpruntList.php             ← mes emprunts (membre) ou tous (admin/biblio)
        EmpruntForm.php             ← choisir livre + soumettre demande
        TableauDeBord.php           ← stats + demandes en attente (temps réel Reverb)
      Http/Controllers/
        EmpruntController.php
      Providers/
        EmpruntServiceProvider.php
      routes/
        web.php
    database/
      migrations/
        create_membres_table.php
        create_emprunts_table.php
      seeders/
        EmpruntSeeder.php
        RolesPermissionsSeeder.php
```

---

## Modèle de données

### `users`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `name` | string | NOT NULL |
| `email` | string | UNIQUE NOT NULL |
| `email_verified_at` | timestamp | nullable |
| `password` | string | NOT NULL |
| `remember_token` | string | nullable |
| `is_active` | boolean | default `true` |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `membres`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `user_id` | bigint | FK → `users.id` NOT NULL |
| `numero_carte` | string | UNIQUE (MB-XXXXX) |
| `nom` | string | NOT NULL |
| `prenom` | string | NOT NULL |
| `telephone` | string | nullable |
| `adresse` | text | nullable |
| `date_inscription` | date | NOT NULL |
| `est_actif` | boolean | default `true` |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `categories`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `nom` | string | UNIQUE NOT NULL |
| `description` | text | nullable |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `auteurs`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `nom` | string | NOT NULL |
| `prenom` | string | NOT NULL |
| `biographie` | text | nullable |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `livres`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `categorie_id` | bigint | FK → `categories.id` NOT NULL |
| `titre` | string | NOT NULL |
| `isbn` | string | UNIQUE nullable |
| `annee_parution` | integer | nullable |
| `stock_total` | integer | default `1` |
| `stock_disponible` | integer | default `1` |
| `description` | text | nullable |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `auteur_livre` *(pivot N-N)*
| Colonne | Type | Contrainte |
|---------|------|------------|
| `auteur_id` | bigint | FK → `auteurs.id` |
| `livre_id` | bigint | FK → `livres.id` |

### `emprunts`
| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `membre_id` | bigint | FK → `membres.id` NOT NULL |
| `livre_id` | bigint | FK → `livres.id` NOT NULL |
| `statut` | enum | `brouillon\|en_attente\|approuve\|refuse\|rendu` |
| `workflow_state` | string | default `'brouillon'` |
| `date_emprunt` | date | nullable |
| `date_retour_prevue` | date | nullable |
| `date_retour_reelle` | date | nullable |
| `approuve_par` | bigint | FK → `users.id` nullable |
| `note_membre` | text | nullable |
| `note_bibliothecaire` | text | nullable |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

---

## Règles de gestion

| Code | Règle |
|------|-------|
| **RG-01** | Un `User` possède **au plus un** `Membre` (créé automatiquement à l'inscription via Observer) |
| **RG-02** | Un `Membre` peut avoir **zéro ou plusieurs** `Emprunts` |
| **RG-03** | Un `Emprunt` concerne **exactement un** `Livre` et **exactement un** `Membre` |
| **RG-04** | Un `Livre` appartient **obligatoirement** à **une seule** `Categorie` |
| **RG-05** | Une `Categorie` peut contenir **zéro ou plusieurs** `Livres` |
| **RG-06** | Un `Livre` peut avoir **un ou plusieurs** `Auteurs` ; un `Auteur` peut avoir **un ou plusieurs** `Livres` (N-N via pivot) |
| **RG-07** | Le `stock_disponible` d'un `Livre` **diminue de 1** à chaque emprunt approuvé |
| **RG-08** | Le `stock_disponible` **augmente de 1** quand l'emprunt passe à l'état `rendu` |
| **RG-09** | Un emprunt ne peut être créé que si `stock_disponible > 0` |
| **RG-10** | Un `Membre` soft-deleté ne peut plus emprunter |
| **RG-11** | Un `Livre` soft-deleté ne peut plus faire l'objet d'un nouvel emprunt |
| **RG-12** | Seul un `BIBLIOTHECAIRE` ou `ADMIN` peut approuver / refuser / valider le retour |
| **RG-13** | Seul le `Membre` propriétaire peut annuler un emprunt en état `en_attente` |
| **RG-14** | La `date_retour_prevue` est fixée à **+14 jours** depuis `date_emprunt` |
| **RG-15** | Un emprunt est **en retard** si `date_retour_prevue < aujourd'hui` et `statut = approuve` |

---

## Diagramme UML

```mermaid
erDiagram

    users ||--o| membres : "1 => 0..1 (RG-01)"
    membres ||--o{ emprunts : "1 => 0..N (RG-02)"
    livres ||--o{ emprunts : "1 => 0..N (RG-03)"
    categories ||--o{ livres : "1 => 0..N (RG-05)"
    livres }o--o{ auteurs : "N <=> N (RG-06)"
    users ||--o{ emprunts : "1 approbateur => 0..N (RG-12)"

    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at "nullable"
        string password
        boolean is_active "default true"
        timestamp deleted_at "SoftDelete"
    }

    membres {
        bigint id PK
        bigint user_id FK "NOT NULL - RG-01"
        string numero_carte UK "MB-XXXXX"
        string nom "NOT NULL"
        string prenom "NOT NULL"
        string telephone "nullable"
        date date_inscription "NOT NULL"
        boolean est_actif "default true - RG-10"
        timestamp deleted_at "SoftDelete"
    }

    categories {
        bigint id PK
        string nom UK "NOT NULL"
        text description "nullable"
        timestamp deleted_at "SoftDelete"
    }

    auteurs {
        bigint id PK
        string nom "NOT NULL"
        string prenom "NOT NULL"
        text biographie "nullable"
        timestamp deleted_at "SoftDelete"
    }

    livres {
        bigint id PK
        bigint categorie_id FK "NOT NULL - RG-04"
        string titre "NOT NULL"
        string isbn UK "nullable"
        int annee_parution "nullable"
        int stock_total "default 1"
        int stock_disponible "default 1 - RG-07 RG-08 RG-09"
        text description "nullable"
        timestamp deleted_at "SoftDelete - RG-11"
    }

    auteur_livre {
        bigint auteur_id FK
        bigint livre_id FK
    }

    emprunts {
        bigint id PK
        bigint membre_id FK "NOT NULL - RG-02 RG-03"
        bigint livre_id FK "NOT NULL - RG-03"
        enum statut "brouillon|en_attente|approuve|refuse|rendu"
        string workflow_state "default brouillon"
        date date_emprunt "nullable"
        date date_retour_prevue "nullable - RG-14"
        date date_retour_reelle "nullable"
        bigint approuve_par FK "nullable => users.id - RG-12"
        text note_membre "nullable"
        text note_bibliothecaire "nullable"
        timestamp deleted_at "SoftDelete"
    }
```

### Cardinalités récapitulées

```
users          1 ──────────── 0..1   membres
membres        1 ──────────── 0..*   emprunts
livres         1 ──────────── 0..*   emprunts
categories     1 ──────────── 0..*   livres
livres       0..* ──────────── 0..*  auteurs   (via auteur_livre)
users          1 ──────────── 0..*   emprunts  (approbateur)
```

---

## Relations Eloquent

| Modèle | Méthode | Type | Vers |
|--------|---------|------|------|
| `User` | `membre()` | `hasOne` | `Membre` |
| `Membre` | `user()` | `belongsTo` | `User` |
| `Membre` | `emprunts()` | `hasMany` | `Emprunt` |
| `Emprunt` | `membre()` | `belongsTo` | `Membre` |
| `Emprunt` | `livre()` | `belongsTo` | `Livre` |
| `Emprunt` | `approbateur()` | `belongsTo` | `User` (clé `approuve_par`) |
| `Livre` | `emprunts()` | `hasMany` | `Emprunt` |
| `Livre` | `categorie()` | `belongsTo` | `Categorie` |
| `Livre` | `auteurs()` | `belongsToMany` | `Auteur` (pivot `auteur_livre`) |
| `Categorie` | `livres()` | `hasMany` | `Livre` |
| `Categorie` | `emprunts()` | `hasManyThrough` | `Emprunt` via `Livre` |
| `Auteur` | `livres()` | `belongsToMany` | `Livre` (pivot `auteur_livre`) |

---

## Workflow des emprunts (Symfony Workflow)

```
                  ┌─────────────┐
     [MEMBRE]     │  brouillon  │ ◄─── annuler (RG-13)
                  └──────┬──────┘
                         │ demander
                         ▼
                  ┌─────────────┐
                  │ en_attente  │
                  └──┬──────┬───┘
        approuver    │      │  refuser
       [BIBLIO/ADMIN]│      │  [BIBLIO/ADMIN]
                     ▼      ▼
              ┌─────────┐ ┌────────┐
              │ approuve│ │ refuse │
              └────┬────┘ └────────┘
                   │ rendre
                   │ [BIBLIO/ADMIN]
                   ▼
              ┌─────────┐
              │  rendu  │
              └─────────┘
```

| Transition | De | Vers | Qui déclenche |
|------------|----|------|---------------|
| `demander` | brouillon | en_attente | MEMBRE |
| `approuver` | en_attente | approuve | BIBLIOTHECAIRE, ADMIN |
| `refuser` | en_attente | refuse | BIBLIOTHECAIRE, ADMIN |
| `rendre` | approuve | rendu | BIBLIOTHECAIRE, ADMIN |
| `annuler` | en_attente | brouillon | MEMBRE (propriétaire uniquement) |

---

## Rôles et permissions (Spatie)

| Rôle | Permissions |
|------|-------------|
| `ADMIN` | Toutes les permissions |
| `BIBLIOTHECAIRE` | `voir-livres`, `gérer-livres`, `gérer-emprunts`, `approuver-emprunt`, `refuser-emprunt`, `valider-retour`, `voir-membres`, `gérer-membres` |
| `MEMBRE` | `voir-livres`, `créer-emprunt`, `voir-mes-emprunts`, `annuler-emprunt` |

---

## Authentification (Breeze)

Toutes les fonctionnalités Breeze sont incluses :

| Fonctionnalité | Route |
|----------------|-------|
| Connexion + reCAPTCHA | `GET/POST /login` |
| Inscription | `GET/POST /register` |
| Vérification email | `GET /verify-email` |
| Mot de passe oublié | `GET/POST /forgot-password` |
| Réinitialisation MDP | `GET/POST /reset-password/{token}` |
| Confirmation MDP | `GET/POST /confirm-password` |
| Remember me | checkbox dans le formulaire login |
| Déconnexion | `POST /logout` |

### Flow inscription → profil membre

```
1. /register → saisit nom, email, password
2. Email de vérification envoyé (Breeze natif)
3. MembreObserver::created() déclenché automatiquement
   → Membre créé (numero_carte: MB-00001)
   → Rôle MEMBRE assigné (Spatie)
   → Email BienvenueMail envoyé
4. Clic sur lien → email vérifié
5. Redirigé vers /dashboard
```

---

## Composants Livewire

### Module Biblio

| Composant | Fichier | Fonctionnalités |
|-----------|---------|-----------------|
| `LivreList` | `Biblio/app/Livewire/LivreList.php` | Liste paginée, recherche `wire:model.live`, filtre par catégorie, stock en temps réel (Reverb) |
| `LivreForm` | `Biblio/app/Livewire/LivreForm.php` | Modal création/édition, `wire:submit.prevent="save"`, sélection auteurs multiple |
| `AuteurForm` | `Biblio/app/Livewire/AuteurForm.php` | Ajout/édition auteur |
| `CategorieForm` | `Biblio/app/Livewire/CategorieForm.php` | Ajout/édition catégorie |

### Module Emprunt

| Composant | Fichier | Fonctionnalités |
|-----------|---------|-----------------|
| `EmpruntList` | `Emprunt/app/Livewire/EmpruntList.php` | Mes emprunts (membre) ou tous (admin/biblio), filtre par statut, badge retard |
| `EmpruntForm` | `Emprunt/app/Livewire/EmpruntForm.php` | Choisir un livre disponible + soumettre demande, validation RG-09 |
| `TableauDeBord` | `Emprunt/app/Livewire/TableauDeBord.php` | Stats (livres dispo, emprunts en attente, retards), mise à jour temps réel via Reverb |

---

## Emails

| Classe Mail | Déclencheur | Destinataire |
|-------------|-------------|--------------|
| `BienvenueMail` | `MembreObserver::created()` | Nouveau membre |
| `EmpruntSoumisNotif` | `EmpruntObserver::created()` | Bibliothécaire |
| `EmpruntApprouveMail` | `EmpruntObserver::updated()` statut → approuve | Membre |
| `EmpruntRefuseMail` | `EmpruntObserver::updated()` statut → refuse | Membre |
| `RappelRetardMail` | `CheckRetardsCommand` (scheduler quotidien) | Membres en retard |

### Configuration Mailtrap (développement)

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxxx
MAIL_PASSWORD=xxxxxx
MAIL_FROM_ADDRESS="noreply@mini-biblio.local"
MAIL_FROM_NAME="Mini Bibliothèque"
```

---

## Reverb (WebSockets temps réel)

| Événement | Canal | Type | Qui écoute |
|-----------|-------|------|------------|
| `EmpruntSoumis` | `bibliothecaire` | Privé | Dashboard bibliothécaire |
| `EmpruntApprouve` | `membre.{id}` | Privé | Page "mes emprunts" du membre |
| `EmpruntRefuse` | `membre.{id}` | Privé | Page "mes emprunts" du membre |
| `StockMisAJour` | `livres` | Public | Liste des livres |

### Écoute dans Livewire

```php
// TableauDeBord.php — côté bibliothécaire
#[On('echo:bibliothecaire,EmpruntSoumis')]
public function rafraichirDemandes(): void
{
    $this->chargerDemandes();
}

// EmpruntList.php — côté membre
#[On('echo-private:membre.{membreId},EmpruntApprouve')]
public function notifierApprobation(): void
{
    $this->dispatch('notify', message: 'Votre emprunt a été approuvé !');
}
```

### Configuration `.env`

```env
REVERB_APP_ID=mini-biblio
REVERB_APP_KEY=xxxxxx
REVERB_APP_SECRET=xxxxxx
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

BROADCAST_CONNECTION=reverb
```

---

## SoftDeletes

`SoftDeletes` est activé sur : `users`, `membres`, `livres`, `categories`, `auteurs`, `emprunts`.

**Non utilisé sur** : `auteur_livre` (pivot), tables Spatie.

| Opération | Méthode |
|-----------|---------|
| Suppression douce | `$model->delete()` |
| Inclure supprimés | `Model::withTrashed()->get()` |
| Uniquement supprimés | `Model::onlyTrashed()->get()` |
| Restaurer | `$model->restore()` |
| Suppression définitive | `$model->forceDelete()` |

---

## Ordre d'implémentation

### Étape 1 — Socle
1. `laravel new mini-biblio`
2. Installer les packages (`composer require`, `npm install`)
3. Configurer `.env` (DB, Mail, reCAPTCHA, Reverb)
4. `php artisan breeze:install` (stack Blade)
5. `php artisan migrate`

### Étape 2 — Spatie + rôles
6. `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"`
7. Créer `RolesPermissionsSeeder` (ADMIN, BIBLIOTHECAIRE, MEMBRE + permissions)
8. Ajouter `HasRoles` dans `User.php`

### Étape 3 — Module Biblio
9. `php artisan module:make Biblio`
10. Migrations : `categories`, `auteurs`, `livres`, pivot `auteur_livre`
11. Modèles + relations (`belongsTo`, `hasMany`, `belongsToMany`)
12. `BiblioController` (mince)
13. Livewire `LivreList` + `LivreForm`
14. Enregistrement dans `BiblioServiceProvider`

### Étape 4 — Module Emprunt
15. `php artisan module:make Emprunt`
16. Migrations : `membres`, `emprunts`
17. Modèles + relations + `hasManyThrough`
18. `Enum StatutEmprunt`
19. `config/workflow.php` — définir les transitions
20. Livewire `EmpruntForm` + `EmpruntList` + `TableauDeBord`
21. Enregistrement dans `EmpruntServiceProvider`

### Étape 5 — Observers + Mail
22. `MembreObserver` (création profil + rôle + email Bienvenue)
23. `EmpruntObserver` (events Reverb + emails selon transition)
24. Classes `Mailable` + templates Blade email
25. `CheckRetardsCommand` + `scheduler`

### Étape 6 — Reverb
26. `php artisan reverb:install`
27. Créer les `Event` broadcastables
28. Brancher `#[On('echo:...')]` dans les composants Livewire

### Étape 7 — Auth + reCAPTCHA
29. Modifier `login.blade.php` : ajouter `{!! NoCaptcha::display() !!}`
30. Modifier `AuthenticatedSessionController` : valider `g-recaptcha-response`
31. Ajouter `MustVerifyEmail` dans `User.php`

### Étape 8 — Finitions
32. Layout principal avec sidebar + `@can(...)`
33. `@livewireStyles` / `@livewireScripts` dans le layout
34. Seeders de données de test
35. Vérification des RG dans les composants Livewire

---

## Jour 1 — Module Auth complet

> Objectif : avoir une authentification 100% fonctionnelle avant de toucher aux modules métier.

### Prérequis

| Outil | Vérification |
|-------|-------------|
| PHP 8.2+ | `php -v` |
| Composer | `composer -V` |
| Node.js / npm | `node -v && npm -v` |
| MySQL ou SQLite | `mysql --version` |
| Compte [Mailtrap](https://mailtrap.io) | Gratuit — pour tester les emails |
| Compte [Google reCAPTCHA v2](https://www.google.com/recaptcha) | Gratuit — clé site + clé secrète |

---

### Plan d'exécution Jour 1

#### 1. Créer le projet Laravel

```bash
laravel new mini-biblio --no-interaction
cd mini-biblio
```

#### 2. Installer les packages

```bash
composer require \
  laravel/breeze \
  nwidart/laravel-modules \
  spatie/laravel-permission \
  livewire/livewire \
  anhskohbo/no-captcha \
  symfony/workflow \
  laravel/reverb

php artisan breeze:install blade
npm install && npm run build
```

#### 3. Configurer `.env`

```env
APP_NAME="Mini Bibliothèque"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_biblio
DB_USERNAME=root
DB_PASSWORD=secret

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxxx
MAIL_PASSWORD=xxxxxx
MAIL_FROM_ADDRESS="noreply@mini-biblio.local"
MAIL_FROM_NAME="Mini Bibliothèque"

NOCAPTCHA_SECRET=xxxxxx
NOCAPTCHA_SITEKEY=xxxxxx

REVERB_APP_ID=mini-biblio
REVERB_APP_KEY=xxxxxx
REVERB_APP_SECRET=xxxxxx
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

BROADCAST_CONNECTION=reverb
```

#### 4. Publier Spatie + migrer

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

#### 5. Modifier `app/Models/User.php`

Ajouter les trois traits :

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles, SoftDeletes;

    protected $fillable = ['name', 'email', 'password', 'is_active'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
        'deleted_at'        => 'datetime',
    ];

    public function membre(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\Modules\Emprunt\app\Models\Membre::class);
    }
}
```

#### 6. Créer la migration `membres`

```bash
php artisan module:make Emprunt
php artisan make:migration create_membres_table --path=Modules/Emprunt/database/migrations
```

```php
Schema::create('membres', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('numero_carte')->unique();
    $table->string('nom');
    $table->string('prenom');
    $table->string('telephone')->nullable();
    $table->text('adresse')->nullable();
    $table->date('date_inscription');
    $table->boolean('est_actif')->default(true);
    $table->softDeletes();
    $table->timestamps();
});
```

#### 7. Créer le modèle `Membre`

```php
class Membre extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'numero_carte', 'nom', 'prenom',
        'telephone', 'adresse', 'date_inscription', 'est_actif',
    ];

    protected $casts = [
        'date_inscription' => 'date',
        'est_actif'        => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emprunts(): HasMany
    {
        return $this->hasMany(Emprunt::class);
    }
}
```

#### 8. Créer `MembreObserver`

Déclenché automatiquement à chaque création de `User` :

```php
class MembreObserver
{
    public function created(User $user): void
    {
        $membre = Membre::create([
            'user_id'          => $user->id,
            'numero_carte'     => 'MB-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
            'nom'              => $user->name,
            'prenom'           => '',
            'date_inscription' => now(),
        ]);

        $user->assignRole('MEMBRE');

        Mail::to($user->email)->send(new BienvenueMail($user));
    }
}
```

Enregistrer dans `AppServiceProvider::boot()` :
```php
User::observe(MembreObserver::class);
```

#### 9. Créer `BienvenueMail`

```bash
php artisan make:mail BienvenueMail
```

Template Blade `resources/views/emails/bienvenue.blade.php` :
- Nom du membre
- Lien de connexion
- Rappel : vérifier l'email

#### 10. Créer `RolesPermissionsSeeder`

```php
$permissions = [
    'voir-livres', 'gérer-livres',
    'créer-emprunt', 'voir-mes-emprunts', 'annuler-emprunt',
    'gérer-emprunts', 'approuver-emprunt', 'refuser-emprunt', 'valider-retour',
    'voir-membres', 'gérer-membres',
];

foreach ($permissions as $perm) {
    Permission::firstOrCreate(['name' => $perm]);
}

$admin        = Role::firstOrCreate(['name' => 'ADMIN']);
$biblio       = Role::firstOrCreate(['name' => 'BIBLIOTHECAIRE']);
$membre       = Role::firstOrCreate(['name' => 'MEMBRE']);

$biblio->syncPermissions([
    'voir-livres', 'gérer-livres', 'gérer-emprunts',
    'approuver-emprunt', 'refuser-emprunt', 'valider-retour',
    'voir-membres', 'gérer-membres',
]);

$membre->syncPermissions([
    'voir-livres', 'créer-emprunt', 'voir-mes-emprunts', 'annuler-emprunt',
]);

$admin->syncPermissions(Permission::all());

// User admin de test
$userAdmin = User::firstOrCreate(
    ['email' => 'admin@mini-biblio.local'],
    ['name' => 'Administrateur', 'password' => bcrypt('password'), 'email_verified_at' => now()]
);
$userAdmin->assignRole('ADMIN');
```

#### 11. Ajouter reCAPTCHA sur le login

Dans `resources/views/auth/login.blade.php`, avant le bouton submit :

```html
<div class="mt-4">
    {!! NoCaptcha::display() !!}
    @error('g-recaptcha-response')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
```

Dans `AuthenticatedSessionController@store`, ajouter à la validation :

```php
'g-recaptcha-response' => ['required', 'captcha'],
```

Dans le layout, avant `</body>` :

```html
{!! NoCaptcha::renderJs() !!}
```

#### 12. Lancer et tester

```bash
php artisan db:seed --class=RolesPermissionsSeeder
php artisan serve
```

**Tests à effectuer :**
- [ ] Inscription → email de vérification reçu sur Mailtrap
- [ ] Clic sur lien → email vérifié → redirigé vers dashboard
- [ ] Profil `Membre` créé automatiquement (vérifier en DB)
- [ ] Rôle `MEMBRE` assigné (vérifier en DB)
- [ ] Email `BienvenueMail` reçu sur Mailtrap
- [ ] Login avec reCAPTCHA
- [ ] "Remember me" → cookie persistant
- [ ] Mot de passe oublié → email de reset reçu
- [ ] Réinitialisation du mot de passe fonctionnelle
- [ ] Login avec `admin@mini-biblio.local` / `password`
