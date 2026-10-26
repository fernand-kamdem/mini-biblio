<?php

namespace Modules\Emprunt\Livewire;

use App\Models\User;
use Livewire\Component;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Models\Emprunt;

class EmpruntShowAdmin extends Component
{
    // --- Données affichées ---
    public ?int $empruntId = null;
    public string $statut = '';
    public ?Book $book = null;
    public ?User $approbateur = null;
    public ?User $recepteur = null;
    public string $date_retour_prevue = '';
    public string $date_retour_souhaitee = '';
    public string $date_retour_reelle = '';
    public string $date_emprunt = '';
    public ?string $note_membre = null;
    public ?string $note_bibliothecaire = null;

    // --- Approve ---
    public function requestApproveModal(): void
    {
        $this->dispatch('openModalApprove', loanId: (int) $this->empruntId);
    }


    // --- Return Loan ---
    public function requestRetourner(): void
    {
        $this->dispatch('returnLoan', empruntId: (int) $this->empruntId);
    }


    // --- Lifecycle ---
    public function mount(?int $empruntId = null): void
    {
        $this->empruntId = $empruntId;

        if ($this->empruntId) {
            $emprunt = Emprunt::with(['member.user', 'book', 'approbateur'])->findOrFail($empruntId);
            $this->empruntId = $emprunt->id;
            $this->book = $emprunt->book;
            $this->approbateur = $emprunt->approbateur;
            $this->recepteur = $emprunt->recepteur;
            $this->note_membre = $emprunt->note_membre;
            $this->note_bibliothecaire = $emprunt->note_bibliothecaire;
            $this->statut = $emprunt->statut->value;
            $this->date_retour_prevue = $emprunt->date_retour_prevue?->format('d/m/Y') ?? '';
            $this->date_retour_souhaitee = $emprunt->date_retour_souhaitee?->format('d/m/Y') ?? '';
            $this->date_retour_reelle = $emprunt->date_retour_reelle?->format('d/m/Y') ?? '';
            $this->date_emprunt = $emprunt->date_emprunt?->format('d/m/Y') ?? '';
        }
    }

    public function render()
    {
        return view('emprunt::livewire.emprunt-show-admin');
    }
}
