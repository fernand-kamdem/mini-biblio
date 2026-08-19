laravel new mini-biblio # starter : None
cd mini-biblio

# retirer boost:update du composer.json s’il est là

composer require livewire/livewire livewire/volt nwidart/laravel-modules spatie/laravel-permission
composer require laravel/breeze --dev
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan breeze:install livewire --pest --no-interaction
npm install && npm run build
php artisan migrate
php artisan serve

# GIT

git init && git branch -m main
git add . && git commit -m "Socle Laravel Livewire Breeze."
git remote add origin git@github.com:USER/mini-biblio.git
git push -u origin main
git checkout -b feat/auth-breeze-livewire
