<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttestationController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/attestation/print/{id}', [AttestationController::class, 'print'])->name('attestation.print');

