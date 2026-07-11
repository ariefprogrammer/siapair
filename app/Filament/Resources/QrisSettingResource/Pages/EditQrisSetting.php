<?php

namespace App\Filament\Resources\QrisSettingResource\Pages;

use App\Filament\Resources\QrisSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQrisSetting extends EditRecord
{
    protected static string $resource = QrisSettingResource::class;

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
