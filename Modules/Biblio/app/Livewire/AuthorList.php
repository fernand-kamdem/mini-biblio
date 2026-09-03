<?php

namespace Modules\Biblio\Livewire;

use Livewire\Component;
use Modules\Biblio\Models\Author;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
class AuthorList extends Component{

    public string $search='';
    public ?int $editingId=null;
    public ?int $authorIdToDelete=null;
    public ?int $authorId=null;
    public bool $showFormModal=false;
    public bool $showConfirmDeleteForm=false;
    public bool $showDetailView=false;

    protected $listeners=
    [
        'closeFormModal'=>'closeFormModal',
        'closeAuthorView'=>'closeDetailView',
        'showAuthorEdit'=>'showEditModal',
        'showAuthorDelete'=>'showDeleteModal',
        'authorCreated'=>'handleAuthorCreated',
        'authorUpdated'=>'handleAuthorUpdated'
    ];

    public function showCreateModal():void
    {
        $this->editingId=null;
        $this->showFormModal=true;
    }

    public function showEditModal(int $authorId):void
    {
        $this->editingId=$authorId;
        $this->showFormModal=true;
    }

    public function openDetailView(int $authorId):void
    {
        $this->authorId=$authorId;
        $this->showDetailView=true;
    }

    public function closeDetailView():void
    {
        $this->authorId=null;
        $this->showDetailView=false;
    }


    public function showDeleteModal(int $authorId):void
    {
        $this->authorIdToDelete=$authorId;
        $this->showConfirmDeleteForm=true;
    }

    public function closeFormModal():void
    {
        $this->editingId=null;
        $this->showFormModal=false;
    }

    public function closeDeleteModal():void
    {
        $this->authorIdToDelete=null;
        $this->showConfirmDeleteForm=false;
    }

    public function handleAuthorCreated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Author created successfully.'));
    }

    public function handleAuthorUpdated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Author updated successfully.'));
    }

    public function deleteAuthor():void
    {
        if(!Auth::user()?->can('gérer-auteurs')){
            return;
        }
        $author=Author::find($this->authorIdToDelete);
        if($author){
            $author->delete();
            session()->flash('success', __('Author deleted successfully.'));
        }else{
            session()->flash('error', __('Author not found.'));
        }
        $this->closeDeleteModal();
    }

    public function render()
    {
        $authors=Author::query()
        ->when($this->search!='', function($query){
            $query->where('nom', 'ilike', '%'.$this->search.'%')
            ->orWhere('prenom', 'ilike', '%'.$this->search.'%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        return view('biblio::livewire.author-list', [
            'authors'=>$authors
        ]);
    }
}
?>