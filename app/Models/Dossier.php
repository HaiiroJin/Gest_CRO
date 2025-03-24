<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dossiers';

    protected $fillable = [
        'nom_dossier',
        'description',
    ];

    protected $casts = [
        'nom_dossier' => 'string',
        'description' => 'string',
    ];

    public function sousDossiers(): HasMany
    {
        return $this->hasMany(SousDossier::class);
    }

    public function dossierFonctionnaires(): HasMany
    {
        return $this->hasMany(DossierFonctionnaire::class);
    }

    public static function getDossiers(): array
    {
        return self::pluck('nom_dossier', 'id')->toArray();
    }
}
