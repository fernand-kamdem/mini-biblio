<?php
namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use Modules\Emprunt\Models\Member;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MemberList extends Component{

    public string $search='';
    public ?int $editingId=null;
    public ?int $viewingId=null;
    public ?int $deletingId=null;
    public Bool $showModalForm=false;
    public Bool $showModalDetails=false;
    public Bool $showConfirmDeleteForm=false;

    protected $listeners=[
        'membercreated'=>'handleCreatedMember',
        'memberupdated'=>'handleUpdatedMember',
        'closeModal'=>'closeFormModal',
        'openEditModalForm'=>'handleOpenEditModalForm',
        'openConfirmModalForm'=>'handleOpenConfirmModalForm',
        'closeShowModal'=>'closeDetailsModal'
    ];


    public function openCreateModal(){
        $this->editingId=null;
        $this->showModalForm=true;
    }

    public function openEditModal(?int $memberId){
        $this->editingId=$memberId;
        $this->showModalForm=true;
    }

    public function openDetailsModal(?int $memberId): void
    {
        $this->viewingId = $memberId;
        $this->showModalDetails = true;
    }

    public function openConfirmDeleteModal(?int $memberId): void
    {
        $this->deletingId = $memberId;
        $this->showConfirmDeleteForm = true;
    }

    public function  closeDeleteModal(): void
    {
        $this->deletingId = null;
        $this->showConfirmDeleteForm = false;
    }

    public function deleteMember(): void
    {
        if (! Auth::user()?->can('gérer-membres')) {
            return;
        }
        if (! $this->deletingId) {
            return;
        }
        try {
            DB::transaction(function () {
                $member = Member::with('user')->findOrFail($this->deletingId);
                $member->user?->delete();
                $member->delete();
            });
            session()->flash('success', __('Member deleted successfully'));
        } catch (\Throwable $e) {
            session()->flash('error', __('An error occurred while deleting the member'));
        }
        $this->closeDeleteModal();
    }


    public function closeDetailsModal(): void
    {
        $this->viewingId = null;
        $this->showModalDetails = false;
    }

    public function closeFormModal() : void{
        $this->editingId=null;
        $this->showModalForm=false;
    }

    public function handleCreatedMember() : void{
        $this->closeFormModal();
        session()->flash('success', __('Member created succesfully'));
    }

    public function handleUpdatedMember() : void{
        $this->closeFormModal();
        session()->flash('success', __('Member updated succesfully'));
    }

    public function handleOpenEditModalForm(?int $memberId) : void{
        $this->closeFormModal();
        $this->openEditModal($memberId);
    }

    public function handleOpenConfirmModalForm(?int $memberId) : void{
        $this->closeFormModal();
        $this->openConfirmDeleteModal($memberId);
    }

    



    public function render()
    {
        $members = Member::with('user')
            ->when($this->search != '', function ($query) {
                $query->where(function ($q) {
                    $q->where('card_number', 'ilike', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'ilike', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('emprunt::livewire.member-list', [
            'members' => $members,
            'totalMembers' => Member::count(),
        ]);
    }

}
?>