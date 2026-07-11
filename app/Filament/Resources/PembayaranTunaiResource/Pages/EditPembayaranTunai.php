<?php

namespace App\Filament\Resources\PembayaranTunaiResource\Pages;

use App\Filament\Resources\PembayaranTunaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranTunai extends EditRecord
{
    protected static string $resource = PembayaranTunaiResource::class;

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
