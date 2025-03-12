<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';
    protected $fillable = [
        'libelle',
        'libelle_ar',
        'division_id',
        'direction_id',
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function fonctionnaires()
    {
        return $this->hasMany(Fonctionnaire::class);
    }

    public static function getServicesOptions()
    {
        return self::pluck('libelle', 'id')->toArray();
    }
}
