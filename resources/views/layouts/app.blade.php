<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles personnalisés supplémentaires -->
    @stack('styles')
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- ============================================ -->
        <!-- SIDEBAR - Navigation latérale                 -->
        <!-- ============================================ -->
        @include('layouts.partials.sidebar')

        <!-- ============================================ -->
        <!-- CONTENU PRINCIPAL                             -->
        <!-- ============================================ -->
        <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
            <!-- ============================================ -->
            <!-- HEADER - Barre de navigation supérieure      -->
            <!-- ============================================ -->
            @include('layouts.partials.header')

            <!-- ============================================ -->
            <!-- MAIN - Contenu principal de la page          -->
            <!-- ============================================ -->
            <main class="flex-1 p-6">         
                @yield('content')
            </main>

            <!-- ============================================ -->
            <!-- FOOTER - Pied de page                        -->
            <!-- ============================================ -->
            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS POUR LA SIDEBAR MOBILE               -->
    <!-- ============================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('aside');
            const mainContent = document.querySelector('.flex-1');
            
            // État de la sidebar
            let isSidebarOpen = false;
            
            // Fonction pour ouvrir la sidebar
            function openSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    // Ajouter un overlay si nécessaire
                    addOverlay();
                    document.body.style.overflow = 'hidden';
                    isSidebarOpen = true;
                }
            }
            
            // Fonction pour fermer la sidebar
            function closeSidebar() {
                if (window.innerWidth < 1024) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    removeOverlay();
                    document.body.style.overflow = '';
                    isSidebarOpen = false;
                }
            }
            
            // Fonction pour basculer la sidebar
            function toggleSidebar() {
                if (isSidebarOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            // Créer un overlay
            function addOverlay() {
                let overlay = document.getElementById('sidebarOverlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'sidebarOverlay';
                    overlay.className = 'fixed inset-0 bg-black/50 z-20 lg:hidden';
                    overlay.style.display = 'none';
                    document.body.appendChild(overlay);
                    
                    // Fermer la sidebar en cliquant sur l'overlay
                    overlay.addEventListener('click', function() {
                        closeSidebar();
                    });
                }
                overlay.style.display = 'block';
            }
            
            // Supprimer l'overlay
            function removeOverlay() {
                const overlay = document.getElementById('sidebarOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
            
            // Événement click sur le bouton toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
            
            // Fermer la sidebar en cliquant à l'extérieur (mobile)
            document.addEventListener('click', function(event) {
                const isMobile = window.innerWidth < 1024;
                if (isMobile && isSidebarOpen && sidebar) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggle) {
                        closeSidebar();
                    }
                }
            });
            
            // Fermer la sidebar avec la touche Echap
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && isSidebarOpen) {
                    closeSidebar();
                }
            });
            
            // Réinitialiser la sidebar sur les grands écrans
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    removeOverlay();
                    document.body.style.overflow = '';
                    isSidebarOpen = false;
                } else {
                    // Sur mobile, fermer la sidebar par défaut
                    closeSidebar();
                }
            });
            
            // Initialisation : fermer la sidebar sur mobile
            if (window.innerWidth < 1024) {
                closeSidebar();
            }
        });
    </script>

    <!-- Scripts supplémentaires -->
    @stack('scripts')
    @livewireScripts
</body>
</html>