<?php

namespace App\Filament\Resources\FonctionnaireResource\Pages;

use App\Filament\Resources\FonctionnaireResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFonctionnaires extends ManageRecords
{
    protected static string $resource = FonctionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
