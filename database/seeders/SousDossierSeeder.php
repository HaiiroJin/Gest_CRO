<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SousDossierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sous_dossiers = [
            ['nom_sous_doss' => 'Les décisions d\'affectation.', 'id_doss' => 1],
            ['nom_sous_doss' => 'Les fiches d\'informations.', 'id_doss' => 1],
            ['nom_sous_doss' => 'Le formulaire de contrôle du recrutement', 'id_doss' => 1],
            ['nom_sous_doss' => 'La commission médicale d\'admission des fonctionnaires', 'id_doss' => 1],
            ['nom_sous_doss' => 'L\'acte de naissance', 'id_doss' => 11],
            ['nom_sous_doss' => 'L\'acte de mariage', 'id_doss' => 11],
            ['nom_sous_doss' => 'L\'extrait de la fiche anthropométrique', 'id_doss' => 11],
            ['nom_sous_doss' => 'Les diplômes', 'id_doss' => 11],
            ['nom_sous_doss' => 'RIB', 'id_doss' => 11],
            ['nom_sous_doss' => 'CNOPS', 'id_doss' => 11]
        ];

        DB::table('sous_dossiers')->insert($sous_dossiers);
    }
}
