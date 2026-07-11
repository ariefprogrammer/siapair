<?php

namespace App\Filament\Resources\QrisSettingResource\Pages;

use App\Filament\Resources\QrisSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQrisSettings extends ListRecords
{
    protected static string $resource = QrisSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
