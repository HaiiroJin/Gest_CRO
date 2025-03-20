<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JoursFeries extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jours_fériés';

    protected $fillable = [
        'nom',
        'date_depart',
        'date_fin',
        'nombre_jours',
        'description',
    ];

    protected $casts = [
        'nom' => 'string',
        'date_depart' => 'date',
        'date_fin' => 'date',
        'nombre_jours' => 'integer',
        'description' => 'string',
    ];
}
