<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    protected $table = 'directions';
    protected $fillable = [
        'libelle',
        'libelle_ar',
    ];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function fonctionnaires()
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getDirectionsOptions()
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
