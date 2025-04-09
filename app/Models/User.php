<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Authenticatable implements FilamentUser, AuthenticatableContract
{
    use HasFactory, 
        Notifiable, 
        HasRoles, 
        HasPanelShield,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'fonctionnaire_id',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the fonctionnaire associated with the user.
     */
    public function fonctionnaire()
    {
        return $this->belongsTo(Fonctionnaire::class, 'fonctionnaire_id', 'id');
    }

    /**
     * Check if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'active';
    }

    /**
     * Update last login information
     */
    public function updateLastLogin($request)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $request->ip();
        $this->save();
    }

    /**
     * Get a virtual name attribute
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        // If fonctionnaire exists and has both nom and prenom, use full name
        if ($this->fonctionnaire && $this->fonctionnaire->nom && $this->fonctionnaire->prenom) {
            return trim("{$this->fonctionnaire->nom} {$this->fonctionnaire->prenom}");
        }
        
        // Fallback to email
        return $this->email ?? 'Utilisateur';
    }

    /**
     * Get the user name for Filament
     */
    public function getUserName(): string
    {
        return $this->name;
    }

    /**
     * Alias methods for compatibility
     */
    public function name(): string
    {
        return $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken(): ?string
    {
        return $this->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value): void
    {
        $this->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
