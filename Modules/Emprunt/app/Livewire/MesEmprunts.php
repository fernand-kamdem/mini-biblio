<?php

namespace Modules\Emprunt\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Emprunt\Models\Emprunt;
use Illuminate\Support\Facades\DB;
use Modules\Emprunt\Services\EmpruntWorkflowService;
use Modules\Emprunt\Enums\StatutEmprunt;


class MesEmprunts extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public bool $onlyLate = false;

    public ?int $editingId = null;
    public ?int $viewingId = null;
    public ?int $deletingId = null;
    public ?int $cancelLoanId = null;
    public bool $showModalForm = false;
    public bool $showDeleteModalForm=false;
    public bool $showModalDetails=false;
    public bool $showCancelLoanModalForm=false;

    protected $listeners = [
        'closeModalForm' => 'closeFormModal',
        'loanDraftCreated' => 'handleLoanDraftCreated',
        'loanSubmitCreated' => 'handleLoanSubmitCreated',
        'loanDraftUpdated' => 'handleLoanDraftUpdated',
        'cancelLoanSubmitted'=>'cancelLoan',
        'openCancelLoanModal'=>'handleOpenConfirmCancelLoanModal'
    ];

    public function handleLoanDraftCreated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Loan created succesfully'));
    }  
    
    public function handleLoanDraftUpdated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Loan update succesfully'));
    }   


    public function handleLoanSubmitCreated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Loan submmited succesfully'));
    }

    public function openConfirmDeleteModal(?int $loanId):void{
        $this->deletingId=$loanId;
        $this->showDeleteModalForm=true;
    }

    public function closeDeleteModal() : void{
        $this->deletingId = null;
        $this->showDeleteModalForm=false;
    }


    public function deleteLoan() : void{

        if(!Auth::user()?->can('supprimer-emprunt')){
            return;
        }

        if (! $this->deletingId) {
            return;
        }
        $loan=Emprunt::findOrFail($this->deletingId);

        DB::transaction(function() use($loan){
            try {
                $loan->delete();
                session()->flash('success', __('Loan deleted successfully'));
            } catch (\Throwable $th) {
                session()->flash('error', __('An error is come'));
            }
        });

        $this->closeDeleteModal();
    }


    public function handleOpenConfirmCancelLoanModal(int $loanId):void{
        $this->closeDetailsModal();
        $this->openConfirmCancelLoanModal($loanId);
    }

    public function openConfirmCancelLoanModal(?int $loanId=null):void{
        $this->cancelLoanId=$loanId;
        $this->showCancelLoanModalForm=true;
    }

    public function cancelLoan(EmpruntWorkflowService $workflow){
        if (! Auth::user()?->can('annuler-emprunt')) {
            return;
        }

        try {
            $emprunt=Emprunt::FindOrFail($this->cancelLoanId);
            $workflow->apply($emprunt, 'annuler');

            session()->flash('success', __('Loan cancel Succesfully'));
        } catch (\Throwable $th) {
            session()->flash('error', __('Loan cancel Succesfully Error'));
        }

        $this->closeCancelLoanModal();

        return;
    }

    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->showModalForm = true;
    }

    public function openEditModal(?int $loanId): void
    {
        $this->editingId = $loanId;
        $this->showModalForm = true;
    }

    public function openDetailsModal(?int $loanId): void  
    {
        $this->viewingId = $loanId;
        $this->showModalDetails = true;
    }

    public function closeDetailsModal(): void  
    {
        $this->viewingId =null;
        $this->showModalDetails = false;
    }

    public function closeCancelLoanModal(): void  
    {
        $this->cancelLoanId =null;
        $this->showCancelLoanModalForm = false;
    }

    public function closeFormModal(): void
    {
        $this->editingId = null;
        $this->showModalForm = false;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingOnlyLate(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $emprunts = Emprunt::with(['member.user', 'book', 'approbateur'])
            ->whereHas('member', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->when($this->search != '', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('book', function ($bookQuery) {
                        $bookQuery->where('titre', 'ilike', '%'.$this->search.'%');
                    });
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('statut', $this->status);
            })
            ->when($this->onlyLate, function ($query) {
                $query->where('statut', StatutEmprunt::Approuve)
                    ->where('date_retour_prevue', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('emprunt::livewire.mes-emprunts', [
            'emprunts' => $emprunts,
        ]);
    }
}