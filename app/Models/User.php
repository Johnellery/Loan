<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar_url',
        'profile_photo_url',
        'email',
        'role_id',
        'branch_id',
        'status',
        'password',
        'first',
        'middle',
        'last',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
    public function Branch(): BelongsTo
    {
        return $this->BelongsTo(Branch::class);
    }
    public function role(): BelongsTo
    {
        return $this->BelongsTo(Role::class);
    }
    public function applicant(): HasMany
    {
        return $this->HasMany(Applicant::class);
    }
    public function billing(): HasMany
    {
        return $this->HasMany(Billing::class);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
    public function isDeactive()
    {
        return $this->status === 'deactivated';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@gmail.com');
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }
}
