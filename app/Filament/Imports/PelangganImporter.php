<?php

namespace App\Filament\Imports;

use App\Models\Pelanggan;
use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PelangganImporter extends Importer
{
    protected static ?string $model = Pelanggan::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nomor_sambungan')
                ->requiredMapping()
                ->rules(['required', 'max:20']),

            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required', 'max:100']),

            ImportColumn::make('alamat')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('rt')->rules(['nullable', 'max:5']),
            ImportColumn::make('rw')->rules(['nullable', 'max:5']),
            ImportColumn::make('wilayah')->rules(['nullable', 'max:100']),

            ImportColumn::make('tanggal_daftar')
                ->castStateUsing(function (?string $state) {
                if (blank($state)) {
                    return null;
                }

                // Terima format d/m/Y (23/01/2026), juga toleransi d-m-Y
                try {
                    return Carbon::createFromFormat('d/m/Y', trim($state))->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        return Carbon::createFromFormat('d-m-Y', trim($state))->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null; // format tidak dikenali -> biarkan validasi 'date' yang menolak
                    }
                }
            })
            ->rules(['nullable', 'date']),

            ImportColumn::make('status')
                ->rules(['nullable', 'in:aktif,nonaktif']),
        ];
    }

    // Cari record existing berdasarkan nomor_sambungan, kalau belum ada -> buat baru
    public function resolveRecord(): ?Pelanggan
    {
        return Pelanggan::firstOrNew([
            'nomor_sambungan' => $this->data['nomor_sambungan'],
        ]);
    }

    protected function beforeSave(): void
    {
        $this->record->tanggal_daftar ??= now();
        $this->record->status ??= 'aktif';
    }

    // Setelah pelanggan tersimpan, otomatis buatkan akun User dari nomor_sambungan
    protected function afterSave(): void
    {
        if ($this->record->user_id) {
            return; // sudah punya akun, jangan dobel
        }

        $nomor = strtolower($this->record->nomor_sambungan);

        // email: sa-0001@bumka.com (nomor sambungan apa adanya, lowercase)
        $nomorBersih = preg_replace('/[^a-z0-9]/', '', $nomor);
        $email = "{$nomorBersih}@bumka.com";
        $password = "password{$nomorBersih}";

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'      => $this->record->nama,
                'password'  => Hash::make($password),
                'role'      => 'pelanggan',
                'is_active' => true,
            ]
        );

        $this->record->user_id = $user->id;
        $this->record->saveQuietly();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import pelanggan selesai, ' . number_format($import->successful_rows) . ' baris berhasil. Akun login otomatis dibuat untuk setiap pelanggan baru.';

        if ($failed = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failed) . ' baris gagal.';
        }

        return $body;
    }
}