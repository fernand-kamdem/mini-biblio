<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome 5 (plus stable) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Colonne Gauche -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-purple-700 p-12 flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-book-open text-2xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Bibliothèque</h1>
                        <p class="text-white/80 text-sm">Gestionnaire de bibliothèque</p>
                    </div>
                </div>

                <div class="mt-16 space-y-6">
                    <h2 class="text-4xl font-bold text-white leading-tight">
                        Bienvenue sur<br>
                        <span class="text-indigo-200">votre bibliothèque</span>
                        numérique
                    </h2>
                    <p class="text-white/80 text-lg max-w-md">
                        Gérez efficacement vos livres, membres et emprunts dans un espace centralisé et sécurisé.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 text-white">
                                <i class="fas fa-book text-xl text-indigo-200"></i>
                                <div>
                                    <p class="text-2xl font-bold">+1200</p>
                                    <p class="text-sm text-white/70">Livres disponibles</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                            <div class="flex items-center gap-3 text-white">
                                <i class="fas fa-users text-xl text-indigo-200"></i>
                                <div>
                                    <p class="text-2xl font-bold">+450</p>
                                    <p class="text-sm text-white/70">Membres actifs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-white/60 text-sm">
                <p>© 2026 Bibliothèque. Tous droits réservés.</p>
            </div>
        </div>

        <!-- Colonne Droite -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
            <div class="w-full max-w-md">
                <div class="lg:hidden flex justify-center mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-book-open text-2xl text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Bibliothèque</h1>
                            <p class="text-sm text-gray-500">Gestionnaire de bibliothèque</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8">
                    {{ $slot }}
                </div>

                <div class="mt-6 text-center text-sm text-gray-500">
                    <p>
                        Besoin d'aide ? 
                        <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">
                            Contactez-nous
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>