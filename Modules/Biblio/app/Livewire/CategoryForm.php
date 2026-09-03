<?php

namespace Modules\Biblio\Livewire;

use Livewire\Component;
use Modules\Biblio\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CategoryForm extends Component{


    public string $nom='';
    public string $description='';
    public ?int $categoryId=null;

    public function rules(): array
    {
        return [
            'nom' =>['required', 
            $this->categoryId
            ? Rule::unique('categories', 'nom')->ignore($this->categoryId)
            : 'unique:categories,nom',],
            'description'=>['nullable', 'string']
        ];
    }

    public function mount(?int $categoryId=null):void {
        
        $this->categoryId=$categoryId;

        if($this->categoryId){
            $category=Category::findOrFail($this->categoryId);
            $this->nom=$category->nom;
            $this->description=$category->description??'';
        }
    }

    public function cancel(){
        $this->dispatch('modalClose');
    }

    public function save():void
    {

        if (! Auth::user()?->can('gérer-catégories')) {
            return;
        }
        $this->validate();

         $data=[
            'nom'=>$this->nom,
            'description'=>$this->description
         ];

         if($this->categoryId){
            $category=Category::findOrFail($this->categoryId);
            $category->update($data);
            $this->dispatch('categoryUpdated');
         }else{
            $category=Category::create($data);
            $this->dispatch('categoryCreated');
         }
    }

    public function render(){
        return view('biblio::livewire.category-form');
    }
}
?>