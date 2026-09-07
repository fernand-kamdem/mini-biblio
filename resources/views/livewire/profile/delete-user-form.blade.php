<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public string $password = '';
    public Bool $showPassword = false;
    public Bool $showDeleteModal = false;


    public function confirmDelete():void{
        $this->showDeleteModal=true;
    }

    public function cancelDelete():void{
        $this->showDeleteModal=false;
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = Auth::user();
        $member = $user->member;

        //verifier si l'utilisateur a des emprunts en cours
        if ($member && $member->emprunts()->whereIn('statut', ['en_attente', 'approuve'])->exists()) {
            $this->addError('password', __('Vous avez des emprunts en cours. Veuillez les régulariser avant de supprimer votre compte.'));
            return;
        }

        //deconnexion de l'utilisateur
        tap($user, $logout(...));

        //supprimer le membre et l'utilisateur
        DB::transaction(function () use ($user, $member) {
            $member?->delete(); // soft delete si SoftDeletes activé sur Member
            $user->delete();    // soft delete si SoftDeletes activé sur User
        });

        //redirection vers la page d'accueil
        $this->redirect('/', navigate: true);
    }
}; ?>

<div class="w-full max-w-full">
    <section class="w-full max-w-full space-y-6">
        <!-- En-tête -->
        <div class="w-full flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-red-600">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                    {{ __('Delete Account') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 whitespace-nowrap">
                    <i class="fas fa-exclamation-circle mr-1.5 text-red-500"></i>
                    {{ __('Irreversible action') }}
                </span>
            </div>
        </div>

        <!-- Carte d'avertissement -->
        <div class="w-full rounded-xl border border-red-200 bg-red-50 p-5">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-exclamation text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">
                        {{ __('Warning: This action is irreversible') }}
                    </h3>
                    <ul class="mt-2 space-y-1 text-xs text-red-700">
                        <li class="flex items-center gap-2">
                            <i class="fas fa-circle text-[6px] text-red-500"></i>
                            {{ __('All your personal data will be permanently deleted') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-circle text-[6px] text-red-500"></i>
                            {{ __('Your account cannot be recovered') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fas fa-circle text-[6px] text-red-500"></i>
                            {{ __('All associated resources will be removed') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bouton de suppression -->
        <div class="w-full flex justify-end">
            <button
                type="button"
                wire:click="confirmDelete"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
            >
                <i class="fas fa-trash-alt"></i>
                {{ __('Delete Account') }}
            </button>
        </div>

        <!-- Modal personnalisé -->
        @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" 
             x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = true, 50)">
            
            <div class="fixed inset-0 bg-black/50 transition-opacity"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="show = false; @this.cancelDelete()">
            </div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white p-6 text-left shadow-xl transition-all"
                     x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <form wire:submit="deleteUser">
                        <!-- En-tête -->
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">
                                    {{ __('Are you sure?') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('This action cannot be undone. All your data will be permanently deleted.') }}
                                </p>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="mt-4 rounded-lg bg-yellow-50 border border-yellow-200 p-3">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-circle mr-2 text-yellow-600"></i>
                                {{ __('Please enter your password to confirm.') }}
                            </p>
                        </div>

                        <!-- Mot de passe avec bouton afficher/masquer -->
                        <div class="mt-6">
                            <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <x-text-input
                                    wire:model="password"
                                    id="password"
                                    name="password"
                                    type="{{ $showPassword ? 'text' : 'password' }}"
                                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150"
                                    placeholder="{{ __('Enter your password') }}"
                                    autocomplete="current-password"
                                />
                                <button 
                                    type="button" 
                                    wire:click="$toggle('showPassword')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <i class="fas {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }} text-gray-400 hover:text-gray-600 transition-colors"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-red-600" />
                        </div>

                        <!-- Boutons -->
                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                            <button
                                type="button"
                                wire:click="cancelDelete"
                                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-colors"
                            >
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Cancel') }}
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="deleteUser"
                                class="w-full sm:w-auto rounded-lg bg-red-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors disabled:opacity-70"
                            >
                                <span wire:loading.remove wire:target="deleteUser">
                                    <i class="fas fa-trash-alt mr-2"></i>
                                    {{ __('Delete Account') }}
                                </span>
                                <span wire:loading wire:target="deleteUser">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    {{ __('Deleting...') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </section>
</div>
