# Playbooks — mini-biblio

Guides **branche par branche** pour reproduire le projet (auth → modules → profil → emprunts UI).

| Ordre | Branche | Fichier | Objectif | Statut |
|------:|---------|---------|----------|--------|
| 0 | — | [00-machine-et-laravel-new.md](./00-machine-et-laravel-new.md) | Composer, Boost, SQLite tests, Git SSH | ✅ |
| 1 | `feat/auth-breeze-livewire` | [01-feat-auth-breeze-livewire.md](./01-feat-auth-breeze-livewire.md) | Login, register, verify, reset, remember | ✅ |
| 2 | `feat/auth-login-easyday` | [02-feat-auth-login-easyday.md](./02-feat-auth-login-easyday.md) | `is_active`, `IsActive`, Spatie aliases, SoftDeletes | ✅ |
| 3 | `feat/auth-roles-spatie` | [03-feat-auth-roles-spatie.md](./03-feat-auth-roles-spatie.md) | Rôles ADMIN / BIBLIOTHECAIRE / MEMBRE + seed | ✅ |
| 4 | `feat/auth-ui-livewire` | [04-feat-auth-ui-livewire.md](./04-feat-auth-ui-livewire.md) | Layout split-screen, toggles Livewire, barre de force | ✅ |
| 5 | `feat/i18n-auth` | [05-feat-i18n-auth.md](./05-feat-i18n-auth.md) | Locale FR/EN, switcher guest, `users.language` | ✅ |
| 6 | (suivi) | [06-prochaines-branches.md](./06-prochaines-branches.md) | Backlog : i18n app, reCAPTCHA, UI emprunts | 🔄 |
| 7 | `feat/module-emprunt-workflow` | [07-feat-module-emprunt-workflow.md](./07-feat-module-emprunt-workflow.md) | Enum, config Symfony Workflow, `EmpruntWorkflowService` | ✅ |
| 8 | `feat/module-biblio` | [08-feat-module-biblio.md](./08-feat-module-biblio.md) | Catalogue livres / auteurs / catégories Livewire | ✅ |
| 9 | `feat/module-emprunt` (membres) | [09-feat-module-emprunt-membres.md](./09-feat-module-emprunt-membres.md) | CRUD membres + mail identifiants + register → Member | ✅ |
| 10 | `feat/profile-livewire` | [10-feat-profile-livewire.md](./10-feat-profile-livewire.md) | Profil : infos User+Member, password, suppression | ✅ |

Raccourci `laravel new` : [setup_new_project.md](./setup_new_project.md).

**Règle :** une branche = une feat = un commit (ou une PR) = un de ces fichiers.

**Prochaine étape métier :** UI Livewire des emprunts (`EmpruntList` / `EmpruntForm`) — voir [06](./06-prochaines-branches.md) et section « À faire » de [07](./07-feat-module-emprunt-workflow.md).

**Cores génériques (Bureau, hors repo) :**  
`~/Desktop/laravel-symfony-workflow-core.md` · `~/Desktop/laravel-notifications-core.md`

Référence métier : projet `easy_days_o_laravel` (Breeze Volt + middleware compte actif, **sans** copier Borex ni SSO Microsoft).
