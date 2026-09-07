<x-mail::message>
# {{ __('Bienvenue') }}

{{ __('Bonjour :name', ['name' => $user->name]) }},

{{ __('Voici vos identifiants :') }}

- **Email :** {{ $user->email }}
- **Mot de passe :** {{ $password }}

<x-mail::button :url="url('/login')">
    
{{ __('Se connecter') }}
</x-mail::button>

{{ __('Changez ce mot de passe après la première connexion.') }}

{{ __('Merci') }},<br>
{{ config('app.name') }}
</x-mail::message>