<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use App\Filament\Resources\TagihanResource;
use App\Services\TagihanService;
use Filament\Resources\Pages\EditRecord;

class EditTagihan extends EditRecord
{
    protected static string $resource = TagihanResource::class;

    private ?array $angkaMeterBaru = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $catatan = $this->record->catatanMeter;

        $data['catatanMeter'] = [
            'angka_meter_lalu'     => $catatan?->angka_meter_lalu,
            'angka_meter_sekarang' => $catatan?->angka_meter_sekarang,
            'status_kondisi'       => $catatan?->status_kondisi,
        ];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->angkaMeterBaru = $data['catatanMeter'] ?? null;

        unset($data['catatanMeter']);
        $data['diubah_oleh'] = auth()->id();

        return $data;
    }

    protected function afterSave(): void
    {
        if (! $this->angkaMeterBaru) {
            return;
        }

        app(TagihanService::class)->updateAngkaMeter(
            $this->record,
            (float) $this->angkaMeterBaru['angka_meter_lalu'],
            (float) $this->angkaMeterBaru['angka_meter_sekarang'],
            auth()->id(),
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}