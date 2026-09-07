# Playbooks — mini-biblio

Guides **branche par branche** pour reproduire l’auth (Breeze Livewire + calque Easy Day-O) sur un prochain projet.

| Ordre | Branche | Fichier | Objectif |
|------:|---------|---------|----------|
| 0 | — | [00-machine-et-laravel-new.md](./00-machine-et-laravel-new.md) | Composer, Boost, SQLite tests, Git SSH |
| 1 | `feat/auth-breeze-livewire` | [01-feat-auth-breeze-livewire.md](./01-feat-auth-breeze-livewire.md) | Login, register, verify, reset, remember |
| 2 | `feat/auth-login-easyday` | [02-feat-auth-login-easyday.md](./02-feat-auth-login-easyday.md) | `is_active`, `IsActive`, Spatie aliases, SoftDeletes |
| 3 | `feat/auth-roles-spatie` | [03-feat-auth-roles-spatie.md](./03-feat-auth-roles-spatie.md) | Rôles ADMIN / BIBLIOTHECAIRE / MEMBRE + seed |
| 4 | `feat/auth-ui-livewire` | [04-feat-auth-ui-livewire.md](./04-feat-auth-ui-livewire.md) | Layout split-screen, toggles Livewire, barre de force |
| 5 | `feat/i18n-auth` | [05-feat-i18n-auth.md](./05-feat-i18n-auth.md) | Locale FR/EN, switcher guest, `users.language` |
| 6 | (plus tard) | [06-prochaines-branches.md](./06-prochaines-branches.md) | i18n app, reCAPTCHA, factory, modules |
| 7 | `feat/module-emprunt` | [07-feat-module-emprunt-workflow.md](./07-feat-module-emprunt-workflow.md) | Migrations, modèles, enum, workflow Symfony (en cours) |

Raccourci `laravel new` : [setup_new_project.md](./setup_new_project.md).

**Règle :** une branche = une feat = un commit (ou une PR) = un de ces fichiers.

**Cores génériques (Bureau, hors repo) :**  
`~/Desktop/laravel-symfony-workflow-core.md` · `~/Desktop/laravel-notifications-core.md`

Référence métier : projet `easy_days_o_laravel` (Breeze Volt + middleware compte actif, **sans** copier Borex ni SSO Microsoft).
