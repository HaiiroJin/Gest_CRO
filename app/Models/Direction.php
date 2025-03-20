<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'directions';

    protected $fillable = [
        'libelle',
        'libelle_ar',
    ];

    protected $casts = [
        'libelle' => 'string',
        'libelle_ar' => 'string',
    ];

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function fonctionnaires(): HasMany
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getDirectionsOptions(): array
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
