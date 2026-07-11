<?php

namespace App\Filament\Resources\QrisSettingResource\Pages;

use App\Filament\Resources\QrisSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQrisSetting extends CreateRecord
{
    protected static string $resource = QrisSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
