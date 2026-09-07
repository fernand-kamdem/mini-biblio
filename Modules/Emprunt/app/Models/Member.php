<?php

namespace Modules\Emprunt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Modules\Emprunt\Database\Factories\MemberFactory;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'card_number',
        'phone',
        'address',
        'registration_date'
    ];

    protected $casts=
    [
        'registration_date'=>'date'
    ];


    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emprunts():HasMany
    {
        return $this->hasMany(Emprunt::class);
    }

    public static function generateCardNumber(): string
    {
        $year = now()->year;
        $prefix = "MB-{$year}-";

        $last = Member::withTrashed()
            ->where('card_number', 'like', $prefix.'%')
            ->orderByDesc('card_number')
            ->value('card_number');

        $next = $last
            ? ((int) substr($last, -5)) + 1
            : 1;

        return $prefix.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }


    // protected static function newFactory(): MemberFactory
    // {
    //     // return MemberFactory::new();
    // }
}
