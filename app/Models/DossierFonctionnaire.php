<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierFonctionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dossiers_fonctionnaires';

    protected $fillable = [
        'fonctionnaire_id', 
        'dossier_id', 
        'sous_dossier_id',
        'date_ajout',
        'description',
        'fichier',
    ];

    protected $casts = [
        'date_ajout' => 'date',
        'description' => 'string',
        'fichier' => 'string',
    ];

    public function fonctionnaire(): BelongsTo
    {
        return $this->belongsTo(Fonctionnaire::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function sousDossier(): BelongsTo
    {
        return $this->belongsTo(SousDossier::class);
    }
}
