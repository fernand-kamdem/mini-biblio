<?php

namespace Modules\Biblio\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Biblio\Models\Author;
use Modules\Biblio\Models\Book;
use Modules\Biblio\Models\Category;

/**
 * =============================================================================
 * ENFANT CREATE / UPDATE (inclus dans BookList via modal)
 * =============================================================================
 *
 * Rôle : formulaire uniquement. Ne gère PAS l'ouverture/fermeture de la modal.
 *
 * Reçu du parent :
 *   <livewire:biblio::book-form :book-id="$editingId" :key="'book-form-'.($editingId ?? 'new')" />
 *   - bookId null  → création
 *   - bookId = 5   → édition
 *   - :key force Livewire à recréer le composant quand on change de livre
 *
 * Envoie au parent (dispatch) :
 *   bookCreated  → après create
 *   bookUpdated  → après update
 *   modalClosed  → bouton Annuler
 *
 * Pour un autre CRUD : copier, renommer, adapter rules() + champs + Model.
 */
class BookForm extends Component
{
    // =========================================================================
    // 1. PROPS / ÉTAT DU FORMULAIRE
    // =========================================================================

    /** null = création ; id = édition */
    public ?int $bookId = null;

    public $categoryId = '';

    public $titre = '';

    public $isbn = '';

    public $annee_parution = '';

    public $stock_total = 0;

    public $stock_disponible = 0;

    public $description = '';

    public array $authorIds = [];

    public string $authorSearch = '';

    /** @var \Illuminate\Support\Collection|array */
    public $categories = [];

    // =========================================================================
    // 2. VALIDATION
    // =========================================================================

    protected function rules(): array
    {
        return [
            'categoryId' => 'required|exists:categories,id',
            'titre' => 'required|string|max:255',
            'isbn' => [
                'nullable',
                'string',
                'max:50',
                $this->bookId
                    ? Rule::unique('books', 'isbn')->ignore($this->bookId)
                    : 'unique:books,isbn',
            ],
            'annee_parution' => 'nullable|integer|min:1000|max:9999',
            'stock_total' => 'required|integer|min:0',
            'stock_disponible' => 'required|integer|min:0|lte:stock_total',
            'description' => 'nullable|string',
            'authorIds' => 'array',
            'authorIds.*' => 'exists:authors,id',
        ];
    }

    // =========================================================================
    // 3. MOUNT (préremplir en édition)
    // =========================================================================

    public function mount(?int $bookId = null): void
    {
        $this->bookId = $bookId;
        $this->categories = $this->getCategories();

        if ($this->bookId) {
            $book = Book::with('authors')->findOrFail($this->bookId);

            $this->titre = $book->titre;
            $this->isbn = $book->isbn ?? '';
            $this->annee_parution = $book->annee_parution ?? '';
            $this->stock_total = $book->stock_total;
            $this->stock_disponible = $book->stock_disponible;
            $this->description = $book->description ?? '';
            $this->categoryId = $book->category_id;
            $this->authorIds = $book->authors->pluck('id')->map(fn ($id) => (int) $id)->all();
        }
    }

    // =========================================================================
    // 4. ACTIONS UI SPÉCIFIQUES (auteurs multi-select)
    // =========================================================================

    public function toggleAuthor(int $authorId): void
    {
        if (in_array($authorId, $this->authorIds, true)) {
            $this->authorIds = array_values(array_filter(
                $this->authorIds,
                fn (int $id) => $id !== $authorId
            ));
        } else {
            $this->authorIds[] = $authorId;
        }
    }

    public function removeAuthor(int $authorId): void
    {
        $this->authorIds = array_values(array_filter(
            $this->authorIds,
            fn (int $id) => $id !== $authorId
        ));
    }

    // =========================================================================
    // 5. SAVE / CANCEL  →  dispatch vers BookList
    // =========================================================================

    public function save(): void
    {
        if (! Auth::user()?->can('gérer-livres')) {
            return;
        }

        $this->validate();

        $data = [
            'category_id' => $this->categoryId,
            'titre' => $this->titre,
            'isbn' => $this->isbn ?: null,
            'annee_parution' => $this->annee_parution !== '' ? $this->annee_parution : null,
            'stock_total' => $this->stock_total,
            'stock_disponible' => $this->stock_disponible,
            'description' => $this->description ?: null,
        ];

        if ($this->bookId) {
            $book = Book::findOrFail($this->bookId);
            $book->update($data);
            $book->authors()->sync($this->authorIds);
            $this->dispatch('bookUpdated'); // → BookList::handleBookUpdated
        } else {
            $book = Book::create($data);
            $book->authors()->sync($this->authorIds);
            $this->dispatch('bookCreated'); // → BookList::handleBookCreated
        }
    }

    public function cancel(): void
    {
        $this->dispatch('modalClosed'); // → BookList::closeFormModal
    }

    // =========================================================================
    // 6. HELPERS + RENDER
    // =========================================================================

    protected function getCategories()
    {
        return Category::select(['id', 'nom'])
            ->orderBy('nom')
            ->get();
    }

    public function render()
    {
        $search = trim($this->authorSearch);

        $authors = Author::query()
            ->select(['id', 'nom', 'prenom'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nom', 'ilike', '%'.$search.'%')
                        ->orWhere('prenom', 'ilike', '%'.$search.'%');
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->limit(20)
            ->get();

        $selectedAuthors = empty($this->authorIds)
            ? collect()
            : Author::query()
                ->select(['id', 'nom', 'prenom'])
                ->whereIn('id', $this->authorIds)
                ->orderBy('nom')
                ->get();

        return view('biblio::livewire.book-form', [
            'authors' => $authors,
            'selectedAuthors' => $selectedAuthors,
        ]);
    }
}
