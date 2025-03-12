<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corps extends Model
{
    protected $table = 'corps';
    protected $fillable = [
        'libelle',
        'libelle_ar',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function fonctionnaires()
    {
        return $this->hasMany(Fonctionnaire::class);
    }
    
    public static function getOptions()
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
