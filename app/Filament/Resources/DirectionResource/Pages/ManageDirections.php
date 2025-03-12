<?php

namespace App\Filament\Resources\DirectionResource\Pages;

use App\Filament\Resources\DirectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDirections extends ManageRecords
{
    protected static string $resource = DirectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
