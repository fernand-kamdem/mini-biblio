<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="space-y-6">
    <!-- En-tête -->
    <div class="text-center">
        <div class="mx-auto w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-envelope-open-text text-2xl text-indigo-600"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">Vérifiez votre email</h2>
        <p class="mt-2 text-sm text-gray-600">
            Un lien de vérification a été envoyé à votre adresse email.
        </p>
    </div>

    <!-- Message de statut -->
    @if (session('status') == 'verification-link-sent')
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Message d'information -->
    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    {{ __('Avant de commencer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="space-y-4 pt-2">
        <!-- Bouton Renvoyer -->
        <button 
            wire:click="sendVerification" 
            wire:loading.attr="disabled"
            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 disabled:opacity-70 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove>
                <i class="fas fa-paper-plane mr-2"></i>
                {{ __('Renvoyer l\'email de vérification') }}
            </span>
            <span wire:loading>
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Envoi en cours...
            </span>
        </button>

        <!-- Bouton Déconnexion -->
        <button 
            wire:click="logout" 
            type="submit" 
            class="w-full flex justify-center items-center py-2.5 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
        >
            <i class="fas fa-sign-out-alt mr-2"></i>
            {{ __('Se déconnecter') }}
        </button>
    </div>

    <!-- Astuce -->
    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            <i class="fas fa-envelope mr-1"></i>
            Vous n'avez pas reçu l'email ? Vérifiez vos spams ou 
            <button wire:click="sendVerification" class="text-indigo-600 hover:text-indigo-700 font-medium underline">
                renvoyez-le
            </button>
        </p>
    </div>
</div>
