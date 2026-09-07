<?php

namespace Modules\Emprunt\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Emprunt\Models\Member;
use Illuminate\Support\Facades\Auth;
use Modules\Emprunt\Notifications\MemberCredentialsNotification;

class MemberForm extends Component
{
    public ?int $memberId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';



    public function cancel(): void
    {
        $this->dispatch('closeModal');
    }


    public function mount(?int $memberId=null){
        $this->memberId=$memberId;
        if ($this->memberId) {
            $member = Member::with('user')->findOrFail($this->memberId);
            $this->name=$member->user->name;
            $this->email=$member->user->email;
            $this->phone=$member->phone;
            $this->address=$member->address;
        }

    }

    public function save(): void
    {

        if(!Auth::user()?->can('gérer-membres')){
            return;
        }

        if ($this->memberId) {
            $member = Member::with('user')->findOrFail($this->memberId);
    
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 'string', 'lowercase', 'email', 'max:255',
                    Rule::unique(User::class)->ignore($member->user_id),
                ],
                'phone' => [
                    'required', 'string',
                    Rule::unique('members', 'phone')->ignore($member->id),
                ],
                'address' => ['nullable', 'string', 'max:255'],
            ]);
    
            DB::transaction(function () use ($member, $validated) {
                $emailChanged = $member->user?->email !== $validated['email'];
                $newPassword = null;
    
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];
    
                if ($emailChanged) {
                    $newPassword = Str::password(12);
                    $userData['password'] = $newPassword;
                    $userData['email_verified_at'] = now();
                }
    
                $member->user->update($userData);
    
                $member->update([
                    'phone' => $validated['phone'],
                    'address' => $validated['address'] ?? null,
                ]);
    
                if ($emailChanged && $newPassword) {
                    $member->user->notify(new MemberCredentialsNotification($newPassword));
                }
            });
    
            $this->dispatch('memberupdated');
            return;
        }
        
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'unique:members,phone'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

    $password = Str::password(12);

    DB::transaction(function () use ($validated, $password) {
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $password, // sera hashé si cast 'hashed' configuré
            'email_verified_at' => now(),
        ]);
        $user->assignRole('MEMBRE');

        Member::create([
            'user_id' => $user->id,
            'card_number' => Member::generateCardNumber(),
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'registration_date' => now()->toDateString(),
        ]);

        $user->notify(new MemberCredentialsNotification($password));
    });

    $this->dispatch('membercreated');
    }

    public function render()
    {
        return view('emprunt::livewire.member-form');
    }
}
?>  