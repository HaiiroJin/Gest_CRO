<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SousDossier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sous_dossiers';

    protected $fillable = [
        'nom_sous_doss',
        'description',
        'dossier_id',
    ];

    protected $casts = [
        'nom_sous_doss' => 'string',
        'description' => 'string',
    ];

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    public function dossierFonctionnaires()
    {
        return $this->hasMany(DossierFonctionnaire::class);
    }

    public static function getSousDossiersOptions(): array
    {
        return self::pluck('nom_sous_doss', 'id')->toArray();
    }
}
