<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use App\Models\Fonctionnaire;
use App\Models\User;

class AttestationTravail extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $table = 'attestations_travail';

    protected $fillable = [
        'fonctionnaire_id',
        'status',
        'date_demande',
        'langue',
        'demande',
        'attestation',
        'raison_rejection',
    ];

    protected $casts = [
        'date_demande' => 'date',
        'status' => 'string',
        'langue' => 'string',
    ];

    /**
     * Relation to Fonctionnaire
     */
    public function fonctionnaire(): BelongsTo
    {
        return $this->belongsTo(Fonctionnaire::class, 'fonctionnaire_id', 'id');
    }

    /**
     * Relation to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fonctionnaire_id', 'fonctionnaire_id');
    }

    /**
     * Scope for filtering user-specific demandes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('fonctionnaire_id', $userId);
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
            
            // If the user is a Super Admin, use the fonctionnaire_id passed in the form
            if ($user->hasRole('super_admin')) {
                // Ensure the fonctionnaire_id comes from the form (this may be set before this)
                // No automatic override for super admins
            } else {
                // For non-admins, set their own fonctionnaire_id
                $attestation->fonctionnaire_id = $user->fonctionnaire_id;
            }

            // Default status to "en cours" for all new requests
            $attestation->status = 'en cours';

            // Generate attestation content when created
            $fonctionnaire = $user->fonctionnaire;
            $htmlContent = view('attestation-travail', [
                'fonctionnaire' => $fonctionnaire,
                'langue' => $attestation->langue
            ])->render();

            $attestation->attestation = $htmlContent;
        });
    }
}
