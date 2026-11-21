<?php

namespace Modules\Emprunt\Livewire;

use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;
use Symfony\Component\Workflow\Exception\LogicException;

class EmpruntRefuse extends Component
{
    // --- Données du formulaire ---
    public ?int $empruntId = null;

    public ?string $note_bibliothecaire = null;

    // --- Refuse ---
    public function refuse(EmpruntWorkflowService $workflow): void
    {
        if (! Auth::user()?->can('refuser-emprunt')) {
            return;
        }

        if (! $this->empruntId) {
            return;
        }

        $validated = $this->validate([
            'note_bibliothecaire' => ['required', 'string', 'max:255'],
        ]);

        $emprunt = Emprunt::findOrFail($this->empruntId);
        $emprunt->note_bibliothecaire = $validated['note_bibliothecaire'];

        try {
            // Le motif n'est enregistré qu'avec la transition : en cas d'échec, rien ne change.
            $workflow->apply($emprunt, 'refuser');
            $this->successRefuse();
        } catch (DomainException|AuthorizationException|LogicException $exception) {
            $this->dispatch('errorRefuse', message: $exception->getMessage());
        }

        $this->requestCancelRefuse();
    }

    // --- Close ---
    public function requestCancelRefuse(): void
    {
        $this->dispatch('closeModalRefuse');
    }

    public function mount(?int $empruntId = null): void
    {
        $this->empruntId = $empruntId;

        if ($this->empruntId) {
            $emprunt = Emprunt::findOrFail($empruntId);
            $this->empruntId = $emprunt->id;
        }
    }

    public function successRefuse(): void
    {
        $this->dispatch('successRefuse');
    }

    public function render()
    {
        return view('emprunt::livewire.emprunt-refuse');
    }
}
