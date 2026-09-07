<?php

namespace Modules\Biblio\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Biblio\Models\Category;
use Livewire\WithPagination;

class CategoryList extends Component{

    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    public string $search='';

    public Bool $showConfirmDeleteForm=false;
    public ?int $categoryIdToDelete=null;

    public function updatingSearch():void{
        $this->resetPage();
    }


    //create
    public bool $showModalForm=false;
    public ?int $editingId=null;

    protected $listeners =[
        'categoryCreated'=>'handleCategoryCreated',
        'categoryUpdated'=>'handleCategoryUpdated',
        'modalClose'=>'closeModalForm',
    ];


    public function openCreateModalForm():void
    {
        $this->showModalForm=true;
        $this->editingId=null;
    }

    public function handleCategoryCreated():void
    {
        $this->closeModalForm();
        session()->flash('success', __('Category created successfully.'));
    }


    //update
    public function openEditModalForm($categoryId):void
    {
        $this->showModalForm=true;
        $this->editingId=$categoryId;
    }


    //delete
    public function openConfirmDeleteModal(?int $categoryId=null){
        $this->showConfirmDeleteForm=true;
        $this->categoryIdToDelete=$categoryId;
    }

    public function closeDeleteModal(){
        $this->showConfirmDeleteForm=false;
        $this->categoryIdToDelete=null;
    }

    public function deleteCategory():void
    {

        if (! Auth::user()?->can('gérer-catégories')) {
            return;
        }

        if ($this->categoryIdToDelete) {
            $category=Category::find($this->categoryIdToDelete);
            if ($category) {
                $category->delete();
                session()->flash('success', __('Category deleted successfully.'));
            }
        }

        $this->closeDeleteModal();
    }



    public function closeModalForm():void
    {
        $this->showModalForm=false;
        $this->editingId=null;
    }

    public function handleCategoryUpdated():void
    {
        $this->closeModalForm();
        session()->flash('success', __('Category updated successfully.'));
    }


    public function render(){
        $categories=Category::query()
        ->when($this->search!=='', function($query){
            $query->where('nom', 'ilike', '%'.$this->search.'%');
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('biblio::livewire.category-list', [
            'categories' =>$categories,
            'totalCategories' => Category::count(),
        ]);
    }

}
