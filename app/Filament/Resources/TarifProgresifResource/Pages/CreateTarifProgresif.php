<?php

namespace App\Filament\Resources\TarifProgresifResource\Pages;

use App\Filament\Resources\TarifProgresifResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTarifProgresif extends CreateRecord
{
    protected static string $resource = TarifProgresifResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
