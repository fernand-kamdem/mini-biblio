<?php

namespace Modules\Emprunt\Livewire;

use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;
use Symfony\Component\Workflow\Exception\LogicException;

/**
 * Fenêtre d'approbation : récapitule la demande (membre, livre, stock, retour prévu à J+14)
 * et laisse le bibliothécaire ajouter une note avant d'approuver.
 */
class EmpruntApprove extends Component
{
    public ?int $empruntId = null;

    public ?string $note_bibliothecaire = null;

    public function mount(?int $empruntId = null): void
    {
        $this->empruntId = $empruntId;
    }

    #[Computed]
    public function emprunt(): ?Emprunt
    {
        return $this->empruntId
            ? Emprunt::with(['member.user', 'book'])->find($this->empruntId)
            : null;
    }

    public function approve(EmpruntWorkflowService $workflow): void
    {
        if (! Auth::user()?->can('approuver-emprunt') || ! $this->emprunt) {
            return;
        }

        $validated = $this->validate([
            'note_bibliothecaire' => ['nullable', 'string', 'max:255'],
        ]);

        $emprunt = $this->emprunt;
        $emprunt->note_bibliothecaire = $validated['note_bibliothecaire'];

        try {
            // La note n'est enregistrée qu'avec la transition : en cas d'échec, rien ne change.
            $workflow->apply($emprunt, 'approuver');
            $this->dispatch('successApprove');
        } catch (DomainException|AuthorizationException|LogicException $exception) {
            $this->dispatch('errorApprove', message: $exception->getMessage());
        }

        $this->requestCancelApprove();
    }

    public function requestCancelApprove(): void
    {
        $this->dispatch('closeModalApprove');
    }

    public function render()
    {
        return view('emprunt::livewire.emprunt-approve', [
            'dateRetourPrevue' => EmpruntWorkflowService::dateRetourPrevue(),
            'dureeEmprunt' => EmpruntWorkflowService::DUREE_EMPRUNT_JOURS,
        ]);
    }
}
