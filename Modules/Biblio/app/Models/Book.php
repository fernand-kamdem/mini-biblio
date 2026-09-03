<?php

namespace Modules\Biblio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Biblio\Database\Factories\BookFactory;
use Modules\Emprunt\Models\Emprunt;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'titre',
        'isbn',
        'annee_parution',
        'stock_total',
        'stock_disponible',
        'description',
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function emprunts():HasMany
    {
        return $this->hasMany(Emprunt::class);        
    }

    protected static function newFactory(): BookFactory
    {
        return BookFactory::new();
    }
}
