# 6 — Branches à venir

À créer **après** le merge de `feat/i18n-auth`. Ne pas mélanger avec l’i18n guest.

## Suite i18n (optionnel)

| Tâche | Commentaire |
|-------|-------------|
| Switcher sur layout `app` / navigation | Même `<x-language-switcher />` |
| Traduire dashboard + profile | Clés EN + `lang/fr.json` |
| `wire:navigate` sur le switcher | Moins de full reload |
| `attributes` dans `validation.php` | Libellés FR des champs d’erreur |

## Reste auth (petits commits possibles)

| Tâche | Fichier | Commentaire |
|-------|---------|-------------|
| Appeler le seeder rôles | `DatabaseSeeder.php` | `$this->call(RolesPermissionsSeeder::class)` |
| Factory | `UserFactory.php` | `'is_active' => true` (sinon tests 302, doc 02) |
| Inscription ? rôle MEMBRE | `register.blade.php` Volt | `$user->assignRole('MEMBRE')` après `User::create` |
| Restyle confirm-password | `confirm-password.blade.php` | Même layout ; champs comme login (toggle Livewire) |

## `feat/auth-recaptcha` (plus tard)

Ne pas `composer require anhskohbo/no-captcha` tant que Guzzle 8 est là.

| Fichier | Modification | Commentaire |
|---------|--------------|-------------|
| `app/Rules/ReCaptcha.php` | `Http::get` vers `siteverify` ; bypass `app()->environment('local')` | Compatible Guzzle 8 |
| `config/services.php` | `recaptcha.sitekey` / `secret` | `.env` : `RECAPTCHAV_SITEKEY`, `RECAPTCHAV_SECRET` |
| `login.blade.php` (Volt) | Widget + `wire:model` token | Skip en `local` comme la Rule |
| `LoginForm` / `login()` | Valider le token | |

## `feat/module-emprunt` / `feat/module-biblio`

`php artisan module:make …` (nwidart). **Après** auth stable.

### Module Emprunt — état au 2026-09-01

| Fait | À faire |
|------|---------|
| Migrations `members`, `emprunts` | `composer require symfony/workflow` |
| Modèles `Member`, `Emprunt` + relations | `config/workflow.php` + ServiceProvider |
| Enum `StatutEmprunt` + sync workflow | `EmpruntWorkflowService` |
| Rôle MEMBRE à l'inscription (Volt) | `MemberObserver`, Livewire, Mails |
| | Reverb (plus tard) |

Playbook détaillé : **[07-feat-module-emprunt-workflow.md](./07-feat-module-emprunt-workflow.md)**.  
Cores Bureau (génériques) : `~/Desktop/laravel-symfony-workflow-core.md`, `~/Desktop/laravel-notifications-core.md`.

### Module Biblio

Catalogue déjà amorcé (`books`, `authors`, `categories`, Livewire). Voir README racine.

Permissions déjà seedées : `->middleware('permission:voir-livres')` (alias Spatie branche 02).

## Ce qu’on ne copie **pas** d’Easy Day-O

- Thème Borex / ChronoTemps
- SSO Microsoft
- Register / forgot-password **commentés** (mini-biblio = inscription publique)
- `MustVerifyEmail` commenté
