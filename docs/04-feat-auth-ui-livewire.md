# 4 — Branche `feat/auth-ui-livewire`

**But :** UI auth Tailwind (split-screen), **sans** thème Borex. Comportement Breeze / Livewire inchangé.

```bash
git checkout master   # après merge rôles
git checkout -b feat/auth-ui-livewire
```

## Layout `resources/views/layouts/guest.blade.php`

Layout split-screen (colonne gauche dégradé indigo `lg+` + carte formulaire). Font Awesome 5 en CDN. Vite pour CSS/JS.

Chaque page Volt : `#[Layout('layouts.guest')]`.

> Sur cette branche le fichier s’appelait parfois `layouts.auth` ; la branche i18n (`05`) le standardise à nouveau sur `guest`.

**Pas de `@stack('scripts')`** dans ce layout. Donc **pas de `@push('scripts')`** pour toggle mot de passe ou barre de force : le JS ne s’exécute pas.

## Pages Volt (`resources/views/livewire/pages/auth/`)

| Page | UI | Notes |
|------|-----|--------|
| `login` | Email, MDP, remember, lien forgot | `LoginForm` : `wire:model="form.*"` |
| `register` | Nom, email, MDP + confirmation | Propriétés plates ; barre de force |
| `forgot-password` | Email seulement | N’est **pas** la page « nouveau mot de passe » |
| `reset-password` | Email + nouveau MDP + confirmation | Token `#[Locked]` ; barre de force comme register |
| `verify-email` | Renvoyer le mail + logout | Utilisateur connecté non vérifié |
| `confirm-password` | Layout auth seulement | Formulaire Breeze d’origine ; restyle optionnel |

Liens : `route('login')`, `route('register')`, `route('password.request')` + `wire:navigate`.

## Mot de passe (login / register / reset)

| Besoin | Comment |
|--------|---------|
| Afficher / masquer | `public bool $showPassword` + `wire:click="$toggle('showPassword')"` + `type="{{ $showPassword ? 'text' : 'password' }}"` |
| Confirmation | Idem `$showPasswordConfirmation` |
| Barre de force | `passwordStrength()` PHP + `wire:model.live="password"` |
| Loading bouton | `wire:loading` **et** `wire:target="login"` (ou `register` / `resetPassword`) |

Sans `wire:target`, le clic œil (`$toggle`) affiche « Connexion en cours… ».

Ne pas utiliser `onclick="togglePassword(...)"` ni d’IDs DOM pour la force.

## `confirm-password`

Route `GET /confirm-password`, nom `password.confirm`, middleware `auth`. Sert au middleware Laravel `password.confirm` (zone sensible). **Aucune route mini-biblio ne l’applique encore.**

## Cache vues

Après un layout qui ne s’affiche pas :

```bash
php artisan view:clear
```

## Commit

```bash
git add \
  resources/views/layouts/auth.blade.php \
  resources/views/layouts/guest.blade.php \
  resources/views/livewire/pages/auth/

git commit -m "$(cat <<'EOF'
Restyler les pages d’auth Livewire : layout split-screen, toggles et indicateur de force.

Remplace layouts.guest par layouts.auth et aligne login, register, forgot/reset, verify-email.
EOF
)"
```
