<?php

namespace Modules\Biblio\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Biblio\Database\Factories\AuthorFactory;

class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'biographie',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'author_book');
    }

    protected static function newFactory(): AuthorFactory
    {
        return AuthorFactory::new();
    }
}
