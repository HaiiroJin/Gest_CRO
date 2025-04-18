<?php

namespace App\Filament\Exports;

use App\Models\Fonctionnaire;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use App\Notifications\ExportImportSucceeded;
use App\Models\User;

class FonctionnaireExporter extends Exporter
{
    protected static ?string $model = Fonctionnaire::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('civilite'),
            ExportColumn::make('nom'),
            ExportColumn::make('prenom'),
            ExportColumn::make('nom_ar'),
            ExportColumn::make('prenom_ar'),
            ExportColumn::make('cin'),
            ExportColumn::make('rib'),
            ExportColumn::make('tel'),
            ExportColumn::make('email'),
            ExportColumn::make('adresse'),
            ExportColumn::make('date_naissance'),
            ExportColumn::make('date_recruitement'),
            ExportColumn::make('date_affectation_cro'),
            ExportColumn::make('poste'),
            ExportColumn::make('situation'),
            ExportColumn::make('matricule_aujour'),
            ExportColumn::make('corps_id'),
            ExportColumn::make('grade_id'),
            ExportColumn::make('groupe_id'),
            ExportColumn::make('direction_id'),
            ExportColumn::make('division_id'),
            ExportColumn::make('service_id'),
            ExportColumn::make('solde_année_prec'),
            ExportColumn::make('solde_année_act'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Votre export de fonctionnaires est terminé et ' . number_format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' ont été exportées.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' n\'ont pas pu être exportées.';
        }

        return $body;
    }
}
