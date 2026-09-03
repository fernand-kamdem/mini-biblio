<!-- ============================================ -->
<!-- FOOTER - Pied de page                        -->
<!-- ============================================ -->
<footer class="bg-white border-t border-gray-200 px-6 py-4 mt-auto">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
        
        <!-- Partie gauche : Copyright -->
        <div>
            <p>
                &copy; {{ date('Y') }} 
                <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                    Bibliothèque
                </a>
                - Tous droits réservés
            </p>
        </div>
        
        <!-- Partie droite : Liens -->
        <div class="flex items-center gap-4 text-xs flex-wrap justify-center">
            <a href="#" class="hover:text-gray-700 transition-colors">
                <i class="fas fa-shield-alt mr-1"></i>
                Confidentialité
            </a>
            <a href="#" class="hover:text-gray-700 transition-colors">
                <i class="fas fa-file-contract mr-1"></i>
                Conditions
            </a>
            <a href="#" class="hover:text-gray-700 transition-colors">
                <i class="fas fa-life-ring mr-1"></i>
                Aide
            </a>
            <span class="text-gray-300 hidden sm:inline">|</span>
            <span class="flex items-center gap-1">
                <i class="fas fa-circle text-green-500 text-[6px]"></i>
                <span>Système en ligne</span>
            </span>
            <span class="text-gray-300 hidden sm:inline">|</span>
            <span>v1.0.0</span>
        </div>
    </div>
</footer>