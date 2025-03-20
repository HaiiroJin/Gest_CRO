<?php

namespace App\Filament\Resources\JoursFeriesResource\Pages;

use App\Filament\Resources\JoursFeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJoursFeries extends ManageRecords
{
    protected static string $resource = JoursFeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
