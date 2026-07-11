<?php

namespace App\Filament\Resources\ConfigGeneralResource\Pages;

use App\Filament\Resources\ConfigGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfigGeneral extends EditRecord
{
    protected static string $resource = ConfigGeneralResource::class;

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
