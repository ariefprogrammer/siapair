<?php

namespace App\Filament\Resources\TarifProgresifResource\Pages;

use App\Filament\Resources\TarifProgresifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTarifProgresifs extends ListRecords
{
    protected static string $resource = TarifProgresifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
