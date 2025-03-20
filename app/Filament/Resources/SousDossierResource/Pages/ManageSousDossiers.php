<?php

namespace App\Filament\Resources\SousDossierResource\Pages;

use App\Filament\Resources\SousDossierResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSousDossiers extends ManageRecords
{
    protected static string $resource = SousDossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
