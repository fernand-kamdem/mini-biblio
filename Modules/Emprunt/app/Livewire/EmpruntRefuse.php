<?php

namespace Modules\Emprunt\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;

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

        DB::transaction(function () use ($validated, $workflow) {
            $emprunt = Emprunt::findOrFail($this->empruntId);
            try {
                $emprunt->update([
                    'note_bibliothecaire' => $validated['note_bibliothecaire'],
                ]);

                $workflow->apply($emprunt, 'refuser');

                $this->successRefuse();
            } catch (\Throwable $th) {

                $this->errorRefuse();
            }
        });

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

    public function errorRefuse(): void
    {
        $this->dispatch('errorRefuse');
    }

    public function render()
    {
        return view('emprunt::livewire.emprunt-refuse');
    }
}
