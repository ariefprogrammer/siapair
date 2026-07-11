<?php

namespace App\Filament\Resources\PeriodePencatatanResource\Pages;

use App\Filament\Resources\PeriodePencatatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeriodePencatatan extends EditRecord
{
    protected static string $resource = PeriodePencatatanResource::class;

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
