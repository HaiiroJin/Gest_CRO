<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    protected $table = 'groupe';
    protected $fillable = [
        'libelle',
    ];

    public function fonctionnaires()
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getOptions()
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
