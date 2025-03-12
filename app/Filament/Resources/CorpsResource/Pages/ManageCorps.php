<?php

namespace App\Filament\Resources\CorpsResource\Pages;

use App\Filament\Resources\CorpsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCorps extends ManageRecords
{
    protected static string $resource = CorpsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
