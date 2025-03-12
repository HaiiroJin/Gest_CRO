<?php

namespace App\Filament\Resources\AttestationTravailResource\Pages;

use App\Filament\Resources\AttestationTravailResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAttestationTravails extends ManageRecords
{
    protected static string $resource = AttestationTravailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
