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

        // Only allow admin to print
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, "Vous n'êtes pas autorisé à imprimer cette attestation.");
        }

        $fonctionnaire = $attestation->user->fonctionnaire;
        return view('attestation-travail', [
            'fonctionnaire' => $fonctionnaire,
            'langue' => $attestation->langue
        ]);
    }

    public function downloadDemande($id)
    {
        $attestation = AttestationTravail::findOrFail($id);

        // Check if the current user is the owner of the attestation
        if ($attestation->fonctionnaire_id !== auth()->user()->fonctionnaire_id) {
            abort(403, "Vous n'êtes pas autorisé à télécharger cette demande.");
        }

        $fonctionnaire = $attestation->user->fonctionnaire;
        
        return view('demande', [
            'fonctionnaire' => $fonctionnaire,
            'choix_arabe' => $attestation->langue === 'ar',
            'choix_francais' => $attestation->langue === 'fr',
        ]);
    }

}
