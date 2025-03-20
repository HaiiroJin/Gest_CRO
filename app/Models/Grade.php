<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grades';

    protected $fillable = [
        'libelle',
        'libelle_ar',
        'corps_id',
    ];

    protected $casts = [
        'libelle' => 'string',
        'libelle_ar' => 'string',
    ];

    public function corps(): BelongsTo
    {
        return $this->belongsTo(Corps::class);
    }

    public function fonctionnaires(): HasMany
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getOptions(): array
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
