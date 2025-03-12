<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grade';
    protected $fillable = [
        'libelle',
        'libelle_ar',
    ];

    public function corps()
    {
        return $this->belongsTo(Corps::class);
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
