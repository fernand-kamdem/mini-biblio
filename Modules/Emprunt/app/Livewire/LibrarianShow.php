<?php

namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use App\Models\User;

class LibrarianShow extends Component
{
    // --- Données affichées ---
    public ?int $librarianId = null;
    public string $name = '';
    public string $email = '';
    public bool $is_active = false;

    // --- Edit ---
    public function openEditModal(): void
    {
        $this->dispatch('closeShowModal');
        $this->dispatch('openEditModalForm', librarianId: (int) $this->librarianId);
    }

    // --- Delete ---
    public function openConfirmDeleteModal(): void
    {
        $this->dispatch('closeShowModal');
        $this->dispatch('openConfirmModalForm', librarianId: (int) $this->librarianId);
    }

    // --- Lifecycle ---
    public function mount(?int $librarianId = null): void
    {
        $this->librarianId = $librarianId;

        if ($this->librarianId) {
            $librarian = User::findOrFail($this->librarianId);
            $this->name = $librarian->name ?? '';
            $this->email = $librarian->email ?? '';
            $this->is_active = (bool) ($librarian->is_active ?? false);
        }
    }

    public function render()
    {
        return view('emprunt::livewire.librarian-show');
    }
}
