<?php

namespace App\Filament\Resources\PeriodePencatatanResource\Pages;

use App\Filament\Resources\PeriodePencatatanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePeriodePencatatan extends CreateRecord
{
    protected static string $resource = PeriodePencatatanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dibuka_oleh'] = auth()->id();
        $data['dibuka_at']   = now();
        $data['status']      = 'tutup';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}