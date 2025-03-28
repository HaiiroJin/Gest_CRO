<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;
use App\Http\Controllers\Controller;

class AttestationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show Attestation
    public function show()
    {
        $user = Auth::user();
        if (!$user || !$user->fonctionnaire) {
            abort(403, "Vous n'avez pas accès à cette fonctionnalité.");
        }

        $fonctionnaire = $user->fonctionnaire;
        $attestation = new AttestationTravail();
        $attestation->date_demande = now();
        $attestation->fonctionnaire_id = $fonctionnaire->id;
        $attestation->langue = 'fr'; // default language

        return view('attestation-travail', [
            'fonctionnaire' => $fonctionnaire,
            'attestation' => $attestation,
            'langue' => $attestation->langue
        ]);
    }

    public function print($id)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, "Vous devez être connecté pour accéder à cette page.");
        }

        $attestation = AttestationTravail::findOrFail($id);

        // Only allow admin to print
        if (!Gate::allows('print-attestation')) {
            abort(403, "Vous n'êtes pas autorisé à imprimer cette attestation.");
        }

        $fonctionnaire = $attestation->fonctionnaire;
        return view('attestation-travail', [
            'fonctionnaire' => $fonctionnaire,
            'langue' => $attestation->langue,
            'attestation' => $attestation
        ]);
    }

    public function downloadDemande($id)
    {
        $user = Auth::user();
        if (!$user || !$user->fonctionnaire) {
            abort(403, "Vous n'avez pas accès à cette fonctionnalité.");
        }

        $attestation = AttestationTravail::findOrFail($id);

        // Check if the current user is the owner of the attestation
        if ($attestation->fonctionnaire_id !== $user->fonctionnaire->id) {
            abort(403, "Vous n'êtes pas autorisé à télécharger cette demande.");
        }

        $fonctionnaire = $attestation->fonctionnaire;
        
        return view('demande', [
            'fonctionnaire' => $fonctionnaire,
            'choix_arabe' => $attestation->langue === 'ar',
            'choix_francais' => $attestation->langue === 'fr',
            'attestation' => $attestation
        ]);
    }

}
