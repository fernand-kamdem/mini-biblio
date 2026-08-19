# Playbooks — mini-biblio

Guides **branche par branche** pour reproduire l’auth (Breeze Livewire + calque Easy Day-O) sur un prochain projet.

| Ordre | Branche | Fichier | Objectif |
|------:|---------|---------|----------|
| 0 | — | [00-machine-et-laravel-new.md](./00-machine-et-laravel-new.md) | Composer, Boost, SQLite tests, Git SSH |
| 1 | `feat/auth-breeze-livewire` | [01-feat-auth-breeze-livewire.md](./01-feat-auth-breeze-livewire.md) | Login, register, verify, reset, remember |
| 2 | `feat/auth-login-easyday` | [02-feat-auth-login-easyday.md](./02-feat-auth-login-easyday.md) | `is_active`, `IsActive`, Spatie aliases, SoftDeletes |
| 3 | (plus tard) | [03-prochaines-branches.md](./03-prochaines-branches.md) | reCAPTCHA, seeders rôles, modules |

**Règle :** une branche = une feat = un commit (ou une PR) = un de ces fichiers.

Référence métier : projet `easy_days_o_laravel` (Breeze Volt + middleware compte actif, **sans** copier Borex ni SSO Microsoft).
