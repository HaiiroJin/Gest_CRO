<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'divisions';

    protected $fillable = [
        'libelle',
        'libelle_ar',
        'direction_id',
    ];

    protected $casts = [
        'libelle' => 'string',
        'libelle_ar' => 'string',
    ];

    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function fonctionnaires(): HasMany
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getDivisionsOptions(): array
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
