# 5 — Branche `feat/i18n-auth`

**But :** internationalisation FR / EN sur les pages **guest auth** (layout + login / register / forgot / reset / verify). Préférence de langue en session ; persistée en base si l’utilisateur est connecté.

Référence Easy Day-O : `SetLocale`, `/lang/{locale}`, colonne langue user, sélecteur conditionné — **sans** copier Borex.

```bash
git checkout master   # après merge UI auth
git checkout -b feat/i18n-auth
```

## Config & `.env`

Dans `config/app.php` :

| Clé | Rôle |
|-----|------|
| `locale` / `fallback_locale` | Défaut app (`APP_LOCALE=fr`, fallback `en`) |
| `user_internationalisation` | Affiche le switcher si `true` (`filter_var` sur `USER_INTERNATIONALISATION`) |

```env
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
USER_INTERNATIONALISATION=true
```

Ne pas lire `env()` directement dans les Blade : passer par `config('app.user_internationalisation')`.

## Persistance `users.language`

Migration `database/migrations/2026_08_20_075916_add_column_language_to_users_table.php` :

```php
$table->enum('language', ['en', 'fr'])->default('fr');
```

`User` : ajouter `language` au `#[Fillable(...)]`.

**Un seul nom partout** : colonne / fillable / middleware / contrôleur = `language` (pas `langue`).

## Middleware `SetLocale`

`app/Http/Middleware/SetLocale.php` — enregistré sur le groupe `web` dans `bootstrap/app.php` :

```php
$locale = Auth::user()?->language
    ?? Session::get('locale', config('app.locale'));

App::setLocale($locale);
```

Ordre : user connecté ? session ? config.

## Route + contrôleur

`routes/web.php` :

```php
Route::get('/lang/{locale}', [LocaleController::class, 'setLocale'])
    ->name('change.language');
```

`LocaleController::setLocale` :

1. Valide `fr` \| `en`
2. `session()->put('locale', $locale)`
3. Si auth : `Auth::user()->update(['language' => $locale])`
4. `return back()`

La méthode route doit matcher le nom réel du contrôleur (`setLocale`, pas un ancien `toggle_langue`).

## UI switcher (guest)

| Fichier | Rôle |
|---------|------|
| `resources/views/components/language-switcher.blade.php` | Liens FR / EN ? `route('change.language', …)` ; affiché seulement si `config('app.user_internationalisation')` |
| `resources/views/layouts/guest.blade.php` | Inclut `<x-language-switcher />` (coin haut droit) |

Les pages Volt auth utilisent `#[Layout('layouts.guest')]` (layout split-screen de la branche UI, renommé / rétabli en `guest`).

Optionnel plus tard : `wire:navigate` sur les `<a>` du switcher pour éviter un full reload.

## Fichiers de traduction

| Fichier | Usage |
|---------|--------|
| `lang/fr.json` / `lang/en.json` | Textes UI via `__('…')` — **clés en anglais** |
| `lang/fr/validation.php` / `en/…` | Messages de règles (`required`, `email`, …) |
| `lang/fr/auth.php` | `auth.failed`, `auth.throttle` |

Les JSON **ne remplacent pas** `validation.php`.  
`$errors->get('email')` = nom du **champ** ; le texte vient de `validation.php`.  
Pour « adresse e-mail » au lieu de « email » dans le message : remplir `attributes` dans `validation.php`.

## Convention UI guest

1. Clés `__('Log in')`, `__('Create account')`, etc. (anglais)
2. Traductions dans `lang/fr.json`
3. Plus de texte FR en dur dans le layout / pages auth guest

Périmètre de cette branche : **guest uniquement** (layout + 6 pages auth). Dashboard / profile / navigation app ? branche suivante éventuelle.

## Checklist manuelle

1. `USER_INTERNATIONALISATION=true` (+ `php artisan config:clear` si cache)
2. `/login` ? switcher visible
3. Clic EN ? labels / panneau gauche en anglais ; session `locale=en`
4. Clic FR ? retour français
5. `USER_INTERNATIONALISATION=false` ? switcher masqué
6. Après login (user avec `language`) : middleware lit la colonne user

## Commit (fichiers i18n)

```bash
git add \
  .env.example \
  app/Http/Controllers/LocaleController.php \
  app/Http/Middleware/SetLocale.php \
  app/Models/User.php \
  bootstrap/app.php \
  config/app.php \
  database/migrations/2026_08_20_075916_add_column_language_to_users_table.php \
  lang/ \
  resources/views/components/language-switcher.blade.php \
  resources/views/layouts/guest.blade.php \
  resources/views/layouts/auth.blade.php \
  resources/views/livewire/pages/auth/ \
  routes/web.php

git commit -m "$(cat <<'EOF'
Ajouter l’i18n FR/EN sur les pages guest auth.

Persiste la locale (session + users.language), expose un switcher conditionné par USER_INTERNATIONALISATION, et traduit le layout guest ainsi que les écrans login/register/reset/verify.
EOF
)"
```

(`layouts/auth.blade.php` : suppression si le layout unique est à nouveau `guest`.)
