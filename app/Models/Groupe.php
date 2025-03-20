<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Groupe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'groupes';

    protected $fillable = [
        'libelle',
        'libelle_ar',
    ];

    protected $casts = [
        'libelle' => 'string',
        'libelle_ar' => 'string',
    ];

    public function fonctionnaires(): HasMany
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getOptions(): array
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
