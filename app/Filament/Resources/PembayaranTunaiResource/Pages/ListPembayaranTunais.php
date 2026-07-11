<?php

namespace App\Filament\Resources\PembayaranTunaiResource\Pages;

use App\Filament\Resources\PembayaranTunaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembayaranTunais extends ListRecords
{
    protected static string $resource = PembayaranTunaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
