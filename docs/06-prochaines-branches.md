# 6 — Branches / backlog

État au **2026-09-07**. Les playbooks 00→05, 07→10 sont faits. Ce fichier suit le **reste**.

---

## Fait (ne plus replanifier)

| Élément | Branche / doc |
|---------|----------------|
| Auth Breeze Livewire + compte actif + Spatie | 01 → 03 |
| UI auth + i18n guest FR/EN | 04 → 05 |
| Module Biblio (CRUD catalogue) | [08](./08-feat-module-biblio.md) |
| Workflow Symfony + `EmpruntWorkflowService` | [07](./07-feat-module-emprunt-workflow.md) |
| CRUD membres Livewire + register → Member | [09](./09-feat-module-emprunt-membres.md) |
| Profil Livewire (infos, password, delete) + i18n | [10](./10-feat-profile-livewire.md) |

---

## Suite immédiate — UI emprunts

| Tâche | Fichiers cibles | Commentaire |
|-------|-----------------|-------------|
| Routes emprunts | `Modules/Emprunt/routes/web.php` | Ex. `/emprunt/loans`, permissions `voir-mes-emprunts` / `gérer-emprunts` |
| `EmpruntList` | Livewire + Blade | Filtres statut ; badge retard RG-15 |
| `EmpruntForm` | Livewire + Blade | Création brouillon + `demander` |
| Actions workflow | Boutons list | `approuver` / `refuser` / `rendre` / `annuler` via `EmpruntWorkflowService` |
| Sidebar | `layouts/partials/sidebar.blade.php` | Remplacer `href="#"` Active loans / Overdue |
| i18n emprunts | `lang/fr.json`, `lang/en.json` | Clés listes / statuts / boutons |

Playbook détaillé (RG + transitions) : **[07](./07-feat-module-emprunt-workflow.md)**.

---

## Suite i18n (optionnel / partiel)

| Tâche | Commentaire | Statut |
|-------|-------------|--------|
| Traduire pages profil | Clés dans `lang/fr.json` + `en.json` | ✅ (doc 10) |
| Switcher locale sur layout `app` / header | Même idée que guest | À faire |
| `wire:navigate` sur le switcher | Moins de full reload | À faire |
| `attributes` dans `validation.php` | Libellés FR des champs | Partiel |

---

## Reste auth (petits commits)

| Tâche | Commentaire | Statut |
|-------|-------------|--------|
| Seeder rôles dans `DatabaseSeeder` | `$this->call(RolesPermissionsSeeder::class)` | Vérifier |
| `UserFactory` → `is_active => true` | Sinon tests 302 | Vérifier |
| Register → rôle MEMBRE + Member | Voir doc 09 | ✅ |
| Restyle `confirm-password` | Aligné login (toggle) | À faire |

---

## `feat/auth-recaptcha` (plus tard)

Ne pas `composer require anhskohbo/no-captcha` tant que Guzzle 8 est là.

| Fichier | Modification |
|---------|--------------|
| `app/Rules/ReCaptcha.php` | `Http::get` vers `siteverify` ; bypass `local` |
| `config/services.php` | `recaptcha.sitekey` / `secret` |
| `login.blade.php` | Widget + validation token |

---

## Observers / mails emprunts (après UI)

| Élément | Commentaire |
|---------|-------------|
| `EmpruntObserver` | Mails / events sur transitions |
| Mails Approuvé / Refusé / Rappel retard | Voir core notifications Bureau |
| Reverb | Dashboard / stock live — plus tard |

---

## Ce qu’on ne copie **pas** d’Easy Day-O

- Thème Borex / ChronoTemps
- SSO Microsoft
- Register / forgot-password **commentés** (mini-biblio = inscription publique)
- `MustVerifyEmail` commenté (ici : vérif e-mail **activée**)
