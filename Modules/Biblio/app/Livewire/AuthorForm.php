<?php

namespace Modules\Biblio\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Modules\Biblio\Models\Author;
use Illuminate\Support\Facades\Auth;
class AuthorForm extends Component{

    public ?int $authorId=null;
    public string $nom='';
    public string $prenom='';
    public ?string $biographie=null;

    public function rules():array
    {
        return [
            'nom'=>['required', 'string', 'max:255'],
            'prenom'=>['required', 'string', 'max:255'],
            'biographie'=>['nullable', 'string', 'max:1000'],
        ];
    }

    public function mount(int $authorId=null){
        $this->authorId=$authorId;
        if($this->authorId){
            $author=Author::find($this->authorId);
            $this->nom=$author->nom;
            $this->prenom=$author->prenom;
            $this->biographie=$author->biographie;
        }
    }

    public function cancel():void
    {
        $this->dispatch('closeFormModal');
    }

    public function save():void
    {
        if(!Auth::user()?->can('gérer-auteurs')){
            return;
        }
        $this->validate();
        $data=[
            'nom'=>$this->nom,
            'prenom'=>$this->prenom,
            'biographie'=>$this->biographie,
        ];

        if($this->authorId){
            $author=Author::find($this->authorId);
            $author->update($data);
            $this->dispatch('authorUpdated');
        }else{
            Author::create($data);
            $this->dispatch('authorCreated');
        }
        $this->cancel();
    }

    public function render()
    {
        return view('biblio::livewire.author-form');
    }

}
?>