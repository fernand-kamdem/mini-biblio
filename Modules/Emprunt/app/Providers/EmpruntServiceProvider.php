<?php

namespace Modules\Emprunt\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Livewire\Livewire;
use Modules\Emprunt\Livewire\MemberList;
use Modules\Emprunt\Livewire\MemberForm;
use Modules\Emprunt\Livewire\MemberShow;
use Modules\Emprunt\Livewire\EmpruntList;
use Modules\Emprunt\Livewire\LibrarianForm;
use Modules\Emprunt\Livewire\LibrarianShow;
use Modules\Emprunt\Livewire\LibrarianList;
use Modules\Emprunt\Livewire\EmpruntForm;
use Modules\Emprunt\Livewire\MesEmprunts;
use Modules\Emprunt\Livewire\EmpruntShow;
use Modules\Emprunt\Livewire\EmpruntApprove;
use Modules\Emprunt\Livewire\EmpruntRefuse;
use Modules\Emprunt\Livewire\EmpruntShowAdmin;

/**
 * Provider du module Emprunt.
 *
 * Rôle workflow : construire UNE instance StateMachine et la ranger
 * dans le container Laravel sous la clé 'workflow.emprunt'.
 *
 * Chaîne complète :
 *   config/workflow.php          → graphe (places + transitions)
 *   EmpruntServiceProvider       → assemble le StateMachine (ICI)
 *   Emprunt::get/setWorkflowState → marking store (lecture/écriture état)
 *   EmpruntWorkflowService       → can() + RG métier + apply() + save()
 *   Livewire / Controller        → appelle uniquement le service
 */
class EmpruntServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Emprunt';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'emprunt';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     * Enregistrés via parent::register() — ne pas oublier l'appel parent.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

    /**
     * Enregistrement DI du workflow Symfony.
     *
     * register() = phase "lier les services" (avant boot).
     * On y crée le singleton pour que EmpruntWorkflowService puisse
     * faire app('workflow.emprunt') plus tard.
     */
    public function register(): void
    {
        // Obligatoire : enregistre EventServiceProvider + RouteServiceProvider
        // (routes web/api du module). Sans ça, le module perd ses routes.
        parent::register();

        // Singleton = UNE seule instance partagée pour toute l'app.
        // Clé 'workflow.emprunt' = celle utilisée dans EmpruntWorkflowService :
        //   $workflow = app('workflow.emprunt');
        $this->app->singleton('workflow.emprunt', function () {

            // Lit config/workflow.php → clé 'emprunt'
            // Contient : type, marking_store, places, initial_marking, transitions
            $config = config('workflow.emprunt');

            // DefinitionBuilder = "dessine" le graphe d'états.
            // Argument 1 : places (états) — ex. brouillon, en_attente, …
            // Argument 2 : objets Transition construits depuis la config
            $definitionBuilder = new DefinitionBuilder(
                $config['places'], // ← places[] dans config/workflow.php
                array_map(
                    // Pour chaque transition : nom + from + to
                    // Ex. 'approuver' => from en_attente → to approuve
                    fn ($name, $t) => new Transition($name, $t['from'], $t['to']),
                    array_keys($config['transitions']), // noms : demander, approuver…
                    $config['transitions']              // tableaux from/to
                )
            );

            // État de départ d'un nouvel objet (souvent 'brouillon').
            // Doit matcher la valeur default de la colonne workflow_state
            // dans la migration create_emprunts_table.
            $definitionBuilder->setInitialPlaces($config['initial_marking']);

            // MethodMarkingStore = comment Symfony lit/écrit l'état sur le modèle.
            // true  = un seul état à la fois (state_machine, pas multi-places)
            // 'workflowState' → appelle Emprunt::getWorkflowState() / setWorkflowState()
            //   (voir Modules/Emprunt/app/Models/Emprunt.php)
            $markingStore = new MethodMarkingStore(
                true,
                $config['marking_store']['property'] // 'workflowState'
            );

            // StateMachine = moteur final (1 état à la fois).
            // Args :
            //   1. definition  = graphe build() depuis le builder
            //   2. markingStore = get/set sur Emprunt
            //   3. null         = EventDispatcher (optionnel ; events Symfony plus tard)
            //   4. 'emprunt'    = nom du workflow (events : workflow.emprunt.*)
            //
            // Consommé ensuite par EmpruntWorkflowService::apply() :
            //   $workflow->can($emprunt, $transition)
            //   $workflow->apply($emprunt, $transition)  → appelle setWorkflowState()
            return new StateMachine(
                $definitionBuilder->build(),
                $markingStore,
                null,
                'emprunt'
            );
        });
    }


    public function boot(): void
    {
        parent::boot();

        $this->registerLivewireComponents();
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('emprunt::member-list', MemberList::class); 
        Livewire::component('emprunt::member-form', MemberForm::class);
        Livewire::component('emprunt::member-show', MemberShow::class);
        Livewire::component('emprunt::librarian-list', LibrarianList::class);
        Livewire::component('emprunt::librarian-form', LibrarianForm::class);
        Livewire::component('emprunt::librarian-show', LibrarianShow::class);
        Livewire::component('emprunt::emprunt-list', EmpruntList::class);
        Livewire::component('emprunt::emprunt-form', EmpruntForm::class);
        Livewire::component('emprunt::mes-emprunts', MesEmprunts::class);
        Livewire::component('emprunt::emprunt-show', EmpruntShow::class);
        Livewire::component('emprunt::emprunt-show-admin', EmpruntShowAdmin::class);
        Livewire::component('emprunt::emprunt-approve', EmpruntApprove::class);
        Livewire::component('emprunt::emprunt-refuse', EmpruntRefuse::class);
    }
}
