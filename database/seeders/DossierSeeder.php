<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DossierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dossiers = [
            ['nom_dossier' => 'Les décisions d\'affectation et d\'officialisation.'],
            ['nom_dossier' => 'Les notes d\'affectation'],
            ['nom_dossier' => 'Les congés de maladie'],
            ['nom_dossier' => 'Les congés administatifs'],
            ['nom_dossier' => 'Les fiches de notation'],
            ['nom_dossier' => 'Les décisions d\'avancement d\'échellon'],
            ['nom_dossier' => 'Les arrêtes'],
            ['nom_dossier' => 'Les décisions d\'avancement de grade'],
            ['nom_dossier' => 'Les décisions d\'attachement, de mutation et d\'intégration.'],
            ['nom_dossier' => 'Les certificats de travail'],
            ['nom_dossier' => 'Autres']
        ];

        DB::table('dossiers')->insert($dossiers);
    }
}
