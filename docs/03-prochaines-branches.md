# 3 — Branches à venir (ne pas mélanger avec l’auth actuelle)

À créer **après** le merge de `feat/auth-login-easyday`.

## `feat/auth-recaptcha` (plus tard)

Ne pas `composer require anhskohbo/no-captcha` tant que Guzzle 8 est là.

| Fichier | Modification | Commentaire |
|---------|--------------|-------------|
| `app/Rules/ReCaptcha.php` | `Http::get` vers `siteverify` ; **bypass** `app()->environment('local')` | Copie Easy Day-O, compatible Guzzle 8 |
| `config/services.php` | `recaptcha.sitekey` / `secret` | `.env` : `RECAPTCHAV_SITEKEY`, `RECAPTCHAV_SECRET` |
| `login.blade.php` (Volt) | Widget `g-recaptcha` + `wire:model` token | Skip en `local` comme la Rule |
| `LoginForm` / `login()` Volt | Valider le token | |

## `feat/auth-roles-spatie`

| Fichier | Modification | Commentaire |
|---------|--------------|-------------|
| `database/seeders/RolesPermissionsSeeder.php` | ADMIN, BIBLIOTHECAIRE, MEMBRE | Noms du README mini-biblio, pas ceux Easy Day-O |
| `DatabaseSeeder.php` | `$this->call(...)` | |
| Routes modules | `->middleware('permission:voir-livres')` | Alias déjà posé branche 2 |

Pas besoin de republier Spatie si déjà fait au `laravel new`.

## `feat/module-emprunt` / `feat/module-biblio`

`php artisan module:make …` (nwidart). Observer Membre, workflow, Reverb : README racine, **après** auth stable.

## Ce qu’on ne copie **pas** d’Easy Day-O

- Thème Borex / ChronoTemps  
- SSO Microsoft  
- Register / forgot-password **commentés** (mini-biblio = inscription publique)  
- `MustVerifyEmail` commenté  
