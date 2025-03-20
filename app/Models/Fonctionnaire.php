<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fonctionnaire extends Model
{
    use SoftDeletes;

    protected $table = 'fonctionnaires';
    protected $fillable = [
        'nom',
        'civilite',
        'prenom',
        'nom_ar',
        'prenom_ar',
        'cin',
        'rib',
        'tel',
        'email',
        'adresse',
        'date_naissance',
        'date_recruitement',
        'date_affectation_cro',
        'poste',
        'situation',
        'matricule_aujour',
        'corps_id',
        'grade_id',
        'groupe_id',
        'direction_id',
        'division_id',
        'service_id',
        'solde_année_prec',
        'solde_année_act',
    ];

    public function direction()
    {
        return $this->belongsTo(Direction::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function corps()
    {
        return $this->belongsTo(Corps::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'fonctionnaire_id', 'id');
    }

    public function getFonctionnaireOptions()
    {
        return self::pluck('nom', 'id')->toArray();
    }

    public function getSoldeCongéAttribute()
    {
        return ($this->solde_année_prec ?? 0) + ($this->solde_année_act ?? 0);
    }
    public function dossierFonctionnaires()
    {
        return $this->hasMany(DossierFonctionnaire::class);
    }
}
