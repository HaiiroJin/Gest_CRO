<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;

class AttestationController extends Controller
{
    // Show Attestation
    public function show()
    {
        return view('attestation-travail');
    }

    public function print($id)
    {
        $attestation = AttestationTravail::findOrFail($id);

        if (!$attestation->demande || $attestation->status !== 'signé') {
            abort(404, "Attestation non disponible pour impression.");
        }

        return response($attestation->demande)
            ->header('Content-Type', 'text/html');
    }
}
