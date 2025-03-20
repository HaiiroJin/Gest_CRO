<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttestationController;
use App\Http\Controllers\CongeController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attestation/demande/{id}', [AttestationController::class, 'downloadDemande'])->name('attestation.demande');
    Route::get('/attestation/print/{id}', [AttestationController::class, 'print'])->name('attestation.print');
    
    Route::get('/conge/demande/{id}', [CongeController::class, 'downloadDemande'])->name('conge.demande');
    Route::get('/conge/decision/{id}', [CongeController::class, 'downloadDecision'])->name('conge.decision');
});
