<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Emprunt\Models\Emprunt;
use Modules\Emprunt\Models\Member;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'is_active', 'email_verified_at', 'language'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * En mode démonstration, les comptes publics gardent leur e-mail, leur mot de passe
     * et leur statut actif : sinon le premier visiteur pourrait bloquer la démo pour les suivants.
     */
    protected static function booted(): void
    {
        static::updating(function (User $user): ?bool {
            if ($user->isDemoAccount() && $user->isDirty(['email', 'password', 'is_active'])) {
                return false;
            }

            return null;
        });

        static::deleting(fn (User $user): ?bool => $user->isDemoAccount() ? false : null);
    }

    /**
     * Compte public de la démonstration (config/demo.php), seulement quand le mode est actif.
     */
    public function isDemoAccount(): bool
    {
        if (! config('demo.enabled')) {
            return false;
        }

        $demoEmails = array_column(config()->array('demo.accounts'), 'email');

        return in_array($this->getOriginal('email') ?? $this->email, $demoEmails, true);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * @return HasOne<Member, $this>
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * @return HasMany<Emprunt, $this>
     */
    public function empruntsApprouves(): HasMany
    {
        return $this->hasMany(Emprunt::class, 'approuve_par');
    }

    /**
     * @return HasMany<Emprunt, $this>
     */
    public function empruntsRecues(): HasMany
    {
        return $this->hasMany(Emprunt::class, 'recu_par');
    }
}
