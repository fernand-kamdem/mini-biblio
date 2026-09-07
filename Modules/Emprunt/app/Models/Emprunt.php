<?php

namespace Modules\Emprunt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Biblio\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Emprunt\Enums\StatutEmprunt;
// use Modules\Emprunt\Database\Factories\EmpruntFactory;

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
        'date_retour_prevue',
        'date_retour_reelle',
        'approuve_par',
        'note_membre',
        'note_bibliotécaire'
    ];

    protected $casts=
    [
        'statut'=>StatutEmprunt::class,
        'date_emprunt'=>'date',
        'date_retour_prevue'=>'date',
        'date_retour_reelle'=>'date'
    ];

    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book():BelongsTo
    {
        return $this->belongsTo(Book::class);        
    }

    public function approbateur():BelongsTo
    {
        return $this->belongsTo(User::class, 'approuve_par');        
    }

    //methode pour lire l'etat actuel de l'objet (getter)
    public function getWorkflowState(): string
    {
        return $this->workflow_state;
    }

    //methode pour ecrire l'etat actuel de l'objet (setter)
    public function setWorkflowState(string $state, array $context = []): void
    {
        $this->workflow_state = $state;
        $this->statut = StatutEmprunt::tryFrom($state); 
    }

    // protected static function newFactory(): EmpruntFactory
    // {
    //     // return EmpruntFactory::new();
    // }
}
