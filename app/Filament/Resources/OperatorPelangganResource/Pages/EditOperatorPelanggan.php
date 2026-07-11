<?php

namespace App\Filament\Resources\OperatorPelangganResource\Pages;

use App\Filament\Resources\OperatorPelangganResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOperatorPelanggan extends EditRecord
{
    protected static string $resource = OperatorPelangganResource::class;

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
