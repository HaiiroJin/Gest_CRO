<?php

namespace App\Filament\Resources\DossierFonctionnaireResource\Pages;

use App\Filament\Resources\DossierFonctionnaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDossierFonctionnaire extends ManageRecords
{
    protected static string $resource = DossierFonctionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
