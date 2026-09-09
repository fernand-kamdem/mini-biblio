<?php
namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Enums\StatutEmprunt;

class EmpruntShow extends Component{

    public ?int $empruntId=null;
    public string $statut='';
    public string $bookId='';
    public string $note_membre='';
    public string $dateRetourSouhaitee='';


    public function openEditModal():void{
        $this->dispatch('closeShowModal');
        $this->dispatch('openEditModalForm',  memberId: (int) $this->memberId);
    }

    public function openConfirmDeleteModal():void{
        $this->dispatch('closeShowModal');
        $this->dispatch('openConfirmModalForm',  memberId: (int) $this->memberId);
    }
    
    public function cancelLoanSubmitted(){
        $this->dispatch('cancelLoanSubmitted');
        return;
    }

    public function requestCancel() : void{
        $this->dispatch('openCancelLoanModal', loanId: (int) $this->empruntId);
    }

    public function mount(?int $empruntId = null): void
    {
        $this->empruntId = $empruntId;

        if ($this->empruntId) {
            $emprunt=Emprunt::findOrFail($empruntId);
            $this->empruntId = $emprunt->id;
            $this->bookId = $emprunt->book_id;
            $this->note_membre=$emprunt->note_membre;
            $this->statut=$emprunt->statut->value;
            $this->dateRetourSouhaitee=$emprunt->date_retour_souhaitee?->format('Y-m-d') ?? '';
        }
    }
    public function render(){
        return view('emprunt::livewire.emprunt-show');
    }
}

?>