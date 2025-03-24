<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DossierFonctionnaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dossiers_fonctionnaires';

    protected $fillable = [
        'fonctionnaire_id', 
        'dossier_id', 
        'sous_dossier_id',
        'date_ajout',
        'description',
        'fichier',
    ];

    protected $casts = [
        'date_ajout' => 'date',
        'description' => 'string',
        'fichier' => 'string',
    ];

    public function fonctionnaire(): BelongsTo
    {
        return $this->belongsTo(Fonctionnaire::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function sousDossier(): BelongsTo
    {
        return $this->belongsTo(SousDossier::class);
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            // Check if a file was uploaded to the temp directory
            if ($model->fichier && str_contains($model->fichier, 'dossier_fonctionnaire/temp/')) {
                // Determine the destination folder name
                $folderName = $model->fonctionnaire 
                    ? Str::slug($model->fonctionnaire->nom . '-' . $model->fonctionnaire->prenom) 
                    : 'unknown';
                
                // Prepare dossier and sous-dossier names
                $dossierPath = 'sans-dossier';
                if ($model->dossier) {
                    $dossierPath = Str::slug($model->dossier->nom_dossier);
                    
                    // Add sous-dossier if it exists
                    if ($model->sousDossier) {
                        $dossierPath .= '/' . Str::slug($model->sousDossier->nom_sous_doss);
                    }
                }
                
                // Create the destination path
                $currentDate = Carbon::now();
                $destinationDir = "dossier_fonctionnaire/{$folderName}/{$dossierPath}/{$currentDate->year}/{$currentDate->format('m-d')}";
                
                // Extract the filename from the current path
                $filename = basename($model->fichier);
                
                // Move the file
                $destinationPath = "{$destinationDir}/{$filename}";
                
                // Use public disk for storage
                if (Storage::disk('public')->exists($model->fichier)) {
                    Storage::disk('public')->move($model->fichier, $destinationPath);
                    
                    // Quietly update the file path
                    $model->withoutEvents(function () use ($model, $destinationPath) {
                        $model->update(['fichier' => $destinationPath]);
                    });
                }
            }
        });
    }
}
