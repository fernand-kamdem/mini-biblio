<?php
namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use Modules\Emprunt\Models\Member;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MemberShow extends Component{

    public ?int $memberId=null;
    public string $name='';
    public string $email='';
    public string $phone='';
    public string $card_number='';
    public string $address='';
    public string $registration_date='';
    public Bool $is_active=false;
    public string $member_since = '';


    public function openEditModal():void{
        $this->dispatch('closeShowModal');
        $this->dispatch('openEditModalForm',  memberId: (int) $this->memberId);
    }

    public function openConfirmDeleteModal():void{
        $this->dispatch('closeShowModal');
        $this->dispatch('openConfirmModalForm',  memberId: (int) $this->memberId);
    }


    public function mount(?int $memberId = null): void
    {
        $this->memberId = $memberId;

        if ($this->memberId) {
            $member = Member::with('user')->findOrFail($this->memberId);

            $this->name = $member->user?->name ?? '';
            $this->email = $member->user?->email ?? '';
            $this->phone = $member->phone ?? '';
            $this->card_number = $member->card_number ?? '';
            $this->address = $member->address ?? '';
            $this->registration_date = $member->registration_date
            ? $member->registration_date->format('d/m/Y')
            : '';
            $this->is_active = (bool) ($member->user?->is_active ?? false);
        }
    }
    public function render(){
        return view('emprunt::livewire.member-show');
    }
}

?>