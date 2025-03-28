<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttestationController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\FileController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attestation', [AttestationController::class, 'show'])->name('attestation.show');
    Route::get('/attestation/demande/{id}', [AttestationController::class, 'downloadDemande'])->name('attestation.demande');
    Route::get('/attestation/print/{id}', [AttestationController::class, 'print'])->name('attestation.print');
    
    Route::get('/conge/demande/{id}', [CongeController::class, 'downloadDemande'])->name('conge.demande');
    Route::get('/conge/decision/{id}', [CongeController::class, 'downloadDecision'])->name('conge.decision');
    Route::get('/conge/avis_retour/{id}', [CongeController::class, 'downloadAvisRetour'])->name('conge.avis_retour');
    Route::get('/dossier/view/{id}', [FileController::class, 'viewFile'])->name('dossier.view');
});
