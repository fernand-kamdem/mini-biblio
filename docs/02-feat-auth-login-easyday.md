# 2 — Branche `feat/auth-login-easyday`

**But :** calquer Easy Day-O (`is_active` + middleware + alias Spatie + SoftDeletes) **sans** casser register / verify / forgot.

```bash
git checkout feat/auth-breeze-livewire   # ou master après merge
git checkout -b feat/auth-login-easyday
```

## Fichiers à créer

### `database/migrations/xxxx_add_is_active_column_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_active')->default(true);
});
```

`default(true)` : tout nouveau membre est actif. Un admin pourra passer à `false`.

### `database/migrations/xxxx_add_soft_deletes_to_users_table.php`

```php
Schema::table('users', function (Blueprint $table) {
    $table->softDeletes();
});
```

Suppression douce (README mini-biblio). **Ne pas** ajouter `username` unique ici (casse register + factory).

### `app/Http/Middleware/IsActive.php`

Ordre **obligatoire** :

1. Si `! Auth::check()` ? `$next` (sinon crash visiteur : `user()` est `null`)
2. Si `Auth::user()->is_active ?? true` ? `$next` (`?? true` : `null` n’est pas traité comme désactivé, utile avec `actingAs` / factory)
3. Sinon `logout()` + redirect `login` + flash `error`

Ne **pas** lire `Auth::user()->is_active` **avant** `Auth::check()`.

Voir le fichier actuel du repo pour le code commenté complet.

## Fichiers à modifier

### `bootstrap/app.php`

| Ajout | Commentaire |
|--------|-------------|
| `use App\Http\Middleware\IsActive;` | Sans ça : `Class IsActive not found` |
| `use Spatie\Permission\Middleware\PermissionMiddleware;` | Alias pour `->middleware('permission:…')` |
| `use Spatie\Permission\Middleware\RoleMiddleware;` | Idem `role` |
| `$middleware->alias(['permission' => …, 'role' => …]);` | Comme Easy Day-O |
| `$middleware->appendToGroup('web', [IsActive::class]);` | Toutes les routes web |

### `app/Models/User.php`

| Modification | Commentaire |
|--------------|-------------|
| `use SoftDeletes;` | Avec la migration |
| `'is_active' => 'boolean'` dans `casts()` | Évite 0/1 ambigus |
| `MustVerifyEmail` **conservé** | Contrairement à Easy Day-O |
| Un seul fillable | Si `#[Fillable]` + `$fillable`, l’attribut peut **ignorer** `is_active` |

### `database/factories/UserFactory.php`

```php
'is_active' => true,
```

**Indispensable** : `actingAs($user)` réutilise le modèle **en mémoire**. Sans `is_active`, la valeur est `null` ; le cast boolean peut donner `false` ; `IsActive` redirige en 302 ; tests dashboard / verify-email / confirm-password **rouges**.

Option :

```php
public function inactive(): static
{
    return $this->state(fn () => ['is_active' => false]);
}
```

### `resources/views/livewire/pages/auth/login.blade.php`

Sous le status Breeze, afficher le flash du middleware :

```blade
@if (session('error'))
    <div class="mb-4 text-sm text-red-600">{{ session('error') }}</div>
@endif
```

`IsActive` envoie `session('error')`, Breeze n’affiche que `session('status')`.

### `app/Livewire/Forms/LoginForm.php`

**Laisser le login par `email`** (vue `form.email`).  
Le blocage compte inactif est dans **`IsActive`**, pas forcément dans le Form (Easy Day-O).  
Throttle + remember : déjà Breeze, ne pas casser.

## Commandes

```bash
php artisan migrate
php artisan test --filter=Auth
```

Test manuel : `UPDATE users SET is_active = 0 WHERE email = '…'` puis accès dashboard ? login + message.

## Tests Auth — 302 au lieu de 200

Cause habituelle : factory sans `is_active` + middleware trop tôt.  
Pas un bug Breeze. Corriger factory + ordre `IsActive`.

## Commit

```bash
git add app/Http/Middleware/IsActive.php bootstrap/app.php app/Models/User.php \
  database/migrations/*is_active* database/migrations/*soft_deletes* \
  database/factories/UserFactory.php resources/views/livewire/pages/auth/login.blade.php
git commit -m "Ajouter is_active, SoftDeletes et middleware IsActive (Spatie aliases)."
```
