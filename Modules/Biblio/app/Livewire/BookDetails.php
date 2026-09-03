<?php

namespace Modules\Biblio\Livewire;

use Livewire\Component;
use Modules\Biblio\Models\Book;

/**
 * =============================================================================
 * ENFANT SHOW / DÉTAIL (inclus dans BookList via modal)
 * =============================================================================
 *
 * Rôle : affichage en lecture seule. Pas de save ici.
 *
 * Reçu du parent :
 *   <livewire:biblio::book-details :book-id="$bookIdToShow" :key="'book-details-'.$bookIdToShow" />
 *
 * Actions edit / delete :
 *   On ne les exécute PAS ici → on dispatch vers BookList, qui ouvre ses modals.
 *   requestEdit()   → editBookFromDetails
 *   requestDelete() → deleteBookFromDetails
 *
 * Pour un autre CRUD : charger le modèle dans mount(), afficher la vue, garder
 * les 2 dispatch si tu veux les boutons edit/delete dans le détail.
 */
class BookDetails extends Component
{
    // =========================================================================
    // 1. PROPS / ÉTAT
    // =========================================================================

    public $bookId;

    public ?Book $book = null;

    // =========================================================================
    // 2. MOUNT (charger le modèle + relations)
    // =========================================================================

    public function mount(): void
    {
        $this->book = Book::with(['category', 'authors'])
            ->findOrFail($this->bookId);
    }

    // =========================================================================
    // 3. ACTIONS → dispatch vers BookList
    // =========================================================================

    public function requestEdit(): void
    {
        $this->dispatch('editBookFromDetails', bookId: (int) $this->bookId);
    }

    public function requestDelete(): void
    {
        $this->dispatch('deleteBookFromDetails', bookId: (int) $this->bookId);
    }

    // =========================================================================
    // 4. RENDER
    // =========================================================================

    public function render()
    {
        return view('biblio::livewire.book-details', [
            'book' => $this->book,
        ]);
    }
}
