# 1 — Branche `feat/auth-breeze-livewire`

**But :** installer Breeze **Livewire + Volt + Pest**. Login, register, verify-email, forgot/reset, confirm-password, remember.

Prérequis : [00-machine-et-laravel-new.md](./00-machine-et-laravel-new.md) (`post-update-cmd` Boost retiré, `pdo_sqlite`, Composer phar).

```bash
git checkout master
git checkout -b feat/auth-breeze-livewire
```

## Commandes

```bash
composer require laravel/breeze --dev
php artisan breeze:install livewire --pest --no-interaction
npm install && npm run build
php artisan migrate
php artisan test --filter=Auth
```

Sans `--pest`, Breeze pose PHPUnit. Sans `pdo_sqlite`, tous les tests Auth échouent (`could not find driver`).

## Ce que Breeze copie

| Chemin | Rôle |
|--------|------|
| `routes/auth.php` | Routes Volt + `VerifyEmailController` |
| `routes/web.php` | `require auth.php` ; dashboard `auth` + `verified` ; profil `auth` |
| `app/Livewire/Forms/LoginForm.php` | `Auth::attempt`, throttle, remember |
| `resources/views/livewire/pages/auth/*.blade.php` | Pages Volt (PHP + HTML dans le même fichier) |
| `resources/views/livewire/profile/*` | Profil (sous-composants, pas des routes Volt) |
| `resources/views/layouts/guest.blade.php` | Layout Breeze d’origine (restylé branche 04 ; i18n branche 05) |
| `tests/Feature/Auth/*` | Pest |

`Volt::route('login', 'pages.auth.login')` pointe vers `resources/views/livewire/pages/auth/login.blade.php` (montage Volt dans `VoltServiceProvider`).

## Routes et noms Laravel

Utiliser **`route('login')`**, **`route('register')`**, **`route('password.request')`**, **`route('password.reset')`**, **`route('password.confirm')`**, **`route('verification.notice')`**.

Ne pas linker le nom Volt `pages.auth.login` dans les vues.

| URL | Nom | Middleware | Vue Volt |
|-----|-----|------------|----------|
| `/login` | `login` | `guest` | `pages.auth.login` |
| `/register` | `register` | `guest` | `pages.auth.register` |
| `/forgot-password` | `password.request` | `guest` | `pages.auth.forgot-password` |
| `/reset-password/{token}` | `password.reset` | `guest` | `pages.auth.reset-password` |
| `/verify-email` | `verification.notice` | `auth` | `pages.auth.verify-email` |
| `/confirm-password` | `password.confirm` | `auth` | `pages.auth.confirm-password` |

**Forgot ? reset.** Forgot envoie le mail. Reset n’existe qu’avec le token du lien.

**Confirm-password** n’est pas le reset : utilisateur **déjà connecté**, middleware Laravel `password.confirm`. Aucune route métier ne l’utilise tant qu’on ne l’ajoute pas.

## LoginForm vs Register

Seul le login a un `Livewire\Form` : `attempt` + rate limit + lockout. Register / reset restent des propriétés sur le composant Volt (`$email`, `$password`, …).

Binding login : `wire:model="form.email"`. Binding register : `wire:model="email"`.

## Pièges

| Symptôme | Cause | Fix |
|----------|--------|-----|
| Breeze n’a copié aucun fichier | `boost:update` en `post-update-cmd` | Doc 00 |
| Tests Auth  `could not find driver` | Pas de `pdo_sqlite` | `apt install phpX.Y-sqlite3` |
| `wire:navigate` vers une 404 | Mauvais nom de route | `route('login')`, pas le nom Volt |

## Commit

```bash
git add routes/ app/Livewire tests/Feature/Auth resources/views/livewire \
  resources/views/layouts/guest.blade.php resources/views/layouts/app.blade.php
git commit -m "Installer Breeze Livewire : login, register, verify, reset."
```
