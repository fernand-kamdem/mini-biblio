<?php

namespace Modules\Emprunt\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Biblio\Models\Book;
use Modules\Emprunt\Database\Factories\EmpruntFactory;
use Modules\Emprunt\Enums\StatutEmprunt;

class Emprunt extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'book_id',
        'member_id',
        'statut',
        'workflow_state',
        'date_emprunt',
        'date_retour_souhaitee',
        'date_retour_prevue',
        'date_retour_reelle',
        'approuve_par',
        'recu_par',
        'note_membre',
        'note_bibliothecaire',
    ];

    protected $casts = [
        'statut' => StatutEmprunt::class,
        'date_emprunt' => 'date',
        'date_retour_souhaitee' => 'date',
        'date_retour_prevue' => 'date',
        'date_retour_reelle' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function approbateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approuve_par');
    }

    public function recepteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recu_par');
    }

    /**
     * RG-15 : un emprunt est en retard s'il est approuvé et que sa date de retour prévue est passée.
     * Un livre attendu aujourd'hui n'est pas encore en retard.
     *
     * @param  Builder<Emprunt>  $query
     */
    #[Scope]
    protected function enRetard(Builder $query): void
    {
        $query->where('statut', StatutEmprunt::Approuve)
            ->whereDate('date_retour_prevue', '<', today());
    }

    public function estEnRetard(): bool
    {
        return $this->statut === StatutEmprunt::Approuve
            && $this->date_retour_prevue !== null
            && $this->date_retour_prevue->lt(today());
    }

    /**
     * Nombre de jours de retard, 0 si l'emprunt n'est pas en retard.
     */
    public function joursDeRetard(): int
    {
        return $this->estEnRetard()
            ? (int) $this->date_retour_prevue->diffInDays(today())
            : 0;
    }

    // methode pour lire l'etat actuel de l'objet (getter)
    public function getWorkflowState(): string
    {
        return $this->workflow_state;
    }

    // methode pour ecrire l'etat actuel de l'objet (setter)
    public function setWorkflowState(string $state, array $context = []): void
    {
        $this->workflow_state = $state;
        $this->statut = StatutEmprunt::tryFrom($state);
    }

    protected static function newFactory(): EmpruntFactory
    {
        return EmpruntFactory::new();
    }
}
