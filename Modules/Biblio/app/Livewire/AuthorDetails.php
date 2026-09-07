<?php

namespace Modules\Biblio\Livewire;

use Livewire\Component;
use Modules\Biblio\Models\Author;

class AuthorDetails extends Component
{

    public string $nom;
    public string $prenom;
    public string $biographie;
    public ?int $authorId;
    public ?Author $author=null;

    public function mount(){
        $this->author=Author::findOrFail($this->authorId);
    }

    public function requestEdit():void
    {
        $this->dispatch('closeAuthorView');
        $this->dispatch('showAuthorEdit', authorId: (int) $this->authorId);

    }

    public function requestDelete():void
    {
        $this->dispatch('closeAuthorView');
        $this->dispatch('showAuthorDelete', authorId: (int) $this->authorId);
    }

    public function render()
    {
        return view('biblio::livewire.author-details',
        [
            'author'=>$this->author
        ]);
    }
}