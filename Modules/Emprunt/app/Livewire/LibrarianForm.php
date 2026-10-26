<?php

namespace Modules\Emprunt\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Emprunt\Notifications\LibrarianCredentialsNotification;

class LibrarianForm extends Component
{
    // --- Champs du formulaire ---
    public ?int $librarianId = null;
    public string $name = '';
    public string $email = '';
    public bool $is_active = false;

    // --- Create / Update ---
    public function save(): void
    {
        if (! Auth::user()?->can('gérer-bibliothécaires')) {
            return;
        }

        if ($this->librarianId) {
            $librarian = User::findOrFail($this->librarianId);

            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 'string', 'lowercase', 'email', 'max:255',
                    Rule::unique(User::class)->ignore($librarian->id),
                ]
            ]);

            DB::transaction(function () use ($librarian, $validated) {
                $emailChanged = $librarian->email !== $validated['email'];
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

                $librarian->update($userData);

                if ($emailChanged && $newPassword) {
                    $librarian->notify(new LibrarianCredentialsNotification($newPassword));
                }
            });

            $this->dispatch('librarianUpdated');

            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        ]);

        $password = Str::password(12);

        DB::transaction(function () use ($validated, $password) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $password,
                'email_verified_at' => now(),
            ]);
            $user->assignRole('BIBLIOTHECAIRE');

            $user->notify(new LibrarianCredentialsNotification($password));
        });

        $this->dispatch('librarianCreated');
    }

    // --- Close ---
    public function cancel(): void
    {
        $this->dispatch('closeModal');
    }

    // --- Lifecycle ---
    public function mount(?int $librarianId = null): void
    {
        $this->librarianId = $librarianId;

        if ($this->librarianId) {
            $librarian = User::findOrFail($this->librarianId);
            $this->name = $librarian->name;
            $this->email = $librarian->email;;
        }
    }

    public function render()
    {
        return view('emprunt::livewire.librarian-form');
    }
}
