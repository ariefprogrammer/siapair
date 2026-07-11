<?php

namespace App\Filament\Resources\OperatorPelangganResource\Pages;

use App\Filament\Resources\OperatorPelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperatorPelanggans extends ListRecords
{
    protected static string $resource = OperatorPelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
