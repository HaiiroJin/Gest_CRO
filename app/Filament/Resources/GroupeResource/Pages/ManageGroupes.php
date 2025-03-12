<?php

namespace App\Filament\Resources\GroupeResource\Pages;

use App\Filament\Resources\GroupeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGroupes extends ManageRecords
{
    protected static string $resource = GroupeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
