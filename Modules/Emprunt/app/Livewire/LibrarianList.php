<?php

namespace Modules\Emprunt\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class LibrarianList extends Component
{
    use WithPagination;

    // --- Filtres ---
    public string $search = '';

    // --- État des modales ---
    public ?int $editingId = null;
    public ?int $viewingId = null;
    public ?int $deletingId = null;
    public bool $showModalForm = false;
    public bool $showModalDetails = false;
    public bool $showConfirmDeleteForm = false;

    protected $listeners = [
        'librarianCreated' => 'handleCreatedLibrarian',
        'librarianUpdated' => 'handleUpdatedLibrarian',
        'closeModal' => 'closeFormModal',
        'openEditModalForm' => 'handleOpenEditModalForm',
        'openConfirmModalForm' => 'handleOpenConfirmModalForm',
        'closeShowModal' => 'closeDetailsModal',
    ];

    // --- Create / Edit ---
    public function openCreateModal(): void
    {
        $this->editingId = null;
        $this->showModalForm = true;
    }

    public function openEditModal(?int $librarianId): void
    {
        $this->editingId = $librarianId;
        $this->showModalForm = true;
    }

    public function closeFormModal(): void
    {
        $this->editingId = null;
        $this->showModalForm = false;
    }

    public function handleCreatedLibrarian(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Librarian created successfully'));
    }

    public function handleUpdatedLibrarian(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Librarian updated successfully'));
    }

    public function handleOpenEditModalForm(?int $librarianId): void
    {
        $this->closeFormModal();
        $this->openEditModal($librarianId);
    }

    // --- Show ---
    public function openDetailsModal(?int $librarianId): void
    {
        $this->viewingId = $librarianId;
        $this->showModalDetails = true;
    }

    public function closeDetailsModal(): void
    {
        $this->viewingId = null;
        $this->showModalDetails = false;
    }

    // --- Delete ---
    public function openConfirmDeleteModal(?int $librarianId): void
    {
        $this->deletingId = $librarianId;
        $this->showConfirmDeleteForm = true;
    }

    public function closeDeleteModal(): void
    {
        $this->deletingId = null;
        $this->showConfirmDeleteForm = false;
    }

    public function handleOpenConfirmModalForm(?int $librarianId): void
    {
        $this->closeFormModal();
        $this->openConfirmDeleteModal($librarianId);
    }

    public function deleteLibrarian(): void
    {
        if (! Auth::user()?->can('gérer-bibliothécaires')) {
            return;
        }

        if (! $this->deletingId) {
            return;
        }

        try {
            DB::transaction(function () {
                $librarian = User::findOrFail($this->deletingId);
                $librarian->delete();
            });
            session()->flash('success', __('Librarian deleted successfully'));
        } catch (\Throwable $e) {
            session()->flash('error', __('An error occurred while deleting the librarian'));
        }

        $this->closeDeleteModal();
    }

    // --- Render ---
    public function render()
    {
        $librarians = User::query()
            ->role('BIBLIOTHECAIRE')
            ->when($this->search != '', function ($query) {
                $query->where('name', 'ilike', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('emprunt::livewire.librarian-list', [
            'librarians' => $librarians,
            'totalLibrarians' => User::role('BIBLIOTHECAIRE')->count(),
        ]);
    }
}
