<?php

namespace App\Filament\Resources\ConfigGeneralResource\Pages;

use App\Filament\Resources\ConfigGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateConfigGeneral extends CreateRecord
{
    protected static string $resource = ConfigGeneralResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
