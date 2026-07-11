<?php

namespace App\Filament\Resources\PembayaranTunaiResource\Pages;

use App\Filament\Resources\PembayaranTunaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaranTunai extends CreateRecord
{
    protected static string $resource = PembayaranTunaiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
