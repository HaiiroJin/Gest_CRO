<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Conge;
use App\Models\Fonctionnaire;

class CongeController extends Controller
{
    // Show Conge
    public function show()
    {
        return view('conge');
    }

    public function print($id)
    {
        $conge = Conge::findOrFail($id);

        // Only allow admin to print
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, "Vous n'êtes pas autorisé à imprimer cette attestation.");
        }

        // Use the fonctionnaire associated with the conge record
        $fonctionnaire = Fonctionnaire::findOrFail($conge->fonctionnaire_id);
        
        return view('conge', [
            'fonctionnaire' => $fonctionnaire,
            'langue' => $conge->langue
        ]);
    }

    public function downloadDemande($id)
    {
        $conge = Conge::findOrFail($id);

        // Check if the current user is an admin or the owner of the attestation
        if (!auth()->user()->hasRole('super_admin') && 
            $conge->fonctionnaire_id !== auth()->user()->fonctionnaire_id) {
            abort(403, "Vous n'êtes pas autorisé à télécharger cette demande.");
        }

        // Use the fonctionnaire associated with the conge record
        $fonctionnaire = Fonctionnaire::findOrFail($conge->fonctionnaire_id);
        
        return view('demande-conge', [
            'fonctionnaire' => $fonctionnaire,
            'conge' => $conge,
            'autorisation_sortie_territoire' => $conge->autorisation_sortie_territoire
        ]);
    }

    public function downloadDecision($id)
    {
        $conge = Conge::findOrFail($id);
        
        // Only allow admin to download decision
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, "Vous n'êtes pas autorisé à télécharger cette décision.");
        }

        $fonctionnaire = Fonctionnaire::findOrFail($conge->fonctionnaire_id);
        return view('decision', [
            'fonctionnaire' => $fonctionnaire,
            'conge' => $conge,
            'autorisation_sortie_territoire' => $conge->autorisation_sortie_territoire
        ]);
    }
}
