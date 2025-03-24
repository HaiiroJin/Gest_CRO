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

class Conge extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'congés';

    protected $fillable = [
        'fonctionnaire_id',
        'status',
        'date_demande',
        'date_depart',
        'date_retour',
        'nombre_jours',
        'type',
        'autorisation_sortie_territoire',
        'demande',
        'decision',
    ];

    protected $casts = [
        'date_demande' => 'date:Y-m-d',
        'date_depart' => 'date:Y-m-d',
        'date_retour' => 'date:Y-m-d',
        'nombre_jours' => 'integer',
        'autorisation_sortie_territoire' => 'boolean',
    ];

    /**
     * Relation to Fonctionnaire directly
     */
    public function fonctionnaire(): BelongsTo
    {
        return $this->belongsTo(Fonctionnaire::class, 'fonctionnaire_id', 'id');
    }

    /**
     * Relation to User (assuming fonctionnaire_id is linked to users table)
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
     * Check if leave request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'signée';
    }

    public function getDateDepartAttribute($value)
    {
        return $value ? substr($value, 0, 10) : $value;
    }

    public function getDateRetourAttribute($value)
    {
        return $value ? substr($value, 0, 10) : $value;
    }

    public function getDateDemandeAttribute($value)
    {
        return $value ? substr($value, 0, 10) : $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conge) {
            $user = Filament::auth()->user();
            
            // If the user is a Super Admin, use the fonctionnaire_id passed in the form
            if ($user->hasRole('super_admin')) {
                // Ensure the fonctionnaire_id comes from the form (this may be set before this)
                // No automatic override for super admins
            } else {
                // For non-admins, set their own fonctionnaire_id
                $conge->fonctionnaire_id = $user->fonctionnaire_id;
            }

            // Default status to "en cours" for all new requests
            $conge->status = 'en cours';

            // Generate demande content
            $fonctionnaire = $user->fonctionnaire;
            $htmlContent = view('demande-conge', [
                'fonctionnaire' => $fonctionnaire,
                'conge' => $conge,
                'autorisation_sortie_territoire' => $conge->autorisation_sortie_territoire
            ])->render();

            $conge->demande = $htmlContent;
        });
    }
}
