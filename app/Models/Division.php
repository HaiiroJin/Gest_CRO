<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'divisions';
    protected $fillable = [
        'libelle',
        'libelle_ar',
        'direction_id'
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function fonctionnaires()
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getDivisionsOptions()
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
