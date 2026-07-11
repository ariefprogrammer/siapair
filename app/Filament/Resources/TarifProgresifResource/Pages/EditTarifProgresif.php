<?php

namespace App\Filament\Resources\TarifProgresifResource\Pages;

use App\Filament\Resources\TarifProgresifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTarifProgresif extends EditRecord
{
    protected static string $resource = TarifProgresifResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
