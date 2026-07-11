<?php

namespace App\Filament\Resources\OperatorPelangganResource\Pages;

use App\Filament\Resources\OperatorPelangganResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOperatorPelanggan extends CreateRecord
{
    protected static string $resource = OperatorPelangganResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $operatorId = $data['operator_id'];
        $pelangganIds = $data['pelanggan_id']; 
        $assignedAt = $data['assigned_at'] ?? now();

        $lastRecord = null;

        foreach ($pelangganIds as $pelangganId) {
            $lastRecord = static::getModel()::create([
                'operator_id'   => $operatorId,
                'pelanggan_id'  => $pelangganId,
                'assigned_at'   => $assignedAt,
            ]);
        }

        return $lastRecord;
    }
}