# 0 — Machine + `laravel new`

À faire **une fois par poste**, puis à **chaque nouveau projet**, avant toute feat.

## Fichiers concernés (plus tard dans le projet)

| Fichier | Modification | Pourquoi |
|---------|--------------|----------|
| `composer.json` ? `scripts.post-update-cmd` | **Retirer** `"@php artisan boost:update --ansi"` **avant** tout `composer require` / `breeze:install` | Sinon Composer code 1 et Breeze **ne copie aucun fichier** |
| `phpunit.xml` | Ne **pas** changer `DB_CONNECTION=sqlite` / `DB_DATABASE=:memory:` | Les tests Feature Laravel sont prévus pour SQLite mémoire |
| `.github/workflows/tests.yml` | Laisser ; push en **SSH** | Un PAT HTTPS sans scope `workflow` fait échouer le premier push |

## Commandes — une fois par machine

```bash
# Composer officiel (pas apt : clash Symfony Console avec vendor/)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
composer --version   # 2.8+

composer global require laravel/installer

# Tests Pest : driver SQLite (sinon « could not find driver »)
sudo apt install php8.3-sqlite3 php8.3-pdo   # adapter 8.3 / 8.4 à php -v
php -m | grep -i sqlite                      # pdo_sqlite + sqlite3

# GitHub : SSH (évite le PAT workflow)
ssh-keygen -t ed25519 -C "ton@email.com"
# coller ~/.ssh/id_ed25519.pub sur GitHub ? SSH keys
```

## Commandes — chaque projet

```bash
laravel new mon-projet    # starter : None (pas le kit Livewire officiel si tu veux Breeze ensuite)
cd mon-projet

# Si Boost est dans composer.json :
# enlever boost:update du post-update-cmd  OU  php artisan boost:install

composer require livewire/livewire livewire/volt nwidart/laravel-modules spatie/laravel-permission
composer require laravel/breeze --dev
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

php artisan breeze:install livewire --pest --no-interaction
npm install && npm run build
php artisan migrate
```

Git :

```bash
git init && git branch -m master   # ou main
git add . && git commit -m "Socle Laravel Livewire Breeze."
git remote add origin git@github.com:USER/REPO.git
git push -u origin master
```

## Pièges vus sur mini-biblio

| Symptôme | Cause | Fix |
|----------|--------|-----|
| `boost:update` error code 1 | Boost pas installé | Retirer le script ou `boost:install` |
| `ArrayInput` / `InputInterface` fatal | Composer apt | Composer phar |
| Push rejeté `workflow` scope | PAT HTTPS | SSH ou PAT `repo` + `workflow` |
| Tous les tests Auth : `could not find driver` sqlite | Pas de `pdo_sqlite` | `apt install phpX.Y-sqlite3` |
| `anhskohbo/no-captcha` vs Guzzle 8 | Package trop vieux | Plus tard : règle `Http` (voir doc 03) |
