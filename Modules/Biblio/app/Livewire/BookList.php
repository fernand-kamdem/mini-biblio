<?php

namespace Modules\Biblio\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Biblio\Models\Book;
use Modules\Biblio\Models\Category;

/**
 * =============================================================================
 * PATRON CRUD LIVEWIRE (à recopier pour un autre modèle)
 * =============================================================================
 *
 * 3 composants :
 *   XxxList     = parent (orchestre tout : liste + modals)
 *   XxxForm     = enfant create/update  → dispatch bookCreated / bookUpdated / modalClosed
 *   XxxDetails  = enfant show           → dispatch editXxxFromDetails / deleteXxxFromDetails
 *
 * Communication :
 *   Parent → Enfant : props Blade  (:book-id="$editingId", :key="...")
 *   Enfant → Parent : $this->dispatch('event') + $listeners côté parent
 *
 * Règle Livewire critique :
 *   Ne JAMAIS nommer une méthode comme une propriété publique.
 *   Ex. OK  : $showDetailModal + openDetailModal()
 *   Ex. KO  : $showDetailModal + showDetailModal()  ← conflit, la modal ne s'ouvre pas
 *
 * Checklist pour un nouveau CRUD (ex. Author) :
 *   1. AuthorList / AuthorForm / AuthorDetails
 *   2. Les enregistrer dans le ServiceProvider (Livewire::component)
 *   3. Dans la vue list : 3 modals (@if showForm / showDelete / showDetail)
 *   4. Copier les sections ci-dessous et renommer Book → Author
 */
class BookList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    // =========================================================================
    // 0. LISTENERS (événements reçus des enfants)
    // =========================================================================
    // Forme : 'nomEvenementDispatché' => 'methodeDuParent'

    protected $listeners = [
        // BookForm
        'bookCreated' => 'handleBookCreated',
        'bookUpdated' => 'handleBookUpdated',
        'modalClosed' => 'closeFormModal',
        // BookDetails
        'editBookFromDetails' => 'editFromDetails',
        'deleteBookFromDetails' => 'deleteFromDetails',
    ];

    // =========================================================================
    // 1. FILTRES & PAGINATION
    // =========================================================================

    public string $search = '';

    public string $categoryId = '';

    /** Reset page 1 quand un filtre change (évite page vide) */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // 2. CREATE & UPDATE  →  modal + enfant BookForm
    // =========================================================================
    // Vue : @if ($showFormModal) <livewire:biblio::book-form :book-id="$editingId" />

    public bool $showFormModal = false;

    /** null = création ; int = édition de cet id */
    public ?int $editingId = null;

    public function showCreateModal(): void
    {
        if (! Auth::user()?->can('gérer-livres')) {
            return;
        }

        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function showEditModal(int $bookId): void
    {
        if (! Auth::user()?->can('gérer-livres')) {
            return;
        }

        $this->editingId = $bookId;
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
    }

    /** Réponse à BookForm::dispatch('bookCreated') */
    public function handleBookCreated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Book created successfully.'));
    }

    /** Réponse à BookForm::dispatch('bookUpdated') */
    public function handleBookUpdated(): void
    {
        $this->closeFormModal();
        session()->flash('success', __('Book updated successfully.'));
    }

    // =========================================================================
    // 3. DELETE  →  modal de confirmation (pas d'enfant Livewire)
    // =========================================================================

    public bool $showDeleteModal = false;

    public ?int $bookIdToDelete = null;

    public function showConfirmDelete(int $bookId): void
    {
        if (! Auth::user()?->can('gérer-livres')) {
            return;
        }

        $this->bookIdToDelete = $bookId;
        $this->showDeleteModal = true;
    }

    public function deleteBook(): void
    {
        if ($this->bookIdToDelete) {
            $book = Book::find($this->bookIdToDelete);
            if ($book) {
                $book->delete();
                session()->flash('success', __('Book deleted successfully.'));
            }
        }

        $this->closeDeleteModal();
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->bookIdToDelete = null;
    }

    // =========================================================================
    // 4. SHOW / DÉTAIL  →  modal + enfant BookDetails
    // =========================================================================
    // Vue : @if ($showDetailModal) <livewire:biblio::book-details :book-id="$bookIdToShow" />

    public bool $showDetailModal = false;

    public ?int $bookIdToShow = null;

    public function openDetailModal(int $bookId): void
    {
        if (! Auth::user()?->can('gérer-livres')) {
            return;
        }

        $this->bookIdToShow = $bookId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->bookIdToShow = null;
    }

    /** Depuis BookDetails : ferme le détail puis ouvre l'édition */
    public function editFromDetails(int $bookId): void
    {
        $this->closeDetailModal();
        $this->showEditModal($bookId);
    }

    /** Depuis BookDetails : ferme le détail puis ouvre la confirmation delete */
    public function deleteFromDetails(int $bookId): void
    {
        $this->closeDetailModal();
        $this->showConfirmDelete($bookId);
    }

    // =========================================================================
    // 5. RENDER
    // =========================================================================

    public function render()
    {
        $books = Book::query()
            ->with(['category', 'authors']) // eager-load → évite N+1
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    // ilike = PostgreSQL (insensible à la casse)
                    $query->where('titre', 'ilike', '%'.$this->search.'%')
                        ->orWhere('isbn', 'ilike', '%'.$this->search.'%');
                });
            })
            ->when($this->categoryId !== '', function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('biblio::livewire.book-list', [
            'books' => $books,
            'categories' => Category::orderBy('created_at', 'desc')->get(),
            'totalBooks' => Book::count(),
        ]);
    }
}
