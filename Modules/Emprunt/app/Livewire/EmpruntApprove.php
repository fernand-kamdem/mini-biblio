<?php

namespace Modules\Emprunt\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Services\EmpruntWorkflowService;

class EmpruntApprove extends Component
{
    // --- Données du formulaire ---
    public ?int $empruntId = null;
    public ?string $date_retour_souhaitee = null;
    public ?string $note_bibliothecaire = null;

    // --- Approve ---
    public function approve(EmpruntWorkflowService $workflow): void
    {
        if (! Auth::user()?->can('approuver-emprunt')) {
            return;
        }

        if (! $this->empruntId) {
            return;
        }

        $validated = $this->validate([
            'date_retour_souhaitee' => ['required', 'date', 'after_or_equal:today'],
            'note_bibliothecaire' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $workflow) {
            $emprunt = Emprunt::findOrFail($this->empruntId);

            try {
                $emprunt->update([
                    'date_retour_souhaitee' => $validated['date_retour_souhaitee'],
                    'note_bibliothecaire' => $validated['note_bibliothecaire'],
                ]);

                $workflow->apply($emprunt, 'approuver');
                $this->successApprove();
            } catch (\Throwable $th) {
                $this->errorApprove();
            }
        });

        $this->requestCancelApprove();
    }

    
    public function successApprove(): void
    {
        $this->dispatch('successApprove');
    }

    public function errorApprove(): void
    {
        $this->dispatch('successApprove');
    }

    // --- Close ---
    public function requestCancelApprove(): void
    {
        $this->dispatch('closeModalApprove');
    }

    // --- Lifecycle ---
    public function mount(?int $empruntId = null): void
    {
        $this->empruntId = $empruntId;

        if ($this->empruntId) {
            $emprunt = Emprunt::findOrFail($empruntId);
            $this->empruntId = $emprunt->id;
            $this->date_retour_souhaitee = $emprunt->date_retour_souhaitee?->format('Y-m-d') ?? '';
        }
    }

    public function render()
    {
        return view('emprunt::livewire.emprunt-approve');
    }
}
