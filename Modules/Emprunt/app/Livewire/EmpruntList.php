<?php

namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpruntList extends Component
{
    use WithPagination;

    // --- Filtres ---
    public string $search = '';
    public string $status = '';
    public bool $onlyLate = false;

    // --- État des modales ---
    public ?int $viewingId = null;
    public ?int $approvingId = null;
    public ?int $refusingId = null;
    public bool $showModalDetails = false;
    public bool $showModalApprove = false;
    public bool $showModalRefuse=false;

    protected $listeners = [
        'closeModalApprove' => 'closeApproveModal',
        'closeModalRefuse' => 'closeRefuseModal',
        'openModalApprove' => 'handleOpenModalApprove',
        'returnLoan' => 'retourner',
        'successApprove' => 'handleSuccessApprove',
        'errorApprove' => 'handleErrorApprove',
        'successRefuse' => 'handleSuccessRefuse',
        'errorRefuse' => 'handleErrorRefuse',
    ];

    // --- Show ---
    public function openShowModal(?int $loanId): void
    {
        $this->viewingId = $loanId;
        $this->showModalDetails = true;
    }

    public function closeShowModal(): void
    {
        $this->viewingId = null;
        $this->showModalDetails = false;
    }

    // --- Approve ---
    public function openModalApprove(?int $loanId): void
    {
        $this->approvingId = $loanId;
        $this->showModalApprove = true;
    }

    public function closeApproveModal(): void
    {
        $this->approvingId = null;
        $this->showModalApprove = false;
    }

    public function handleOpenModalApprove(int $loanId): void
    {
        $this->closeShowModal();
        $this->openModalApprove($loanId);
    }

    // --- Refuse ---
    public function openModalRefuse(?int $loanId): void
    {
        $this->refusingId = $loanId;
        $this->showModalRefuse = true;
    }

    public function closeRefuseModal(): void
    {
        $this->refusingId = null;
        $this->showModalRefuse = false;
    }

    public function handleOpenModalRefuse(int $loanId): void
    {
        $this->closeShowModal();
        $this->openModalRefuse($loanId);
    }

    // --- Pagination (filtres) ---
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


    public function handleSuccessApprove(){
        session()->flash('success', __('Loan approved successfully'));
    }

    public function handleErrorApprove(){
        session()->flash('error', __('unse erreur dest produite'));
    }

    public function handleSuccessRefuse(){
        session()->flash('success', __('Loan Refused successfully'));
    }

    public function handleErrorRefuse(){
        session()->flash('error', __('unse erreur sest produite'));
    }



    public function retourner(EmpruntWorkflowService $workflow, int $empruntId): void
    {
        if (! Auth::user()?->can('valider-retour')) {
            return;
        }

        try {
            $emprunt = Emprunt::findOrFail($empruntId);
            $workflow->apply($emprunt, 'rendre');
            session()->flash('success', __('Loan returned successfully'));
        } catch (\Throwable $th) {
            session()->flash('error', __('An error occurred while returning the loan'));
        }

        $this->closeShowModal();
    }

    // --- Render ---
    public function render()
    {
        $emprunts = Emprunt::with(['member.user', 'book', 'approbateur'])
            ->when($this->search != '', function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('member', function ($memberQuery) {
                        $memberQuery->where('card_number', 'ilike', '%'.$this->search.'%')
                            ->orWhereHas('user', function ($userQuery) {
                                $userQuery->where('name', 'ilike', '%'.$this->search.'%');
                            });
                    });
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('statut', $this->status);
            })
            ->when($this->onlyLate, function ($query) {
                $query->where('statut', 'approuve')
                    ->where('date_retour_prevue', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('emprunt::livewire.emprunt-list', [
            'emprunts' => $emprunts,
        ]);
    }
}
