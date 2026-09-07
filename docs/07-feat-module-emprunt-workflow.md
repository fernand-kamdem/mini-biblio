# 7 — Module Emprunt : workflow Symfony

Playbook pour la **machine à états des emprunts**. Complète le README racine (section [Workflow des emprunts](../README.md#workflow-des-emprunts-symfony-workflow)).

> Pattern générique (tous projets) : `~/Desktop/laravel-symfony-workflow-core.md`  
> Mails / notifications (tous projets) : `~/Desktop/laravel-notifications-core.md`

**Branche suggérée :** `feat/module-emprunt-workflow` (plusieurs commits possibles : enum → config → service → Livewire → observer).

---

## Objectif

Chaque emprunt suit un cycle d'approbation avant qu'un livre sorte du stock :

```
brouillon → en_attente → approuve → rendu
                ↓
              refuse
         ↑ annuler (depuis en_attente)
```

Un **membre** soumet une demande ; un **bibliothécaire** ou **admin** approuve, refuse ou valide le retour.

---

## Déjà en place (2026-09-01)

| Élément | Fichier | Statut |
|---------|---------|--------|
| Migration `members` | `Modules/Emprunt/database/migrations/2026_09_01_110118_create_members_table.php` | ✅ |
| Migration `emprunts` | `Modules/Emprunt/database/migrations/2026_09_01_110132_create_emprunts_table.php` | ✅ |
| Modèle `Member` | `Modules/Emprunt/app/Models/Member.php` | ✅ |
| Modèle `Emprunt` | `Modules/Emprunt/app/Models/Emprunt.php` | ✅ |
| Enum `StatutEmprunt` | `Modules/Emprunt/app/Enums/StatutEmprunt.php` | ✅ |
| Getters/setters workflow | `Emprunt::getWorkflowState()` / `setWorkflowState()` | ✅ |
| Relations croisées | `User`, `Book`, `Member`, `Emprunt` | ✅ |
| Rôle MEMBRE à l'inscription | `register.blade.php` Volt | ✅ |

## À faire (prochaine session)

| Élément | Fichier cible | Priorité |
|---------|---------------|----------|
| Package Symfony Workflow | `composer require symfony/workflow` | 1 |
| Config workflow | `config/workflow.php` | 1 |
| Enregistrement DI | `EmpruntServiceProvider` | 1 |
| Service métier | `Modules/Emprunt/app/Services/EmpruntWorkflowService.php` | 2 |
| Observer membre | `app/Observers/MemberObserver.php` | 2 |
| Observer emprunt | `app/Observers/EmpruntObserver.php` | 3 |
| Livewire | `EmpruntForm`, `EmpruntList`, `TableauDeBord` | 3 |
| Mails + Events | `~/Desktop/laravel-notifications-core.md` | 4 |
| Reverb (temps réel) | plus tard | 5 |

---

## Convention de nommage

| Couche | Convention | Exemple |
|--------|------------|---------|
| Tables SQL | anglais, snake_case | `members`, `emprunts`, `books` |
| Modèles Eloquent | anglais, PascalCase | `Member`, `Emprunt`, `Book` |
| Colonnes métier Biblio | souvent français | `titre`, `nom`, `prenom` |
| Colonnes Emprunt | mixte | `statut`, `date_emprunt`, `approuve_par` |

**Profil membre :** `Member` ne duplique pas `nom` / `email` — ces données viennent de `User` via `member.user`. Seules les données **bibliothèque** sont sur `members` : `card_number`, `phone`, `address`, `registration_date`.

**Désactivation :** `User` et `Member` ont tous deux `SoftDeletes`. Un membre dont le `User` est supprimé (soft) ou `is_active = false` ne doit plus emprunter (vérification applicative, pas cascade SQL).

---

## Schéma des tables Emprunt

### `members`

| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `user_id` | bigint | FK → `users.id`, UNIQUE, cascade on hard delete |
| `card_number` | string | UNIQUE (ex. `MB-00001`) |
| `phone` | string | UNIQUE, nullable |
| `address` | string | nullable |
| `registration_date` | date | NOT NULL |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

### `emprunts`

| Colonne | Type | Contrainte |
|---------|------|------------|
| `id` | bigint | PK |
| `book_id` | bigint | FK → `books.id`, restrict on delete |
| `member_id` | bigint | FK → `members.id`, restrict on delete |
| `statut` | enum | `brouillon\|en_attente\|approuve\|refuse\|rendu` |
| `workflow_state` | string | default `'brouillon'` — **marking Symfony** |
| `date_emprunt` | date | nullable |
| `date_retour_prevue` | date | nullable (RG-14 : +14 j à l'approbation) |
| `date_retour_reelle` | date | nullable (rempli au retour) |
| `approuve_par` | bigint | FK → `users.id`, nullable, null on delete |
| `note_membre` | text | nullable |
| `note_bibliotécaire` | text | nullable |
| `deleted_at` | timestamp | SoftDelete |
| `created_at` / `updated_at` | timestamp | — |

---

## Enum `StatutEmprunt`

```php
// Modules/Emprunt/app/Enums/StatutEmprunt.php
enum StatutEmprunt: string
{
    case Brouillon = 'brouillon';
    case EnAttente = 'en_attente';
    case Approuve  = 'approuve';
    case Refuse    = 'refuse';
    case Rendu     = 'rendu';
}
```

Cast sur `Emprunt` :

```php
protected $casts = [
    'statut' => StatutEmprunt::class,
    'date_emprunt' => 'date',
    'date_retour_prevue' => 'date',
    'date_retour_reelle' => 'date',
];
```

---

## `statut` vs `workflow_state`

Deux colonnes, **une seule source de vérité métier** :

| Colonne | Rôle |
|---------|------|
| `workflow_state` | Marking lu/écrit par **Symfony Workflow** (`MethodMarkingStore`) |
| `statut` | Valeur métier castée en `StatutEmprunt` (affichage UI, requêtes, observers) |

**Règle :** toujours les synchroniser dans `setWorkflowState()` :

```php
public function setWorkflowState(string $state, array $context = []): void
{
    $this->workflow_state = $state;
    $this->statut = StatutEmprunt::from($state);
}
```

Ne jamais modifier `statut` ou `workflow_state` directement dans Livewire — passer par le service workflow.

---

## Diagramme des transitions

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
              │  rendu  │  (état terminal)
              └─────────┘
```

| Transition | De | Vers | Permission Spatie | Rôle |
|------------|-----|------|-------------------|------|
| `demander` | brouillon | en_attente | `créer-emprunt` | MEMBRE |
| `approuver` | en_attente | approuve | `approuver-emprunt` | BIBLIOTHECAIRE, ADMIN |
| `refuser` | en_attente | refuse | `refuser-emprunt` | BIBLIOTHECAIRE, ADMIN |
| `rendre` | approuve | rendu | `valider-retour` | BIBLIOTHECAIRE, ADMIN |
| `annuler` | en_attente | brouillon | `annuler-emprunt` | MEMBRE (propriétaire, RG-13) |

---

## Règles de gestion par transition

| Transition | RG | Action métier |
|------------|-----|---------------|
| `demander` | RG-09 | Vérifier `book.stock_disponible > 0` |
| `demander` | RG-10 | Membre actif : `!$member->trashed()`, user non trashed, `is_active` |
| `demander` | RG-11 | Livre non soft-deleted |
| `approuver` | RG-07 | `book.stock_disponible--` (transaction DB) |
| `approuver` | RG-12 | Utilisateur BIBLIOTHECAIRE ou ADMIN |
| `approuver` | RG-14 | `date_emprunt = today`, `date_retour_prevue = today + 14 jours` |
| `approuver` | — | `approuve_par = auth()->id()` |
| `refuser` | RG-12 | Idem approbation |
| `refuser` | — | Optionnel : `approuve_par`, `note_bibliotécaire` |
| `rendre` | RG-08 | `book.stock_disponible++` |
| `rendre` | — | `date_retour_reelle = today` |
| `annuler` | RG-13 | `$emprunt->member->user_id === auth()->id()` |

**Retard (RG-15) :** pas une transition — calcul à l'affichage : `statut = approuve` ET `date_retour_prevue < today`.

---

## Étape 1 — Installer Symfony Workflow

```bash
composer require symfony/workflow
```

---

## Étape 2 — `config/workflow.php`

Créer à la racine du projet :

```php
<?php

return [
    'emprunt' => [
        'type' => 'state_machine',
        'marking_store' => [
            'type' => 'method',
            'property' => 'workflowState',
        ],
        'supports' => [
            \Modules\Emprunt\Models\Emprunt::class,
        ],
        'places' => [
            'brouillon',
            'en_attente',
            'approuve',
            'refuse',
            'rendu',
        ],
        'initial_marking' => 'brouillon',
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
```

> Symfony attend les getters/setters `getWorkflowState()` / `setWorkflowState()` — déjà présents sur `Emprunt`.

---

## Étape 3 — Enregistrer le workflow (ServiceProvider)

Dans `EmpruntServiceProvider::register()` :

```php
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;

// ...

$this->app->singleton('workflow.emprunt', function () {
    $config = config('workflow.emprunt');

    $builder = new DefinitionBuilder();
    foreach ($config['places'] as $place) {
        $builder->addPlace($place);
    }
    foreach ($config['transitions'] as $name => $transition) {
        $builder->addTransition(new Transition($name, $transition['from'], $transition['to']));
    }
    $builder->setInitialPlaces([$config['initial_marking']]);
    $definition = $builder->build();

    $markingStore = new MethodMarkingStore(true, 'workflowState');

    return new Workflow($definition, $markingStore, null, 'emprunt');
});

$this->app->alias('workflow.emprunt', WorkflowInterface::class);
```

---

## Étape 4 — `EmpruntWorkflowService`

Fichier : `Modules/Emprunt/app/Services/EmpruntWorkflowService.php`

Responsabilités :

1. Recevoir `Emprunt` + nom de transition
2. Vérifier permission Spatie (`Gate` / `$user->can(...)`)
3. Vérifier RG métier (stock, propriétaire, compte actif)
4. Vérifier `$workflow->can($emprunt, $transition)`
5. Ouvrir une **transaction DB**
6. Appliquer effets métier (dates, stock, `approuve_par`)
7. `$workflow->apply($emprunt, $transition)`
8. `$emprunt->save()`
9. Commit

Exemple de squelette :

```php
public function apply(Emprunt $emprunt, string $transition): void
{
    $workflow = app('workflow.emprunt');

    if (! $workflow->can($emprunt, $transition)) {
        throw new TransitionNotAllowedException($transition);
    }

    DB::transaction(function () use ($emprunt, $transition, $workflow) {
        match ($transition) {
            'demander' => $this->assertCanDemander($emprunt),
            'approuver' => $this->handleApprouver($emprunt),
            'refuser'   => $this->assertBibliothecaire(),
            'rendre'    => $this->handleRendre($emprunt),
            'annuler'   => $this->assertProprietaire($emprunt),
            default     => throw new InvalidArgumentException($transition),
        };

        $workflow->apply($emprunt, $transition);
        $emprunt->save();
    });
}
```

**Important :** le stock (`RG-07`, `RG-08`) vit dans ce service, **pas** dans l'Observer (éviter double décrément ou incohérence sans transaction).

---

## Étape 5 — Relations Eloquent (référence)

| Modèle | Méthode | Type | Clé |
|--------|---------|------|-----|
| `User` | `member()` | `hasOne` | `user_id` |
| `User` | `empruntsApprouves()` | `hasMany` | `approuve_par` |
| `Member` | `user()` | `belongsTo` | — |
| `Member` | `emprunts()` | `hasMany` | — |
| `Emprunt` | `member()` | `belongsTo` | — |
| `Emprunt` | `book()` | `belongsTo` | — |
| `Emprunt` | `approbateur()` | `belongsTo` | `approuve_par` |
| `Book` | `emprunts()` | `hasMany` | — |

---

## Étape 6 — Observer + effets de bord

| Fichier | Déclencheur | Action |
|---------|-------------|--------|
| `MemberObserver` | `User::created` (ou register) | Créer `Member`, `card_number`, email Bienvenue |
| `EmpruntObserver` | transition → `en_attente` | Mail / Event `EmpruntSoumis` |
| `EmpruntObserver` | transition → `approuve` | Mail `EmpruntApprouveMail` |
| `EmpruntObserver` | transition → `refuse` | Mail `EmpruntRefuseMail` |

Enregistrement dans `AppServiceProvider::boot()` :

```php
Member::observe(MemberObserver::class);
Emprunt::observe(EmpruntObserver::class);
```

L'Observer réagit aux **changements de statut** après save — il ne remplace pas le service workflow.

---

## Étape 7 — Livewire

### `EmpruntForm`

- Liste des livres avec `stock_disponible > 0`
- Crée un emprunt en `brouillon` (statut + workflow_state)
- Bouton « Soumettre » → `$workflowService->apply($emprunt, 'demander')`

### `EmpruntList`

- MEMBRE : `$member->emprunts()`
- BIBLIOTHECAIRE / ADMIN : tous les emprunts, filtre par `statut`
- Boutons visibles si `$workflow->can($emprunt, 'approuver')` **et** `$user->can('approuver-emprunt')`
- Badge retard si RG-15

### Exemple bouton conditionnel (Blade)

```blade
@if(app('workflow.emprunt')->can($emprunt, 'approuver') && auth()->user()->can('approuver-emprunt'))
    <button type="button" wire:click="approuver({{ $emprunt->id }})">
        {{ __('Approuver') }}
    </button>
@endif
```

---

## Architecture cible

```
Livewire (EmpruntForm / EmpruntList)
        ↓
EmpruntWorkflowService     ← permissions Spatie + RG + transaction + stock
        ↓
Symfony Workflow           ← états et transitions autorisées
        ↓
Emprunt::save()            ← workflow_state + statut synchronisés
        ↓
EmpruntObserver            ← mails, events Reverb (plus tard)
```

---

## Tests suggérés (Pest)

| Scénario | Assertion |
|----------|-----------|
| `demander` avec stock 0 | exception / validation RG-09 |
| `approuver` en tant que MEMBRE | 403 permission |
| `approuver` OK | statut `approuve`, stock -1, dates renseignées |
| `rendre` OK | statut `rendu`, stock +1, `date_retour_reelle` |
| `annuler` par autre membre | refus RG-13 |
| Transition interdite | `can()` false, pas de save |

---

## Commits suggérés

1. `feat(emprunt): add StatutEmprunt enum and workflow state sync on Emprunt` — déjà fait en grande partie
2. `feat(emprunt): install symfony/workflow and config`
3. `feat(emprunt): add EmpruntWorkflowService with business rules`
4. `feat(emprunt): add EmpruntForm and EmpruntList Livewire components`
5. `feat(emprunt): add observers and mailables`

---

## Voir aussi

- [README racine — Workflow](../README.md#workflow-des-emprunts-symfony-workflow)
- [06 — Prochaines branches](./06-prochaines-branches.md)
- [03 — Rôles Spatie](./03-feat-auth-roles-spatie.md)
- Cores Bureau : `laravel-symfony-workflow-core.md`, `laravel-notifications-core.md`
