<?php

namespace App\Filament\Resources\PeriodePencatatanResource\Pages;

use App\Filament\Resources\PeriodePencatatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPeriodePencatatans extends ListRecords
{
    protected static string $resource = PeriodePencatatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
