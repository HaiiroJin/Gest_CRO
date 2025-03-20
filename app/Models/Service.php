<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services';

    protected $fillable = [
        'libelle',
        'libelle_ar',
        'division_id',
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

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function fonctionnaires(): HasMany
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getServicesOptions(): array
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
