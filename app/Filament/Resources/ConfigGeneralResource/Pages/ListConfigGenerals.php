<?php

namespace App\Filament\Resources\ConfigGeneralResource\Pages;

use App\Filament\Resources\ConfigGeneralResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfigGenerals extends ListRecords
{
    protected static string $resource = ConfigGeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
