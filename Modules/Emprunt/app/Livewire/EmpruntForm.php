<?php

namespace Modules\Emprunt\Livewire;

use Livewire\Component;
use Livewire\WithPagination; 
use Modules\Emprunt\Models\Emprunt;
use Modules\Biblio\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Emprunt\Enums\StatutEmprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;


class EmpruntForm extends Component
{
    public string $bookSearch = '';
    public ?int $bookId = null;
    public ?int $empruntId = null;
    public ?string $note = null;
    public string $dateRetourSouhaitee = '';

    public function selectBook(int $bookId): void
    {
        $this->bookId = $bookId;
        $this->resetErrorBag('bookId');
    }

    public function clearBook(): void
    {
        $this->bookId = null;
    }

    public function cancel(): void
    {
        $this->dispatch('closeModalForm');
    }


    public function saveDraft() : void{

        if ($this->empruntId) {
            $validated=$this->validate([
                'bookId' => ['required', 'exists:books,id'],
                'dateRetourSouhaitee' => ['required', 'date', 'after_or_equal:today'],
                'note' => ['nullable', 'string', 'max:255'],
            ]);

            $member = Auth::user()?->member;

            if (! $member) {
                session()->flash('error', __('Vous devez être un membre pour emprunter un livre'));
                return;
            }

            DB::transaction(function() use($validated,$member){
            $emprunt=Emprunt::findOrFail($this->empruntId);
            $emprunt->update(
                [
                    'book_id' => $validated['bookId'],
                    'member_id' => $member->id,
                    'statut' => StatutEmprunt::Brouillon,
                    'workflow_state' => 'brouillon',
                    'date_retour_souhaitee' => $validated['dateRetourSouhaitee'],
                    'note_membre' => $validated['note'] ?? null,
                ]
            );
                $this->dispatch('loanDraftUpdated');
            });
            return;
        }
        $validated=$this->validate([
            'bookId' => ['required', 'exists:books,id'],
            'dateRetourSouhaitee' => ['required', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $member = Auth::user()?->member;

        if (! $member) {
            session()->flash('error', __('Vous devez être un membre pour emprunter un livre'));
            return;
        }

        DB::transaction(function() use($validated,$member){
            Emprunt::create(
                [
                    'book_id' => $validated['bookId'],
                    'member_id' => $member->id,
                    'statut' => StatutEmprunt::Brouillon,
                    'workflow_state' => 'brouillon',
                    'date_retour_souhaitee' => $validated['dateRetourSouhaitee'],
                    'note_membre' => $validated['note'] ?? null,
                ]
            );
            $this->dispatch('loanDraftCreated');
        });
    }

    public function submit(EmpruntWorkflowService $workflow): void{

        if ($this->empruntId) {

            $validated=$this->validate([
                'bookId' => ['required', 'exists:books,id'],
                'dateRetourSouhaitee' => ['required', 'date', 'after_or_equal:today'],
                'note' => ['nullable', 'string', 'max:255'],
            ]);

            $member = Auth::user()?->member;

            DB::transaction(function() use($member,$validated,$workflow){
                $emprunt=Emprunt::findOrFail($this->empruntId);
                $emprunt->update(
                    [
                        'book_id' => $validated['bookId'],
                        'member_id' => $member->id,
                        'statut' => StatutEmprunt::Brouillon,
                        'workflow_state' => 'brouillon',
                        'date_retour_souhaitee' => $validated['dateRetourSouhaitee'],
                        'note_membre' => $validated['note'] ?? null,
                    ]
                );
                $emprunt->load(['book', 'member.user']);
                $workflow->apply($emprunt, 'demander');
                $this->dispatch('loanSubmitCreated');
            });


            return;
        }
        $validated=$this->validate([
            'bookId' => ['required', 'exists:books,id'],
            'dateRetourSouhaitee' => ['required', 'date', 'after_or_equal:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $member = Auth::user()?->member;

            DB::transaction(function() use($member,$validated,$workflow){
                $emprunt=Emprunt::create(
                    [
                        'book_id' => $validated['bookId'],
                        'member_id' => $member->id,
                        'statut' => StatutEmprunt::Brouillon,
                        'workflow_state' => 'brouillon',
                        'date_retour_souhaitee' => $validated['dateRetourSouhaitee'],
                        'note_membre' => $validated['note'] ?? null,
                    ]
                );
                $emprunt->load(['book', 'member.user']);
                $workflow->apply($emprunt, 'demander');
                $this->dispatch('loanSubmitCreated');
            });

    }

    public function mount(?int $empruntId=null) :void{           

        if ($this->empruntId) {
            $emprunt=Emprunt::findOrFail($empruntId);
            $this->bookId = $emprunt->book_id;
            $this->note=$emprunt->note_membre;
            $this->dateRetourSouhaitee=$emprunt->date_retour_souhaitee?->format('Y-m-d') ?? '';
        }
    }

    public function render()
    {
        $search = trim($this->bookSearch);

        $books = Book::query()
            ->with('authors:nom,prenom')
            ->select(['id', 'titre', 'stock_disponible'])
            ->where('stock_disponible', '>', 0)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('titre', 'ilike', '%'.$search.'%')
                        ->orWhereHas('authors', function ($authorQuery) use ($search) {
                            $authorQuery
                                ->where('nom', 'ilike', '%'.$search.'%')
                                ->orWhere('prenom', 'ilike', '%'.$search.'%');
                        });
                });
            })
            ->orderBy('titre')
            ->limit(50)
            ->get();

        $selectedBook = $this->bookId
            ? Book::with('authors')->select(['id', 'titre', 'stock_disponible'])->find($this->bookId)
            : null;

        return view('emprunt::livewire.emprunt-form', [
            'books' => $books,
            'selectedBook' => $selectedBook,
        ]);
    }
}