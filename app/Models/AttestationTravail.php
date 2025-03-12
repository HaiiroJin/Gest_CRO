<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Facades\Filament;

class AttestationTravail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'attestation_travail';

    protected $fillable = [
        'fonctionaire_id',
        'nom',
        'prenom',
        'status',
        'date_demande',
        'demande',
    ];

    protected $casts = [
        'date_demande' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Relation to User (assuming fonctionaire_id is linked to users table)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fonctionaire_id', 'fonctionnaire_id');
    }

    /**
     * Scope for filtering user-specific demandes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('fonctionaire_id', $userId);
    }

    /**
     * Check if attestation is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'signé';
    }

    protected static function boot()
{
    parent::boot();

    static::creating(function ($attestation) {
        $user = Filament::auth()->user();
        $attestation->fonctionaire_id = $user->fonctionnaire_id;
        $attestation->nom = $user->fonctionnaire->nom;
        $attestation->prenom = $user->fonctionnaire->prenom;
        $attestation->status = 'en cours';
    });
}

}
