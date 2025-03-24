<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Models\DossierFonctionnaire;

class FileController extends Controller
{
    public function viewFile($id)
    {
        // Find the dossier
        $dossier = DossierFonctionnaire::findOrFail($id);
        
        // Check if the file exists
        if (!Storage::disk('public')->exists($dossier->fichier)) {
            abort(404, 'File not found');
        }
        
        // Get the full path and mime type
        $path = Storage::disk('public')->path($dossier->fichier);
        $filename = basename($dossier->fichier);
        $mimeType = mime_content_type($path);
        
        // Read file contents
        $file = Storage::disk('public')->get($dossier->fichier);
        
        // Return file view response
        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
