<?php

namespace Modules\Biblio\Providers;

use Livewire\Livewire;
use Modules\Biblio\Livewire\BookList;
use Modules\Biblio\Livewire\CategoryList;
use Modules\Biblio\Livewire\AuthorList;
use Modules\Biblio\Livewire\AuthorForm;
use Modules\Biblio\Livewire\BookForm;
use Modules\Biblio\Livewire\CategoryForm;
use Modules\Biblio\Livewire\BookDetails;
use Modules\Biblio\Livewire\CategoryDetails;
use Modules\Biblio\Livewire\AuthorDetails;
use Nwidart\Modules\Support\ModuleServiceProvider;

class BiblioServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Biblio';

    protected string $nameLower = 'biblio';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->registerLivewireComponents();
    }

    /**
     * Enregistrer chaque composant Livewire du module.
     * Sans ça : <livewire:biblio::xxx> ne fonctionne pas.
     * Pour un nouveau CRUD : ajouter XxxList, XxxForm, XxxDetails ici.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('biblio::book-list', BookList::class);
        Livewire::component('biblio::book-form', BookForm::class);
        Livewire::component('biblio::book-details', BookDetails::class);
        Livewire::component('biblio::category-list', CategoryList::class);
        Livewire::component('biblio::category-form', CategoryForm::class);
        Livewire::component('biblio::category-details', CategoryDetails::class);
        Livewire::component('biblio::author-list', AuthorList::class);
        Livewire::component('biblio::author-form', AuthorForm::class);
        Livewire::component('biblio::author-details', AuthorDetails::class);
    }
}
